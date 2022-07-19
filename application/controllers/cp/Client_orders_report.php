<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Client Order Report
 *
 * This is a controller for printing report of Orders per client basis
 *
 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
 * @package Client Order Report
 */
class Client_orders_report extends CI_Controller{

	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;

	function __construct(){

		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');

		$this->load->library('session');
		$this->load->library('utilities');
		$this->load->library('Messages');

		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');

		$is_logged_in = $this->session->userdata('cp_is_logged_in');

		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}

		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');

		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}

	/**
	 * This function is used to fetch and show all saved report on the server for current company
	 */
	function index(){

		ini_set('memory_limit', '-1');

		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		/*=================================*/

		/*
		if( $this->input->post('tomorrow') )
		{
			$date_tomorrow = $this->input->post('date_tomorrow');

			$data['filter_type'] = $this->input->post('invoice_confirmation');
			if($this->input->post('invoice_confirmation') == 'show_invoice'){
				$orderData = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_tomorrow);
			}elseif($this->input->post('invoice_confirmation') == 'show_without_invoice'){
				$orderData = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_tomorrow);
			}else{
				$orderData = $this->Morders->get_orders('','','','','','','',$date_tomorrow);
			}

			foreach($orderData as $key => $val){
				$orderData[$key]->order_details = $this->Morder_details->get_order_details($val->id);
			}
			$data['orderData'] = $orderData;
			$data['action_set'] = 'ordered_on';
			$data['date_set_1'] = $date_tomorrow;
			$data['date_set_2'] = '';
		}
		elseif( $this->input->post('day_after_tomorrow') )
		{
			$date_after_tomorrow = $this->input->post('date_after_tomorrow');

			$data['filter_type'] = $this->input->post('invoice_confirmation');

			if($this->input->post('invoice_confirmation') == 'show_invoice'){
				$orderData = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_after_tomorrow);
			}elseif($this->input->post('invoice_confirmation') == 'show_without_invoice'){
				$orderData = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_tomorrow);
			}else{
				$orderData = $this->Morders->get_orders('','','','','','','',$date_after_tomorrow);
			}

			foreach($orderData as $key => $val){
				$orderData[$key]->order_details = $this->Morder_details->get_order_details($val->id);
			}
			$data['orderData'] = $orderData;
			$data['action_set'] = 'ordered_on';
			$data['date_set_1'] = $date_after_tomorrow;
			$data['date_set_2'] = '';
		}
		elseif( $this->input->post('act') == 'do_filter'){//===============for the searching==========

			$start_date = date('Y-m-d',strtotime($this->input->post('start_date').' 00:00:00') );
			$end_date = date('Y-m-d',strtotime($this->input->post('end_date')) );

			$data['filter_type'] = $this->input->post('show_all_invoice');

			if($this->input->post('show_all_invoice') == 'show_invoice'){
				$orderData = $this->Morders->get_orders('',$start_date,$end_date,'','',array('clients.notifications'=>'subscribe'));
			}elseif($this->input->post('show_all_invoice') == 'show_without_invoice'){
				$orderData = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_tomorrow);
			}else{
				$orderData = $this->Morders->get_orders('',$start_date,$end_date);
			}

			foreach($orderData as $key => $val){
				$orderData[$key]->order_details = $this->Morder_details->get_order_details($val->id);
			}

			/*for($i=0;$i<count($order_details_data);$i++){
			 if($order_details_data[$i]->add_costs != ""){
			$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
			for($j=0;$j<count($rsExtracosts);$j++){
			$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
			}
			}
			$total += $order_details_data[$i]->total;
			}*/
			/*
			$data['orderData'] = $orderData;

			$data['action_set'] = 'ordered_from_to';
			$data['date_set_1'] = $start_date;
			$data['date_set_2'] = $end_date;
			$returned_data=$this->load->view('cp/order_details',$data,true);

		} */

		$data['saved_reports'] = $this->Morders->get_saved_reports_per_client($this->company_id);
		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();// for calender
		$data['content'] = 'cp/client_order_report_view';
		$this->load->view('cp/cp_view',$data);

	}

	function export_seperate_orders( $export_type = "pdf", $filter_type = null, $start_date = null, $end_date = null)
	{
		ini_set('memory_limit', '-1');

		// sleep(20);

		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		/*=================================*/

		$orderData = array();


		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$hide_zero_orders = '';
		if($this->input->post('hide_zero_orders')){
			$hide_zero_orders = $this->input->post('hide_zero_orders');
		}

		$orderData = $this->Morders->get_orders_per_client($this->input->post('start_date'), $this->input->post('end_date'), $hide_zero_orders);


		if($export_type == "pdf"){
			$data['orderData'] = $orderData;
			$date_text = '';
			if($start_date && $end_date){
				$s_week = date("l",strtotime($start_date));
				$s_month = date("F",strtotime($start_date));
				$e_week = date("l",strtotime($end_date));
				$e_month = date("F",strtotime($end_date));
				if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
				{
					if( $s_month == "January" )
						$s_month = 'Januari';
					if( $s_month == "February" )
						$s_month = 'Februari';
					if( $s_month == "March" )
						$s_month = 'Maart';
					if( $s_month == "May" )
						$s_month = 'Mei';
					if( $s_month == "June" )
						$s_month = 'Juni';
					if( $s_month == "July" )
						$s_month = 'Juli';
					if( $s_month == "August" )
						$s_month = 'Augustus';
					if( $s_month == "October" )
						$s_month = 'Oktober';

					if( $s_week == "Monday" )
						$s_week = 'Maandag';
					if( $s_week == "Tuesday" )
						$s_week ='Dinsdag';
					if( $s_week == "Wednesday" )
						$s_week ='Woensdag';
					if( $s_week == "Thursday" )
						$s_week ='Donderdag';
					if( $s_week == "Friday" )
						$s_week ='Vrijdag';
					if( $s_week == "Saturday" )
						$s_week ='Zaterdag';
					if( $s_week == "Sunday" )
						$s_week ='Zondag';

					if( $e_month == "January" )
						$e_month = 'Januari';
					if( $e_month == "February" )
						$e_month = 'Februari';
					if( $e_month == "March" )
						$e_month = 'Maart';
					if( $e_month == "May" )
						$e_month = 'Mei';
					if( $e_month == "June" )
						$e_month = 'Juni';
					if( $e_month == "July" )
						$e_month = 'Juli';
					if( $e_month == "August" )
						$e_month = 'Augustus';
					if( $e_month == "October" )
						$e_month = 'Oktober';

					if( $e_week == "Monday" )
						$e_week = 'Maandag';
					if( $e_week == "Tuesday" )
						$e_week ='Dinsdag';
					if( $e_week == "Wednesday" )
						$e_week ='Woensdag';
					if( $e_week == "Thursday" )
						$e_week ='Donderdag';
					if( $e_week == "Friday" )
						$e_week ='Vrijdag';
					if( $e_week == "Saturday" )
						$e_week ='Zaterdag';
					if( $e_week == "Sunday" )
						$e_week ='Zondag';

				}
				$date_text = _("from").' '.$s_week.' '.date("d",strtotime($start_date)).' '.$s_month.' '._("to").' '.$e_week.' '.date("d",strtotime($end_date)).' '.$e_month;
			}elseif($start_date){
				$s_week = date("l",strtotime($start_date));
				$s_month = date("F",strtotime($start_date));
				if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
				{
					if( $s_month == "January" )
						$s_month = 'Januari';
					if( $s_month == "February" )
						$s_month = 'Februari';
					if( $s_month == "March" )
						$s_month = 'Maart';
					if( $s_month == "May" )
						$s_month = 'Mei';
					if( $s_month == "June" )
						$s_month = 'Juni';
					if( $s_month == "July" )
						$s_month = 'Juli';
					if( $s_month == "August" )
						$s_month = 'Augustus';
					if( $s_month == "October" )
						$s_month = 'Oktober';

					if( $s_week == "Monday" )
						$s_week = 'Maandag';
					if( $s_week == "Tuesday" )
						$s_week ='Dinsdag';
					if( $s_week == "Wednesday" )
						$s_week ='Woensdag';
					if( $s_week == "Thursday" )
						$s_week ='Donderdag';
					if( $s_week == "Friday" )
						$s_week ='Vrijdag';
					if( $s_week == "Saturday" )
						$s_week ='Zaterdag';
					if( $s_week == "Sunday" )
						$s_week ='Zondag';
				}
				$date_text = _("On").' '.$s_week.' '.date("d",strtotime($start_date)).' '.$s_month;
			}
			$data['date_txt'] = $date_text;
			$data['company_name'] = $this->company->company_name;

			$pdf_report = '';
			/*
			if($this->input->post("act") == "do_filter"){
				if ($this->input->post("full"))
					$pdf_report = $this->load->view('cp/pdf_template_full', $data, TRUE);
				elseif ($this->input->post("short"))
					$pdf_report = $this->load->view('cp/pdf_template_short', $data, TRUE);
			}else{
				$pdf_report = $this->load->view('cp/pdf_template_full', $data, TRUE);
			}
			*/
			$pdf_report = $this->load->view('cp/pdf_template_full_per_client', $data, TRUE);

			include(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

			$report_name = 'report_per_client'.time().'.pdf';

			$mpdf=new mPDF('c');

			//$mpdf = new mPDF('utf-8', 'A4');

			//$mpdf=new mPDF();


			$mpdf->AddPage('L', // L - landscape, P - portrait
					'', '', '', '',
					10, // margin_left
					10, // margin right
					5, // margin top
					5, // margin bottom
					5, // margin header
					12);

			//$mpdf->shrink_tables_to_fit = 0;

			/*
			$header_html = '<table width="100%" style="vertical-align: font-weight: bold; top;font-family:Arial;border-bottom: 1px solid #000000;">
								<tr>
									<td width="60%">
										'.$data['company_name'].'
										<br />
										'._("Order").' '.$data['date_txt'].'
									</td>
									<td width="40%" align="center">&nbsp;</td>
								</tr>
							</table>';

			$footer_html = '<htmlpagefooter name="footer">
							<table width="100%" style="font-family:Arial;font-size:10pt;">
								<tr>
									<td align="center"><br/>Page {PAGENO} of {nb}</td>
								</tr>
							</table>
							</htmlpagefooter>';

			$mpdf->SetHTMLHeader($header_html);
			$mpdf->SetHTMLFooter($footer_html);	/* defines footer for Odd and Even Pages - placed at Outer margin */ /*

			*/
			$stylesheet = '.title_head {
								border-bottom: 2px solid #666;
								font-size: 14pt;
								line-height: 24px;
								padding: 10px 0 0;
								margin-bottom: 10px;
								font-family: arial;
							}

							.title_head .top_head {
								padding: 0 10px;
							}

							.bot_head {
								border-bottom: 1px solid #ccc;
								padding: 0 10px 10px;
							}

							.order_table {
								border-bottom: 2px dotted #666;
							    font-family: arial;
							    margin: 0 0 0 0;
							    padding: 20px 10px;
								font-size: 12px;
							}

							.total {
								font-size: 15px;
								text-align: right;
							}

							.c_name {
								font-weight: bold;
							}';

			$mpdf->WriteHTML($stylesheet,1);



			$mpdf->WriteHTML($pdf_report);

			//echo $pdf_report; die;

			if($this->input->post("act") == "do_filter"){
				$mpdf->Output(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name, 'F');

				$size = filesize(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name);
				$size = ($size > (1024*1024) )?round($size/(1024*1024),2)." MB":round($size/1024,2)." KB";

				if($_COOKIE['locale'] == 'nl_NL'){
					$size = strtr ($size, array ('.' => ','));
				}

				$insert_array = array(
					'company_id' => $this->company_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'invoice' => '',
					'type' => '',
					'report_name' => $report_name,
					'size' => filesize(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name),
					'date' => date("Y-m-d H:i:s"),
					'report_type'=>'Order Date'

				);
				if($this->db->insert("saved_reports_per_client",$insert_array)){
					$insert_array['id'] = $this->db->insert_id();
					$insert_array['start_date'] = date("d-m-Y",strtotime($insert_array['start_date']));
					$insert_array['end_date'] = date("d-m-Y",strtotime($insert_array['end_date']));
					$insert_array['date'] = date("d/m/y",strtotime($insert_array['date']));
					$insert_array['f_size'] = $size;
					echo json_encode(array("error" => "0", "data" => $insert_array));
				}else{
					echo json_encode(array("error" => "1", "data" => _("Report cannot be created. Please try again")));
				}
			}else{
				$mpdf->Output($report_name, 'D');
			}
		}elseif($export_type == "excel"){

		}

	}
 //monu-start
	/**
	 * This function is used to export pdf of report per client by search
	 */
	function export_seperate_orders_without_date($export_type = "pdf", $filter_type = null, $start_date = null, $end_date = null)
	{
		ini_set('memory_limit', '-1');



		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		/*=================================*/

		$orderData = array();

		$searchBy =$this->input->post('search_by');
		$searchKeyword=trim($this->input->post('search_keyword'));
		$orderData = $this->Morders->get_orders_per_client_by($searchBy, $searchKeyword);
       /* print_r($orderData);
        die();*/
		if($export_type == "pdf")
		{
			$data['order_client']='';
			$data['order_detail']='';
			if(!empty($orderData))
			{
				$data['order_client'] = $orderData[0]['client_details'][0];
				$data['order_detail'] = $orderData[0]['order_details'];
				foreach($data['order_detail']  as $k => $val)
				{
				  $data['order_detail'][$k]->order_description = $this->Morder_details->get_order_details($val->id);
			    }
			}

			$date_text = '';
			$data['date_txt'] = $date_text;
			$data['company_name'] = $this->company->company_name;
			$pdf_report = '';
			$pdf_report = $this->load->view('cp/pdf_template_full_per_client_without_date', $data, TRUE);
			include(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

			$report_name = 'report_per_client'.time().'.pdf';
			$mpdf=new mPDF('c');
			$mpdf->AddPage('L',
					'', '', '', '',
					10,
					10,
					5,
					5,
					5,
					12
			);

			$stylesheet = '.title_head {
								border-bottom: 2px solid #666;
								font-size: 14pt;
								line-height: 24px;
								padding: 10px 0 0;
								margin-bottom: 10px;
								font-family: arial;
							}

							.title_head .top_head {
								padding: 0 10px;
							}

							.bot_head {
								border-bottom: 1px solid #ccc;
								padding: 0 10px 10px;
							}';
			$stylesheet .= 'span.bold {
								font-weight: bold;
							}
							span.small {
								font-size: 13px;
							}
							span.medium {
								font-size: 14px;
							}
							span.large {
								font-size: 16px;
							    margin-top: 40px;
							}
							span.underline {
								text-decoration: underline;
							}
							#prod_list tr td {
								border-bottom: 2px dotted #ccc;
								border-top: none;
								padding: 5px 10px;
							}';

			$mpdf->WriteHTML($stylesheet,1);



			$mpdf->WriteHTML($pdf_report);
			if($this->input->post("act") == "do_filter")
			{
				$mpdf->Output(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name, 'F');

				$size = filesize(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name);
				$size = ($size > (1024*1024) )?round($size/(1024*1024),2)." MB":round($size/1024,2)." KB";

				if($_COOKIE['locale'] == 'nl_NL')
				{
					$size = strtr ($size, array ('.' => ','));
				}

					$insert_array['id'] = $this->company_id;
					$insert_array['by'] = $searchBy;
					$insert_array['byDetail'] = $searchKeyword;
					$insert_array['date'] = date("d/m/y",strtotime(date('Y-m-d H:i:s')));
					$insert_array['f_size'] = $size;
					$insert_array['report_name']=$report_name;
					echo json_encode(array("error" => "0", "data" => $insert_array));
			}
			else
			{
				$mpdf->Output($report_name, 'D');
			}
		}
		elseif($export_type == "excel")
		{

		}
	}

	/**
	 * This function is used to export pdf of report per client by pickup date
	 */
	function export_seperate_orders_pickup($export_type='pdf', $filter_type = null, $start_date = null, $end_date = null)
	{

		ini_set('memory_limit', '-1');


		/*===========models================ */
		$this->load->model('Morders');
		/*=================================*/

		$orderData = array();


		$start_date = $this->input->post('start_date_pick');
		$end_date = $this->input->post('end_date_pick');
		$hide_zero_orders = '';
		if($this->input->post('hide_zero_orders_pick')){
			$hide_zero_orders = $this->input->post('hide_zero_orders_pick');
		}

		$orderData = $this->Morders->get_orders_per_client_pickup($start_date, $end_date, $hide_zero_orders);
		if($export_type == "pdf")
		{
			$data['orderData'] = $orderData;
			$date_text = '';
			if($start_date && $end_date)
			{
				$s_week = date("l",strtotime($start_date));
				$s_month = date("F",strtotime($start_date));
				$e_week = date("l",strtotime($end_date));
				$e_month = date("F",strtotime($end_date));
				if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
				{
					if( $s_month == "January" )
						$s_month = 'Januari';
					if( $s_month == "February" )
						$s_month = 'Februari';
					if( $s_month == "March" )
						$s_month = 'Maart';
					if( $s_month == "May" )
						$s_month = 'Mei';
					if( $s_month == "June" )
						$s_month = 'Juni';
					if( $s_month == "July" )
						$s_month = 'Juli';
					if( $s_month == "August" )
						$s_month = 'Augustus';
					if( $s_month == "October" )
						$s_month = 'Oktober';

					if( $s_week == "Monday" )
						$s_week = 'Maandag';
					if( $s_week == "Tuesday" )
						$s_week ='Dinsdag';
					if( $s_week == "Wednesday" )
						$s_week ='Woensdag';
					if( $s_week == "Thursday" )
						$s_week ='Donderdag';
					if( $s_week == "Friday" )
						$s_week ='Vrijdag';
					if( $s_week == "Saturday" )
						$s_week ='Zaterdag';
					if( $s_week == "Sunday" )
						$s_week ='Zondag';

					if( $e_month == "January" )
						$e_month = 'Januari';
					if( $e_month == "February" )
						$e_month = 'Februari';
					if( $e_month == "March" )
						$e_month = 'Maart';
					if( $e_month == "May" )
						$e_month = 'Mei';
					if( $e_month == "June" )
						$e_month = 'Juni';
					if( $e_month == "July" )
						$e_month = 'Juli';
					if( $e_month == "August" )
						$e_month = 'Augustus';
					if( $e_month == "October" )
						$e_month = 'Oktober';

					if( $e_week == "Monday" )
						$e_week = 'Maandag';
					if( $e_week == "Tuesday" )
						$e_week ='Dinsdag';
					if( $e_week == "Wednesday" )
						$e_week ='Woensdag';
					if( $e_week == "Thursday" )
						$e_week ='Donderdag';
					if( $e_week == "Friday" )
						$e_week ='Vrijdag';
					if( $e_week == "Saturday" )
						$e_week ='Zaterdag';
					if( $e_week == "Sunday" )
						$e_week ='Zondag';

				}
				$date_text = _("from").' '.$s_week.' '.date("d",strtotime($start_date)).' '.$s_month.' '._("to").' '.$e_week.' '.date("d",strtotime($end_date)).' '.$e_month;
			}
			elseif($start_date)
			{
				$s_week = date("l",strtotime($start_date));
				$s_month = date("F",strtotime($start_date));
				if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
				{
					if( $s_month == "January" )
						$s_month = 'Januari';
					if( $s_month == "February" )
						$s_month = 'Februari';
					if( $s_month == "March" )
						$s_month = 'Maart';
					if( $s_month == "May" )
						$s_month = 'Mei';
					if( $s_month == "June" )
						$s_month = 'Juni';
					if( $s_month == "July" )
						$s_month = 'Juli';
					if( $s_month == "August" )
						$s_month = 'Augustus';
					if( $s_month == "October" )
						$s_month = 'Oktober';

					if( $s_week == "Monday" )
						$s_week = 'Maandag';
					if( $s_week == "Tuesday" )
						$s_week ='Dinsdag';
					if( $s_week == "Wednesday" )
						$s_week ='Woensdag';
					if( $s_week == "Thursday" )
						$s_week ='Donderdag';
					if( $s_week == "Friday" )
						$s_week ='Vrijdag';
					if( $s_week == "Saturday" )
						$s_week ='Zaterdag';
					if( $s_week == "Sunday" )
						$s_week ='Zondag';
				}
				$date_text = _("On").' '.$s_week.' '.date("d",strtotime($start_date)).' '.$s_month;
			}
			$data['date_txt'] = $date_text;
			$data['company_name'] = $this->company->company_name;

			$pdf_report = '';
			$pdf_report = $this->load->view('cp/pdf_template_full_per_client', $data, TRUE);
			include(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');
			$report_name = 'report_per_client'.time().'.pdf';
			$mpdf=new mPDF('c');
			$mpdf->AddPage('L', // L - landscape, P - portrait
					'', '', '', '',
					10, // margin_left
					10, // margin right
					5, // margin top
					5, // margin bottom
					5, // margin header
					12);
			$stylesheet = '.title_head {
								border-bottom: 2px solid #666;
								font-size: 14pt;
								line-height: 24px;
								padding: 10px 0 0;
								margin-bottom: 10px;
								font-family: arial;
							}

							.title_head .top_head {
								padding: 0 10px;
							}

							.bot_head {
								border-bottom: 1px solid #ccc;
								padding: 0 10px 10px;
							}

							.order_table {
								border-bottom: 2px dotted #666;
							    font-family: arial;
							    margin: 0 0 0 0;
							    padding: 20px 10px;
								font-size: 12px;
							}

							.total {
								font-size: 15px;
								text-align: right;
							}

							.c_name {
								font-weight: bold;
							}';

			$mpdf->WriteHTML($stylesheet,1);



			$mpdf->WriteHTML($pdf_report);
			if($this->input->post("act") == "do_filter")
			{
				$mpdf->Output(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name, 'F');

				$size = filesize(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name);
				$size = ($size > (1024*1024) )?round($size/(1024*1024),2)." MB":round($size/1024,2)." KB";

				if($_COOKIE['locale'] == 'nl_NL')
				{
					$size = strtr ($size, array ('.' => ','));
				}

				$insert_array = array(
					'company_id' => $this->company_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'invoice' => '',
					'type' => '',
					'report_name' => $report_name,
					'size' => filesize(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_name),
					'date' => date("Y-m-d H:i:s"),
					'report_type'=>'Pickup Date'

				);
				if($this->db->insert("saved_reports_per_client",$insert_array))
				{
					$insert_array['id'] = $this->db->insert_id();
					$insert_array['start_date'] = date("d-m-Y",strtotime($insert_array['start_date']));
					$insert_array['end_date'] = date("d-m-Y",strtotime($insert_array['end_date']));
					$insert_array['date'] = date("d/m/y",strtotime($insert_array['date']));
					$insert_array['f_size'] = $size;
					echo json_encode(array("error" => "0", "data" => $insert_array));
				}
				else
				{
					echo json_encode(array("error" => "1", "data" => _("Report cannot be created. Please try again")));
				}
			}
			else
			{
				echo "!";
				die();
				$mpdf->Output($report_name, 'D');
			}
		}
		elseif($export_type == "excel")
		{

		}

	}

	/**
	 * This function is used to delete particular report
	 */
	function delete($id = null){
		$response = array('error' => 1, 'message' => _("Cannot find Id of report. Please reload page and then try again.") );
		if($id){
			$report_info = $this->db->get_where("saved_reports_per_client", array("id" => $id))->result_array();
			if(!empty($report_info)){
				if( @unlink(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_info['0']['report_name']) ){
					if($this->db->delete("saved_reports_per_client" , array("id" => $id))){
						$response = array('error' => 0, 'message' => _("Report deleted successfully.") );
					}else{
						$response = array('error' => 1, 'message' => _("Report cannot deleted successfully") );
					}
				}else{
					$response = array('error' => 1, 'message' => _("Report cannot deleted successfully") );
				}
			}else{
				$response = array('error' => 1, 'message' => _("Report cannot be detected") );
			}
		}
		echo json_encode($response);
	}

	/**
	 * This function is similar to above function except that it will delete multiple reports at a time
	 */
	function delete_all(){
		$response = array("error" => 1 , "message" => _("No report found. Please select any report then delete it again."));
		$reoprt_ids = $this->input->post("ids");

		$result = '';
		if(!empty($reoprt_ids)){
			$this->db->where_in("id",$reoprt_ids);

			$report_infos = $this->db->get("saved_reports_per_client")->result_array();

			if(!empty($report_infos)){
				foreach ($report_infos as $report_info){
					if( @unlink(dirname(__FILE__)."/../../../assets/pdf_reports/".$report_info['report_name']) ){
						if(!$this->db->delete("saved_reports_per_client" , array("id" => $report_info['id']))){
							$result .= $report_info['report_name']." ";
						}
					}else{
						$result .= $report_info['report_name']." ";
					}
				}

				if($result == ''){
					$response = array("error" => 0 , "message" => _("Selected Reports are deleted successfully"));
				}else{
					$response = array("error" => 1 , "message" => _("Selected Reports cannot be deleted"));
				}
			}else{
				$response = array("error" => 1 , "message" => _("No data found for selected Report(s). Please select any report then delete it again."));
			}
		}
		echo json_encode($response);
	}
}
?>