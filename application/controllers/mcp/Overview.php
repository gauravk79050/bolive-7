<?php
	class Overview extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			ini_set('max_execution_time', '0');
			ini_set('memory_limit', '-1');
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
			
			//$this->load->library('form_validation'); 
			$this->tempUrl = base_url().'application/views/mcp';
			$this->template = "/mcp";
			$this->load->model('mcp/MOverview');
			$this->fdb = $this->load->database('fdb', true);
			$this->temp="/mcp/overview";
			
			$current_user = $this->session->userdata('username');
			$is_logged_in = $this->session->userdata('is_logged_in');
			
			if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index() {
			$where_array = array(
								'approved'=>'1'								
							);
			$data['company_count'] = $this->MOverview->get_company_count($where_array);
			$param = array();
			$data['account_types'] = $this->MOverview->get_account_types();
			
			$data['tempUrl']=$this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/overview';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');  
		}	
		
		function ajax_companies(){
			
			mb_internal_encoding('UTF-8');
			$sql_details['charset']  = 'utf8';
			$table = 'company';
			$primaryKey = 'id';

			$columns = array(
					array( 'db' => 'company.id', 'dt' => 0, 'field'=>'id'),
					array( 'db' => 'company_name', 'dt' => 1,'field'=>'company_name' ),
					array( 'db' => 'company_type_name',  'dt' => 2,'field'=>'company_type_name' ),
					array( 'db' => 'city',     'dt' => 3,'field'=>'city' ),
					array( 'db' => 'phone',  'dt' => 4,'field'=>'phone' ),
					array( 'db' => 'email',  'dt' => 5,'field'=>'email' ),
			);
			$sql_details = array(
					'user' => $this->db->username,
					'pass' => $this->db->password,
					'db'   => $this->db->database,
					'host' => $this->db->hostname
			);
			require( 'Ssp.customized.class.php' );

			$_GET['search']['value'] = addslashes(addslashes($_GET['search']['value']));
			$where = " `company`.`approved` = '1' ";

			$joinQuery = "FROM `company` JOIN `company_type` ON `company`.`type_id` = `company_type`.`id` ";
			$data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where );
			foreach ( $data[ 'data' ] as $key => $value ) {
				$this->db->select( 'id, direct_kcp' );
				$this->db->where( 'company_id', $value[0] );
				$prod_ids = $this->db->get('products')->result();
				$total_products = sizeof( $prod_ids );
				$fixed_products = 0;
				foreach ( $prod_ids as $k => $qr ){
					$complete = 1;
					if( $qr->direct_kcp == 1 ){
						$this->db->where( array( 'obs_pro_id' => $qr->id,'is_obs_product' => 0 ) );
						$result = $this->db->get('fdd_pro_quantity')->result_array();
						if(empty($result)){
							$complete = 0;
						}
					}
					else{
						$this->db->where(array( 'obs_pro_id' => $qr->id ) );
						$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
						if(!empty($result_custom)){
							foreach ($result_custom as $val){
								if( $val['is_obs_product'] == 1 ){
									$complete = 0;
									break;
								} else {
									$this->fdb->where( 'p_id', $val[ 'fdd_pro_id' ] );
									$this->fdb->where( 'approval_status', 1 );
									$count = $this->fdb->count_all_results( 'products' );
									if( $count == 0 ) {
										$complete = 0;
										break;
									}
								}
							}
						}
						else{
							$complete = 0;
						}
					}

					if($complete == 1){
						$fixed_products++;
					}
						
				}
				if( $total_products != 0 && $fixed_products != 0 ) {
					$complete_per = ( $fixed_products / $total_products ) * 100;
					if( is_float( $complete_per ) ) {
						$complete_per = round( $complete_per, 2 );
					}
				} else {
					$complete_per = 0;
				}
				$data['data'][$key][6] = $fixed_products.' / '.$total_products;
				$data['data'][$key][7] = $complete_per.'%';
			}
			echo json_encode($data);
		}
	   	
	   	function download_client_overview()
	   	{
			$datestamp = date("d-m-Y");
			$filename = "Company-Overview-".$datestamp.".xls";

			$result = $this->MOverview->get_company( array('approved'=>1,'status'=>'1','flag'=>"1"),array('id'=>'ASC'),null,0,0 );
			
			
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Company Overview') );
			
			$counter = 1;
			//$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Sr. No') 			)->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Company id') 		)->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Company Name') 		)->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Company Type') 	)->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('City') 			)->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('Phone') 			)->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('Email') 			)->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Recipes / Products') 		)->getStyle('G'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('Completed %') 			)->getStyle('H'.$counter)->getFont()->setBold(true);
			
			
			if(!empty($result))
			{
				foreach($result as $result_row)
				{
					$counter++;
					if( $result_row['total_products'] != 0 && $result_row['recipes'] != 0 ) {
						$complete_per = ( $result_row['recipes'] / $result_row['total_products'] ) * 100;
						if( is_float( $complete_per ) ) {
							$complete_per = round( $complete_per, 2 );
						}
					} else {
						$complete_per = 0;
					}
					$this->excel->getActiveSheet()->setCellValue('A'.$counter, $result_row['id'] );
					$this->excel->getActiveSheet()->setCellValue('B'.$counter, $result_row['company_name'] );
					$this->excel->getActiveSheet()->setCellValue('C'.$counter, $result_row['company_type_name'] );
					$this->excel->getActiveSheet()->setCellValue('D'.$counter, $result_row['city'] );
					$this->excel->getActiveSheet()->setCellValue('E'.$counter, $result_row['phone'] );
					$this->excel->getActiveSheet()->setCellValue('F'.$counter, $result_row['email'] );
					$this->excel->getActiveSheet()->setCellValue('G'.$counter, $result_row['recipes'].' / '.$result_row['total_products'] );
					$this->excel->getActiveSheet()->setCellValue('H'.$counter, $complete_per.'%' );
				}
			}
			
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
			
		}

		function recipe_entered_statistics()
	   	{
			$datestamp = date("d-m-Y");
			$filename = "Company-Overview-".$datestamp.".xls";

			$result = $this->MOverview->get_company( array('approved'=>1,'status'=>'1','flag'=>"1"),array('id'=>'ASC'),null,0,0 );
			
			
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Recipe entered statistics') );
			
			$counter = 1;
			//$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Sr. No') 			)->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Id') 		)->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Shop Name') 		)->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Total Recipes') 	)->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Recipes Fixed') 	)->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('PCT') 	)->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('Type') 	)->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Total amount of used products') 	)->getStyle('G'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('Amount of products without sheet') 	)->getStyle('H'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('Amount of refused products') 	)->getStyle('I'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('Amount of products in treatment') 	)->getStyle('J'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K'.$counter, _('Register Date') 	)->getStyle('K'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('L'.$counter, _('Phone') 			)->getStyle('L'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('M'.$counter, _('City') 			)->getStyle('M'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('N'.$counter, _('Contact Person') 			)->getStyle('N'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('O'.$counter, _('Email') 			)->getStyle('O'.$counter)->getFont()->setBold(true);
			
			if(!empty($result))
			{
				foreach($result as $result_row)
				{
					$counter++;
					if( $result_row['total_products'] != 0 && $result_row['recipes'] != 0 ) {
						$complete_per = ( $result_row['recipes'] / $result_row['total_products'] ) * 100;
						if( is_float( $complete_per ) ) {
							$complete_per = round( $complete_per, 2 );
						}
					} else {
						$complete_per = 0;
					}
					$this->excel->getActiveSheet()->setCellValue('A'.$counter, $result_row['id'] );
					$this->excel->getActiveSheet()->setCellValue('B'.$counter, $result_row['company_name'] );
					$this->excel->getActiveSheet()->setCellValue('C'.$counter, $result_row['total_products'] );
					$this->excel->getActiveSheet()->setCellValue('D'.$counter, $result_row['recipes']);
					$this->excel->getActiveSheet()->setCellValue('E'.$counter, $complete_per);
					$this->excel->getActiveSheet()->setCellValue('F'.$counter, $result_row['ac_title']);
					$this->excel->getActiveSheet()->setCellValue('G'.$counter, $result_row['total_used_product']);
					$this->excel->getActiveSheet()->setCellValue('H'.$counter, $result_row['total_pws']);
					$this->excel->getActiveSheet()->setCellValue('I'.$counter, $result_row['total_refused_product']);
					$this->excel->getActiveSheet()->setCellValue('J'.$counter, $result_row['treatment']);
					$this->excel->getActiveSheet()->setCellValue('K'.$counter, $result_row['registration_date']);
					$this->excel->getActiveSheet()->setCellValue('L'.$counter, $result_row['phone'] );
					$this->excel->getActiveSheet()->setCellValue('M'.$counter, $result_row['city'] );
					$this->excel->getActiveSheet()->setCellValue('N'.$counter, $result_row['first_name'].' '.$result_row['last_name']  );
					$this->excel->getActiveSheet()->setCellValue('O'.$counter, $result_row['email']  );
				}
			}

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(28);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(32);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(28);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);


			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		}	
	}	 	
?>