<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 *  ======================================= 
 *  Author     : Keerti Rastogi
 *  License    : Protected 
 *  Email      : keertirastogi@cedcoss.com
 *  ======================================= 
 */  
require_once APPPATH."/third_party/ExcelReader/reader.php"; 
 
class Excel_Reader extends Spreadsheet_Excel_Reader
{ 
    public function __construct()
	{ 
        parent::__construct(); 
    }
	
	function read_excel( $file_path = NULL )
	{
		if( $file_path == '' )
		  return false;
		
		ini_set('memory_limit', '512M');
		  
		//$data = new Spreadsheet_Excel_Reader();
		$this->setOutputEncoding('CP1251');
		$this->read( $file_path );
		
		$arr = array();
		
		for ($i = 1; $i <= $this->sheets[0]['numRows']; $i++) {
			for ($j = 1; $j <= $this->sheets[0]['numCols']; $j++) {
				//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";								
				$arr[$i][$j] = $this->sheets[0]['cells'][$i][$j];
			}
			//echo "\n";						
		}
		
		return $arr;
	}
}

?>