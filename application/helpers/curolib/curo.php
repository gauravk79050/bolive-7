<?php
/**
 * CURO Payments
 *
 * @author CURO Payments
 * @package clientlib class
 * @version 1.0 - (C)2013 by DBS for CURO
 *
 * == BEGIN LICENSE ==
 *
 * THIS SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT OF THIRD PARTY RIGHTS.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * == END LICENSE ==
 */

class CURO {

	protected $_mMerchantId		= '';
	protected $_sMerchantSecret	= '';

	protected $_bTestMode		= FALSE;

	protected $_sErrorStr		= '';
	protected $_iErrorNo		= 0;

	const API_URL_LIVE = 'secure.curopayments.net';
	const API_PORT_LIVE = 443;
	const API_URL_TEST	= 'secure-staging.curopayments.net';
	const API_PORT_TEST = 443;

    protected $_sApiHost;
	protected $_iApiPort;
	protected $_iApiMethod		= 'curl';

    protected $_sLastRequest    = '';
    protected $_sLastResult     = '';
    
    /**
     * Class Constructor
     * @param String $sConfigFile_ Optionally you may provide the path to the config file. If you omit it, the
     * default config file located in the SDK folder will be used.
     */
	public function __construct( $sConfigFile_ = '' ) {
		if ( empty( $sConfigFile_ ) ) {
			$sConfigFile_ = dirname( __FILE__ ) . '/curo.config.php';
		}
		require( $sConfigFile_ );

        // $this->_mMerchantId = $aConfig['merchant_id'];
        // $this->_sMerchantSecret = $aConfig['merchant_secret'];
        
		$this->_mMerchantId = $aConfig['merchant_name'];
		$this->_sMerchantSecret = $aConfig['merchant_api_key'];
		
        if( $this->_bTestMode = (bool) $aConfig['testmode'] ) {
            $this->_sApiHost = self::API_URL_TEST;
            $this->_iApiPort = self::API_PORT_TEST;
        } else {
            $this->_sApiHost = self::API_URL_LIVE;
            $this->_iApiPort = self::API_PORT_LIVE;            
        }
	}

    /**
     * Execute API function
     * @param string $sService_ Service module to call
     * @param string $sAction_ Action with the module
     * @param array $aData_ Arguments
     * @return mixed FALSE or Array with result 
     */
	public function execute( $sService_, $sAction_, $aData_ = array() ) {
		$aParams = array(
			'action' => $sAction_,
			'testmode' => $this->_bTestMode ? '1' : '0',
			'merchant_id' => $this->_mMerchantId,
			'hash' => $this->_sMerchantSecret,
			'response' => 'serialize'
		);
		// Separate files to calculate the hash
		$aFiles = array();
		foreach( $aData_ as $k => $v ) {
			if( $v[0] == '@' ) {
				$aFiles[$k] = $v;
			} else {
				$aParams[$k] = $v;
			}
		}
		ksort( $aParams, SORT_STRING );
		$sHash = md5( implode( '|', array_values( $aParams ) ) );
		$aParams['hash'] = $sHash;
		return $this->_request( "/v1/api/{$sService_}/", array_merge( $aFiles, $aParams ) );
	}

    /**
     * Internal request preparation method
     * @param string $sPath_ URI
     * @param array $sData_ POST data
     * @return mixed
     */
	protected function _request( $sPath_, $aData_ ) {
		if ( function_exists( 'curl_init' ) ) {
	        $this->_sLastRequest = print_r( $aData_, 1 ); // *DEBUG*
			return $this->_requestUsingCurl( $this->_sApiHost, $this->_iApiPort, $sPath_, $aData_ );
		} else {
	        $this->_sLastRequest = http_build_query( $aData_, '', '&' ); // *DEBUG*
			return $this->_requestUsingFsock( $this->_sApiHost, $this->_iApiPort, $sPath_, $this->_sLastRequest );
		}
	}

    /**
     * Send HTTP request using CURL
     * @param string $sHost_
     * @param integer $iPort_
     * @param string $sPath_
     * @param string $sData_
     * @return mixed
     */
	protected function _requestUsingCurl( $sHost_, $iPort_, $sPath_, $aData_ ) {
		$this->_iApiMethod = 'curl';
		$sHost_ = ( $iPort_ == 443 ? 'https://' : 'http://' ) . $sHost_;
		$rCh = curl_init();
		curl_setopt( $rCh, CURLOPT_URL, $sHost_ . $sPath_ );
		curl_setopt( $rCh, CURLOPT_PORT, $iPort_ );
		curl_setopt( $rCh, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $rCh, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $rCh, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $rCh, CURLOPT_TIMEOUT, 60 );
		curl_setopt( $rCh, CURLOPT_HEADER, FALSE );
		curl_setopt( $rCh, CURLOPT_POST, TRUE );
		curl_setopt( $rCh, CURLOPT_POSTFIELDS, $aData_ );
		$sResults = curl_exec( $rCh );
		curl_close( $rCh );
		$mData = @unserialize( $sResults );
		if ( is_array( $mData ) ) {
			if (
				! isset( $mData['success'] ) ||
				$mData['success'] == FALSE
			) {
				$this->_sErrorStr = isset( $mData['message'] ) ? $mData['message'] : 'unknown error';
				$this->_iErrorNo = isset( $mData['code'] ) ? $mData['code'] : 0;
				return FALSE;
			} else {
				return $mData;
			}
		} else {
			$this->_sErrorStr = 'unrecognized reply: ' . trim( $sResults );
			$this->_iErrorNo = 0;
			return FALSE;
		}
	}

    /**
     * Send HTTP/1.0 request using socket connection
     * @param string $sHost_
     * @param integer $iPort_
     * @param string $sPath_
     * @param string $sData_
     * @return mixed
     */
	protected function _requestUsingFsock( $sHost_, $iPort_, $sPath_, $sData_ ) {
		$this->_iApiMethod = 'fsock';
		if ( FALSE == ( $rFp = @fsockopen( $sHost_, $iPort_, $iErrorNo, $sErrorStr ) ) ) {
			$this->_sErrorStr = 'unable to connect to server: ' . $sErrorStr;
			$this->_iErrorNo = 0;
			return FALSE;
		}
		@fputs( $rFp, "POST {$sPath_} HTTP/1.0\n" );
		@fputs( $rFp, "Host: {$sHost_}\n" );
		@fputs( $rFp, "Content-type: application/x-www-form-urlencoded\n" );
		@fputs( $rFp, "Content-length: " . strlen( $sData_ ) . "\n" );
		@fputs( $rFp, "Connection: close\n\n" );
		@fputs( $rFp, $sData_ );

		$sBuffer = '';
		while ( ! feof( $rFp ) ) {
			$sBuffer .= fgets( $rFp, 128 );
		}
		fclose( $rFp );
		return $this->_processRawResults( $sBuffer );
	}

    /**
     * Process HTTP results
     * @param string $sResults_
     * @return mixed
     */
	protected function _processRawResults( $sResults_ ) {
        $this->_sLastResult = $sResults_;
		list( $sHeaders, $sBody ) = preg_split( "/(\r?\n){2}/", $sResults_, 2 );
		if ( (integer) strpos( $sHeaders, '200 OK' ) > 0 ) {
			if ( ! empty( $sBody ) ) {
				$mData = @unserialize( $sBody );
				if ( is_array( $mData ) ) {
					if (
						! isset( $mData['success'] ) ||
						$mData['success'] == FALSE
					) {
						$this->_sErrorStr = isset( $mData['message'] ) ? $mData['message'] : 'unknown error';
						$this->_iErrorNo = isset( $mData['code'] ) ? $mData['code'] : 0;
						return FALSE;
					} else {
						return $mData;
					}
				} else {
					$this->_sErrorStr = 'unrecognized reply: ' . trim( $sBody );
					$this->_iErrorNo = 0;
					return FALSE;
				}
			} else {
				$this->_sErrorStr = 'zero-sized reply';
				$this->_iErrorNo = 0;
				return FALSE;
			}
		} else {
			$this->_sErrorStr = 'invalid service';
			$this->_iErrorNo = 0;
			return FALSE;
		}
	}

    /**
     * Setters for protected members
     */
	public function setApiHost( $sApiHost_ ) {
		$this->_sApiHost = $sApiHost_;
	}

	public function setApiPort( $iApiPort_ ) {
		$this->_iApiPort = $iApiPort_;
	}

	public function setMerchantId( $mMerchantId_ ) {
		$this->_mMerchantId = $mMerchantId_;
	}

	public function setMerchantSecret( $sMerchantSecret_ ) {
		$this->_sMerchantSecret = $sMerchantSecret_;
	}

	public function setTestMode( $bTestMode_ = TRUE ) {
		$this->_bTestMode = $bTestMode_;
	}
	
    /**
     * Getters for error members
     */
	public function getError() {
		return $this->_sErrorStr;
	}

	public function getErrorNo() {
		return $this->_iErrorNo;
	}
	
}