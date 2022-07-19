<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report_exp extends CI_Controller{

	function __construct(){

		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->model('Mgeneral_settings');
		$this->load->model('M_fooddesk');
		$this->company_id = $this->session->userdata('cp_user_id');

		$this->load->model('Mcompany');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		$this->lang_u = get_lang( $_COOKIE['locale'] );
		$this->company = array();
		$company =  $this->Mcompany->get_company();
    $this->fdb = $this->load->database('fdb',TRUE);
		if( !empty($company) )
			$this->company = $company[0];
	}

	function index(){

	}

	function digi_export($company_id = 0){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$new_ing_name = array();

		$company_id = $this->company_id;
		
		$this->db->select("id, pro_art_num, direct_kcp, recipe_weight,sell_product_option");
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$products = $this->db->get_where('products',array('company_id'=>$company_id))->result_array();

		$add_name = 'all_name'.$this->lang_u;
		
		$this->fdb->select(array('all_id',$add_name));
		$aller_arr = $this->fdb->get('allergence')->result_array();

		

		$allergens_array = array(
				"Glutenbevattende granen"  => "Gluten",
				"schaaldieren" => "Schaaldieren",
				"eieren" => "Ei",
				"vis" => "Vis",
				"Aardnoten (pinda''s)" => "Aardnoten",
				"soja" => "Soja",
				"melk" => "Melk",
				"noten" => "Noten",
				"selderij" => "Selderij",
				"mosterd" => "Mosterd",
				"sesamzaad" => "Sesamzad",
				"Zwaveldioxide en sulfieten" => "Sulfiet",
				"lupine" => "Lupine",
				"weekdieren" => "Weekdieren",
		);

		$sub_allergence_array = array(
				"amandelen" => "amandelen",
				"hazelnoten" => "hazelnoten",
				"walnoten" => "walnoten",
				"cashewnoten" => "cashewnoten",
				"pecannoten" => "pecannoten",
				"paranoten" => "paranoten",
				"pistachenoten" => "pistachenoten",
				"macadamia" => "macadamia",
				"queenslandnoten" => "queenslandnoten",
				"tarwe" => "tarwe",
				"rogge" => "rogge",
				"gerst" => "gerst",
				"haver" => "haver",
				"spelt" => "spelt",
				"kamut" => "kamut"
		);

		$data = '';
		$data_n = '';
		$c = 1;
		$this->load->model('mproducts');

		foreach($products as $key => $value){
			$a = '';

			$complete = 1;
			if($value['direct_kcp'] == 1){
				$this->db->where(array('obs_pro_id'=>$value['id'],'is_obs_product'=>0));
				$result = $this->db->get('fdd_pro_quantity')->result_array();
				if(empty($result)){
					$complete = 0;
				}
			}
			else{
				$this->db->where(array('obs_pro_id'=>$value['id']));
				$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
				if(!empty($result_custom)){
					foreach ($result_custom as $val){
						if($val['is_obs_product'] == 1){
							$complete = 0;
							break;
						}
					}
				}
				else{
					$complete = 0;
				}
			}

			if($complete == 0){
				continue;
			}

			$product_ingredients = $this->mproducts->get_product_ingredients_dist($value['id'],1);
			$fixed = $this->mproducts->fixed($value['id']);
			$allergens = $this->mproducts->get_product_allergence_dist($value['id'],1);
			$sub_allergens = $this->mproducts->get_product_sub_allergence_dist($value['id'],1);

			if(empty($fixed))
			{
				$product_allergences=$allergens;
				if (!empty($product_allergences))
				{
					foreach ($product_allergences as $allergences_key=>$allergences_val)
					{
						$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
						$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
					}
				}
				$product_sub_allergences=$this->mproducts->get_product_sub_allergence_dist($value['id'],$this->company->k_assoc);
				if (!empty($product_sub_allergences))
				{
					foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
					{
						$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
						$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
					}
				}
				$all_aller = $this->mproducts->get_ing_allergen($value['id'],$this->lang_u);
				foreach ($all_aller as $all_aller_k => $all_aller_v ) {
					$allergence_lang_d = 'allergence';
					$sub_allergence_d = 'sub_allergence';
		 			if ($this->lang_u == '_dch') {
		 				$allergence_lang_d = 'allergence_dch';
		 				$sub_allergence_d = 'sub_allergence_dch';

		 			}else if ($this->lang_u == '_fr') {
		 				$allergence_lang_d = 'allergence_fr';
		 				$sub_allergence_d = 'sub_allergence_fr';
		 			}

		 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
		 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
		 			}
		 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
		 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
		 			}
	 			}
				$all_allergence = array_unique($all_allergence);

				$final_all = array();
				foreach ($all_allergence as $key1 => $value1 ) {
					$ing_aller = explode('-', $value1);
					$final_all = array_merge($ing_aller, $final_all);
				}
				$final_all = array_unique($final_all);
				$final_aller = $this->mproducts->get_allergen($final_all,$this->lang_u);

				if (empty($final_aller)) {
					$allergens = array_values(array_unique($product_allergence, SORT_REGULAR));
				}
				elseif (empty($product_allergence))
				{
					$allergens = array_values(array_unique($final_aller, SORT_REGULAR));
				}
				else{
					$allergens = array_merge($product_allergence,$final_aller);
					$allergens = array_values(array_unique($allergens, SORT_REGULAR));
				}

				$final_sub_all = array();
				foreach ($all_sub_allergence as $key1 => $value1 ) {
					$ing_sub_aller = explode('-', $value1);
					$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
				}
				$final_sub_all = array_unique($final_sub_all);
				$final_sub_aller = $this->mproducts->get_sub_allergen($final_sub_all,$this->lang_u);

				if (empty($final_sub_aller)) {
					$sub_allergens = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
				}
				elseif (empty($product_sub_allergence))
				{
					$sub_allergens = array_values(array_unique($final_sub_aller, SORT_REGULAR));
				}
				else{
					$sub_allergens = array_merge($product_sub_allergence,$final_sub_aller);
					$sub_allergens = array_values(array_unique($sub_allergens, SORT_REGULAR));
				}
			}


			$all_id_arr = array();
			if(!empty($allergens)){
				foreach ($allergens as $aller){
					$all_id_arr[] = $aller->ka_id;
				}
			}

			$allergence_words = array();
			foreach ($aller_arr as $val){
				if(in_array($val['all_id'],$all_id_arr))
					$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
			}

			$single_ing = 0;
			if(empty($fixed))
			{
				$aller_type 	= 'aller_type'.$this->lang_u ;
			  	$allergence 	= 'allergence'.$this->lang_u ;
			  	$sub_allergence = 'sub_allergence'.$this->lang_u ;
				$ing = '';
				$count = 0;
				foreach ($product_ingredients as $key => $ingredients){
					if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
						$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
						$to_remove = $len+2;
						$ing = substr($ing, 0 , -$to_remove);
						$ing .= '';
						if( empty($fixed) ){
							$ing .= get_the_allergence($ingredients->ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence);
						}else{
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
						}
						$single_ing = 0;
					}
					else{
						if( $ingredients->ki_id == 0 ){
							if( $ingredients->ki_name == '(' ){
								if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
									$ing .= ',';
								}
								else{
									$ing .= ' (';
								}
							}
							elseif( $ingredients->ki_name == ')' ){
								$ing  = rtrim($ing, ' ');
								$ing  = rtrim($ing, ',');
								if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){
									$ing .= ', ';
								}
								else{
									$ing .= '),';
								}
								array_push( $new_ing_name, $ing );
							}
							else{
								$ing .= $ingredients->ki_name;
							}
						}

						if($ingredients->ki_id != 0){
							$count++;
							if($ingredients->prefix == ''){
								$flag = true;
								if($ingredients->ki_name == '('){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= ' (';
								}
								elseif( $ingredients->ki_name == ')' ){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= '), ';
								}
								elseif($ingredients->ki_name == ':'){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= ': ';
								}else{
									if( empty($fixed) ){
										$ing .= get_the_allergence($ingredients->ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence);
									}else{
										$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
									}
								}
							}
						}
					}
				}

				if( $count == 1 ){
					$ing = rtrim($ing, ' ');
					$ing = rtrim($ing, ',');
				}
				elseif( $count == 0 ){
					$ing = str_replace(' (),', '', $ing);
					$ing = rtrim($ing, ' ');
					$ing = rtrim($ing, ',');
				}
				else{
					$ing = rtrim($ing, ' ');
					$ing = rtrim($ing, ',');
				}
				if( sizeof( $new_ing_name ) == 1  ) {
					if( strpos( $new_ing_name[0], ')' ) !== false ) {
						$str_pos = stripos( $new_ing_name[0], '(' );
						if( $single_ing == 1 ) {
							$ing = $new_ing_name[0] = rtrim( $new_ing_name[0], ", " );
						} else {
							$new_ing_name[0] = substr( $new_ing_name[0], $str_pos + 1, strlen( $new_ing_name[0] ) );
							$new_ing_name[0] = rtrim($new_ing_name[0], ' ');
							$ing = $new_ing_name[0] = substr( $new_ing_name[0], 0, (strlen( $new_ing_name[0] )-2) ); 
						}
					} else {
						$ing = rtrim($ing, ' ');
						$ing = rtrim($ing, ',');
					}
				} else {
					$ing = rtrim($ing, ' ');
					$ing = rtrim($ing, ',');
				}
			}
			else{
				$ing = '';
				$count = 0;
				foreach ($product_ingredients as $key => $ingredients){
					if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
						$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
						$to_remove = $len+2;
						$ing = substr($ing, 0 , -$to_remove);
						$ing .= '';
						$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
						// $ing = rtrim($ing, ' ');
						// $ing .= ', ';
						$single_ing = 0;
					}
					else {
						if( $ingredients->ki_id == 0 ){
							if( $ingredients->ki_name == '(' ){
								if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
									$ing .= ',';
								}
								else{
									$ing .= ' (';
								}
							}
							elseif( $ingredients->ki_name == ')' ){
								$ing  = rtrim($ing, ' ');
								$ing  = rtrim($ing, ',');
								if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){

									$ing .= ', ';
								}
								else{
									$ing .= '), ';
								}
								array_push( $new_ing_name, $ing );
							}
							else{
								$ing .= $ingredients->ki_name;
							}
						}

						if($ingredients->ki_id != 0){
							$count++;
							if($ingredients->prefix == ''){
								$flag = true;
								if($ingredients->ki_name == '('){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= ' (';
								}
								elseif( $ingredients->ki_name == ')' ){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= '), ';
								}
								elseif($ingredients->ki_name == ':'){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									$ing .= ': ';
								}else{
									$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
									// $ing = rtrim($ing, ' ');
									// $ing .= ', ';
								}
							}
						}
					}
				}
				$ing = rtrim($ing," ,");
				if( sizeof( $new_ing_name ) == 1  ) {
					if( strpos( $new_ing_name[0], ')' ) !== false ) {
						$str_pos = stripos( $new_ing_name[0], '(' );
						if( $single_ing == 1 ) {
							$ing = $new_ing_name[0] = rtrim( $new_ing_name[0], ", " );
						} else {
							$new_ing_name[0] = substr( $new_ing_name[0], $str_pos + 1, strlen( $new_ing_name[0] ) );
							$new_ing_name[0] = rtrim($new_ing_name[0], ' ');
							$ing = $new_ing_name[0] = substr( $new_ing_name[0], 0, (strlen( $new_ing_name[0] )-2) ); 
						}
					}
				}
			}
			if(!empty($ing)){
				$ing = "Ingredienten: ".$ing;
			}
			else{
				$ing = "Nog geen allergenen info beschikbaar";
			}

			if(!empty($allergens)){
				$total_pro_allergens = count($allergens);
				$total_pro_allergens = $total_pro_allergens-1;
				foreach ($allergens as $k => $v){
					$allergens_key = array_search($v->ka_name, $allergens_array);
					$a .=  $allergens_key;

					if(($v->ka_id == 1) || ($v->ka_id == 8)){
						$a1 = '';
						if(!empty($sub_allergens)){
							$a1 .= ' (';
							foreach ($sub_allergens as $v1){
								if($v1->parent_ka_id == $v->ka_id){
									$sub_allergens_key = array_search($v1->sub_ka_name, $sub_allergence_array);
									$a1 .=  $sub_allergens_key.', ';
								}
							}
							$a1 = rtrim($a1,', ');
							$a1 .= ')';
							$a1 = str_replace('()', '', $a1);
						}
						$a .= $a1;
					}
					//if($k < $total_pro_allergens)
						$a .=  ', ';
				}
				$a = rtrim(trim($a),',');
			}

			if(empty($a))
				$ing = rtrim(trim($ing),",");

			$recipe_wt = $value['recipe_weight'];
			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}
			else{
				$recipe_wt = 100;
			}

			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($value['id']);

			$nutri_values = array();
			$nutri_str = '';
			if (!empty($has_fdd_quant)){
				$nutri_values['e_val_1'] = 0;
				$nutri_values['e_val_2'] = 0;
				$nutri_values['protiens'] = 0;
				$nutri_values['carbo'] = 0;
				$nutri_values['sugar'] = 0;
				$nutri_values['fats'] = 0;
				$nutri_values['sat_fats'] = 0;
				$nutri_values['salt'] = 0;


				foreach ($has_fdd_quant as $has_fdd_qu){
					
					$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
					if( !empty( $fdd_pro_info ) ){
						if(isset($fdd_pro_info[0]))
						{
							$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
							$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
							$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
							$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
							$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
							$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
							$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
							$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							//break;
						}
					}
				}
				$n_arr = $n_str = array();
				$n_num = 0;

				$nutri_str = "Voedingswaarden gem. per 100 g: energie: ".defined_money_format($nutri_values['e_val_1'],0)." kcal/".defined_money_format($nutri_values['e_val_2'],0)." kJ. vetten ".defined_money_format($nutri_values['fats'],1)." g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)." g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)." g. waarvan - suikers ".defined_money_format($nutri_values['carbo'],1)." g. eiwitten ".defined_money_format($nutri_values['protiens'],1)." g. zout ".defined_money_format($nutri_values['salt'],1)." g";

			}
			$data_n = $data_s = $row_extra = '';
			$sel_product_type = '1';
			if ($value['sell_product_option'] == 'weight_wise'){
				$sel_product_type = '0';
			}
			if($value['pro_art_num'] != '' && $value['pro_art_num'] != '0' && $value['pro_art_num'] != 'null'){
				$length = strlen($value['pro_art_num']);
				if($length < 8){
					$art_num = str_pad($value['pro_art_num'], 8, "0", STR_PAD_LEFT);
				}
				else{
					$value['pro_art_num'] = substr($value['pro_art_num'], 0, 8);
					$art_num = str_pad($value['pro_art_num'], 8, "0", STR_PAD_LEFT);
				}
				$row_ing = "0111".pack("H*","1F")."AA".$art_num."".pack("H*","1F")."AB04000000".$ing;
				$row_nutri = $nutri_str."".pack("H*","1F")."VH0500".pack("H*","1F")."\r\n";
				$row_extra = "0101".pack("H*","1F")."AA".$art_num.pack("H*","1F")."WB".$art_num.pack("H*","1F")."\r\n";
				$data_n = $this->digi_sub($row_ing);
				$data_s = $this->digi_sub($row_nutri,1);
				$data .= $data_n.$data_s.$row_extra;
			}
			else{
				$length = strlen($value['pro_art_num']);
				$art_num = str_pad("0", 8, "0", STR_PAD_LEFT);
				$row_ing = "0111".pack("H*","1F")."AA".$art_num."".pack("H*","1F")."AB04000000".$ing;
				$row_nutri = $nutri_str."".pack("H*","1F")."VH0500".pack("H*","1F")."\r\n";
				$row_extra = "0101".pack("H*","1F")."AA".$art_num.pack("H*","1F")."WB".$art_num.pack("H*","1F")."\r\n";
				$data_n = $this->digi_sub_exp($row_ing);
				$data_s = $this->digi_sub_exp($row_nutri,1);
				$data .= $data_n.$data_s.$row_extra;
			}
			$c++;
		}
		$flag = 0;
		$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/digi_export/';

		$file_name1 = "FD-Ingredient.dat";

		$file_name3 = "FD-Ingredient.start";
		$flag = 2;
		if($flag == 2){

			$this->load->library('zip');
			$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/digi_export/';
			$this->zip->add_data($file_name1, $data);
			$this->zip->add_data($file_name3, "");
			$this->zip->archive($path.'FoodDesk_'.$company_id.'_'.time().'.zip');
			echo 'FoodDesk_'.$company_id.'_'.time().'.zip';

		}
		else{
			echo '';
		}
	}

	private function digi_sub($row = '',$set_d = 0){
		$new_string = array ();

		$s = '';
		$pos = '';
		$j = 0;
		$count = 0;
		$len = strlen ( $row );

		for($i=0;$i<$len;$i++){

			if ($row [$i] == ' '){
				$pos = $i;
			}

			if ($count % 47 == '0' && $i != '0') {

				$k = $i;
				if ($row [$k + 1] == ' ') {

					$j = $i;

					$new_string [] = $row [$i];
					$new_string [$j] .= '~04000000';

					$count = 0;
				}
				elseif (preg_match ( "/[a-zA-Z0-9]/i", $row [$k + 1] ) && ($row [$k + 1] != '<' && $row [$k + 2] != 'b' && $row [$k + 3] != '>') && ($row [$k + 1] != '<' && $row [$k + 2] != '/' && $row [$k + 3] != 'b' && $row [$k + 4] != '>')) {
					$j = $i;
					if ($row [$j] == ' ') {
						$new_string [] = $row [$i];
						$new_string [$j] = '~04000000';
						$count = 0;
					} else {
						do {
							$j = $j - 1;

						} while ( $j > $pos );

						if ($j == $pos) {
							if($pos == '41'){
								$new_string [] = $row [$i];
								$new_string [$j] .= '~04000000';
								$count = 0;
							}
							else{
								$new_string [] = $row [$i];
								$new_string [$j] = '~04000000';
								$count = 0;
							}
						}
					}
				}
				else {
					$j = $i;
					if($row[$i] == ' '){
						$new_string [] = $row [$i];
						$new_string [$j] = '~04000000';
						$count = 0;
					}else{
						do {
							$j = $j - 1;
						} while ( $j > $pos );

						if ($j == $pos) {
							if($pos == '41'){
								$new_string [] = $row [$i];
								$new_string [$j] .= '~04000000';
								$count = 0;
							}
							else{
								$new_string [] = $row [$i];
								$new_string [$j] = '~04000000';
								$count = 0;
							}
						}
					}
				}
			}
			elseif ($count % 47 != '0' || $i == '0') {
				$new_string [] = $row [$i];
			}

			if(($row[$i] != '<' && $row[$i + 1] != 'b' && $row[$i + 2] != '>') && ($row[$i] != '<' && $row[$i + 1] != '/' && $row[$i + 2] != 'b' && $row[$i + 3] != '>')){
				$count ++;
			}
		}

		foreach ($new_string as $string){
			$s .= $string;
		}

		if($set_d){
			return '~04000000'.$s;
		}
		else{
			return $s;
		}
	}

	private function digi_sub_exp($row = '',$set_d = 0){
		$new_string = array ();

		$s = '';
		$pos = '';
		$count = 0;

		$j = 0;
		$len = strlen ( $row );
		for($i=0;$i<=$len;$i++)	{
			if ($row [$i] == ' ') {
				$pos = $i;
			}

			if ($count % 47 == '0' && $i != '0') {
				$k = $i;
				if ($row [$k + 1] == ' ') {
					$j = $i;
					$new_string [] = $row [$i];
					$new_string [$j] .= '~04000000';
					$count = 0;
				}
				elseif (preg_match ( "/[a-zA-Z0-9]/i", $row [$k + 1] ) && ($row [$k + 1] != '<' && $row [$k + 2] != 'b' && $row [$k + 3] != '>') && ($row [$k + 1] != '<' && $row [$k + 2] != '/' && $row [$k + 3] != 'b' && $row [$k + 4] != '>')) {
					$j = $i;
					if ($row [$j] == ' ') {
						$new_string [] = $row [$i];
						$new_string [$j] = '~04000000';
						$count = 0;
					}
					else {
						do {
							$j = $j - 1;
						} while ( $j > $pos );

						if ($j == $pos){
							if($pos == '41'){
								$new_string [] = $row [$i];
								$new_string [$j] .= '~04000000';
								$count = 0;
							}
							else {
								$new_string [] = $row [$i];
								$new_string [$j] = '~04000000';
								$count = 0;
							}
						}
					}
				}
				else {
					$j = $i;
					if($row[$i] == ' '){

						$new_string [] = $row [$i];
						$new_string [$j] = '~04000000';
						$count = 0;
					}
					else{
						do {
							$j = $j - 1;
						} while ( $j > $pos );

						if ($j == $pos) {
							if($pos == '41'){
								$new_string [] = $row [$i];
								$new_string [$j] .= '~04000000';
								$count = 0;
							}
							else{
								$new_string [] = $row [$i];
								$new_string [$j] = '~04000000';
								$count = 0;
							}
						}
					}
				}
			}
			elseif ($count % 47 != '0' || $i == '0') {
				$new_string [] = $row [$i];
			}

			if(($row[$i] != '<' && $row[$i + 1] != 'b' && $row[$i + 2] != '>') && ($row[$i] != '<' && $row[$i + 1] != '/' && $row[$i + 2] != 'b' && $row[$i + 3] != '>') && $row[$i] != ' ') {
				$count ++;
			}
			elseif($row[$i] == ' '){
				$count ++;
			}
		}
		foreach ($new_string as $string){
			$s .= $string;
		}

		if($set_d){
			return '~04000000'.$s;
		}
		else{
			return $s;
		}
	}

	function zenius_export($company_id = 0){
		$company_id = $this->company_id;
        
		$this->db->select('id,pro_art_num,proname,prodescription,sell_product_option,price_per_person,price_per_unit,price_weight');
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$this->db->where(array('company_id'=>$company_id));
		$products = $this->db->get('products')->result_array();

		if(!empty($products)){
			$this->load->model('Mproducts');
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Zenius Export') );

			$counter = 1;
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('ARTICLE NBR') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('PRODUCTNAAM') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('OMSCHRIJVING') )->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('TARIEF') )->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('ALLERGENEN') )->getStyle('E'.$counter)->getFont()->setBold(true);

			foreach ($products as $pro_key=>$product){
				$counter++;
				$data['fixed'] = $this->Mproducts->fixed($product['id']);
				$allergens = $this->Mproducts->get_product_allergence_dist($product['id'],1);
				$sub_allergens = $this->Mproducts->get_product_sub_allergence_dist($product['id'],1);

				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $product['pro_art_num'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($product['proname']) );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, stripslashes($product['prodescription']) );
				if($product['sell_product_option'] == 'per_unit')
					$this->excel->getActiveSheet()->setCellValue('D'.$counter, round($product['price_per_unit'],2).' €' );
				elseif ($product['sell_product_option'] == 'weight_wise')
					$this->excel->getActiveSheet()->setCellValue('D'.$counter, round($product['price_weight']*1000,2).' €/Kg' );
				else
					$this->excel->getActiveSheet()->setCellValue('D'.$counter, round($product['price_per_person'],2).' €/P.' );

				if(empty($data['fixed']))
				{
					$product_allergences=$allergens;
					if (!empty($product_allergences))
					{
						foreach ($product_allergences as $allergences_key=>$allergences_val)
						{
							$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
							$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
						}
					}
					$product_sub_allergences=$sub_allergens;
					if (!empty($product_sub_allergences))
					{
						foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
						{
							$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
							$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
						}
					}

					$all_aller = $this->Mproducts->get_ing_allergen($product['id'],$this->lang_u );
					foreach ($all_aller as $all_aller_k => $all_aller_v ) {
						$allergence_lang_d = 'allergence';
						$sub_allergence_d = 'sub_allergence';
						if ($this->lang_u == '_dch') {
							$allergence_lang_d = 'allergence_dch';
							$sub_allergence_d = 'sub_allergence_dch';

						}else if ($this->lang_u == '_fr') {
							$allergence_lang_d = 'allergence_fr';
							$sub_allergence_d = 'sub_allergence_fr';
						}

						if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
							$all_allergence[] = $all_aller_v->$allergence_lang_d;
						}
						if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
							$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
						}
					}
					$all_allergence = array_unique($all_allergence);

					$final_all = array();
					foreach ($all_allergence as $key1 => $value1 ) {
						$ing_aller = explode('-', $value1);
						$final_all = array_merge($ing_aller, $final_all);
					}
					$final_all = array_unique($final_all);
					$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);
					if (empty($final_aller)) {
						$allergens = array_values(array_unique($product_allergence, SORT_REGULAR));
					}
					elseif (empty($product_allergence))
					{
						$allergens = array_values(array_unique($final_aller, SORT_REGULAR));
					}
					else{
						$allergens_m = array_merge($product_allergence,$final_aller);
						$allergens = array_values(array_unique($allergens_m, SORT_REGULAR));
					}

					$final_sub_all = array();
					foreach ($all_sub_allergence as $key1 => $value1 ) {
						$ing_sub_aller = explode('-', $value1);
						$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
					}
					$final_sub_all = array_unique($final_sub_all);
					$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);
					$sub_allergens = array_merge($product_sub_allergence,$final_sub_aller);
					$sub_allergens = array_values(array_unique($sub_allergens, SORT_REGULAR));

					if (empty($final_sub_aller)) {
						$sub_allergens = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
					}
					elseif (empty($product_sub_allergence))
					{
						$sub_allergens = array_values(array_unique($final_sub_aller, SORT_REGULAR));
					}
					else{
						$sub_allergens_m = array_merge($product_sub_allergence,$final_sub_aller);
						$sub_allergens = array_values(array_unique($sub_allergens_m, SORT_REGULAR));
					}
				}

				$all = '';
				if(!empty($allergens)){
					foreach ($allergens as $allergence){
						$all .= $allergence->ka_name;

						if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
							$a1 = '';
							if(!empty($sub_allergens)){
								$a1 .= ' (';
								foreach ($sub_allergens as $sub_allergence){
									if($sub_allergence->parent_ka_id == $allergence->ka_id){
										$a1 .=  $sub_allergence->sub_ka_name.', ';
									}
								}
								$a1 = rtrim($a1,', ');
								$a1 .= ')';
								$a1 = str_replace('()', '', $a1);
							}
							$all .= $a1;
						}
						$all .=  ', ';
					}
				}

				if($all != '')
					$all = substr($all, 0, -2);

				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $all );
			}

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(80);

			$datestamp = date("d-m-Y");
			$filename = "Zenius-Export-".$company_id."-".time().".xls";
			$file_zip = "export-labels-".$company_id."-".time();

            $path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/zen_export/';
            header('Content-Type: application/vnd.ms-excel');
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save($path.$filename,'php://output');

            $content =  file_get_contents($path.$filename);
			file_put_contents($path.$filename, $content);

			// $zip = new ZipArchive;
			// if ($zip->open($path.$file_zip.'.zip', ZipArchive::CREATE) === TRUE) {
			// 	$zip->addFile($path.$filename, $filename);
			// 	$zip->close();
			// }
			// if (file_exists($path.$filename)){
			// 	unlink($path.$filename);
			// }
			// echo  $file_zip.'.zip';
			echo $filename;die;
		}
		else{
			echo '';
		}
    }

    function label_export($company_id= 0){
    	$new_ing_name = array();
    	$company_id = $this->company_id;
    	$lang_arr = array('_dch','_fr');
		

		$this->db->select('company.company_name,company.address,company.zipcode,company.city,company.phone,company.website,general_settings.labeler_logo');
		$this->db->join('general_settings','general_settings.company_id=company.id');
		$this->db->where('company.id',$company_id);
		$company = $this->db->get('company')->row_array();

		$this->db->select('id,pro_art_num,proname,prodescription,sell_product_option,price_per_person,price_per_unit,price_weight,recipe_weight');
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$this->db->where(array('company_id'=>$company_id));
		$this->db->order_by('proname');
		$products = $this->db->get('products')->result_array();

		if(!empty($products)){
			$this->load->model('Mproducts');
			
			
			$aller_arr = $this->fdb->get('allergence')->result_array();

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Labels Export') );

			$counter = 1;
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('PLU') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('PRODUCTNAAM') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('OMSCHRIJVING') )->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('TARIEF') )->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('INGREDIENTEN') )->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('INGRÉDIENTS') )->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('ALLERGENEN') )->getStyle('G'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('ALLERGÈNES') )->getStyle('H'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('VOEDINGWAARDEN') )->getStyle('I'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('VALEURS NUTRITIONNELLES') )->getStyle('J'.$counter)->getFont()->setBold(true);

			
			foreach ($products as $pro_key=>$product){
				$tarief = '';
				if ($product['sell_product_option'] == 'per_unit')
					$tarief = ($product['price_per_unit'] != '' && $product['price_per_unit'] != null)?defined_money_format($product['price_per_unit']).' €':'0 €';
				elseif ($product['sell_product_option'] == 'per_person')
					$tarief = ($product['price_per_person'] != '' && $product['price_per_person'] != null)?defined_money_format($product['price_per_person']).' €/P.':'0 €/P.';
				elseif ($product['sell_product_option'] == 'weight_wise')
					$tarief = ($product['price_weight'] != '' && $product['price_weight'] != null)?defined_money_format($product['price_weight']*1000).' €/'._('kg'):'0 €/'._('kg');
				elseif ($product['sell_product_option'] == 'client_may_choose'){
					$tarief = ($product['price_per_unit'] != '' && $product['price_per_unit'] != null)?defined_money_format($product['price_per_unit']).' €':'0 €';
					$tarief .= "\n";
					$tarief .= ($product['price_weight'] != '' && $product['price_weight'] != null)?defined_money_format($product['price_weight']*1000).' €/'._('kg'):'0 €/'._('kg');
				}

				
				$product_ingredients = $this->Mproducts->get_product_ingredients_exp($product['id']);
				$data['fixed'] = $this->Mproducts->fixed($product['id']);

				$product_allergences = $this->Mproducts->get_product_allergence_dist($product['id']);
				$product_sub_allergences = $this->Mproducts->get_product_sub_allergence_dist($product['id']);

				$ing_fr = $ing_dch = '';
				for($l = 0; $l < count($lang_arr); $l++ ){
					$sel_lang = $lang_arr[$l];
					$all_name = 'all_name'.$sel_lang;
					$new_ing_name = array();

					$all_id_arr = array();
					if(!empty($product_allergences)){
						foreach ($product_allergences as $aller){
							$all_id_arr[] = $aller->ka_id;
						}
					}

					$allergence_words = array();
					foreach ($aller_arr as $val){
						if(in_array($val['all_id'],$all_id_arr))
							$allergence_words[strtolower($val[$all_name])] = $val['all_id'];
					}
					$single_ing = 0;
					$ki_name = 'ki_name'.$sel_lang;
					if(empty($data['fixed']))
					{
						$aller_type 	= 'aller_type'.$sel_lang ;
					  	$allergence 	= 'allergence'.$sel_lang ;
					  	$sub_allergence = 'sub_allergence'.$sel_lang ;
						$ing 			= '';
						$count 			= 0;
						foreach ($product_ingredients as $key => $ingredients){
							if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
								
								$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
								$to_remove = $len+2;
								$ing = substr($ing, 0 , -$to_remove);
								$ing .= '';
								if( empty($fixed) ){
									$ing .= get_the_allergence($ingredients->$ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence,$sel_lang);
								}else{
									$ing .= get_the_allergen($ingredients->$ki_name,$ingredients->have_all_id,$allergence_words);
								}
								$single_ing = 1;
							}
							else{
								if( $ingredients->ki_id == 0 ){
									if( $ingredients->ki_name == '(' ){
										if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){

											$ing .= ',';
										}
										else{
											$ing .= ' (';
										}
									}
									elseif( $ingredients->$ki_name == ')' ){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){

											$ing .= ', ';
										}
										else{
											$ing .= '),';
										}
										array_push( $new_ing_name, $ing );
									}
									else{
										$ing .= $ingredients->ki_name;

									}
								}

								if($ingredients->ki_id != 0){
									$count++;
									if($ingredients->prefix == ''){
										$flag = true;
										if($ingredients->$ki_name == '('){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= ' (';
										}
										elseif( $ingredients->$ki_name == ')' ){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= '), ';
										}
										elseif($ingredients->$ki_name == ':'){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= ': ';
										}else{
											if( empty($fixed) ){
												$ing .= get_the_allergence($ingredients->$ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence,$sel_lang);
											}else{
												$ing .= get_the_allergen($ingredients->$ki_name,$ingredients->have_all_id,$allergence_words);
											}
										}
									}
								}
							}
						}

						if( $count == 1 ){
							$ing = rtrim($ing, ' ');
							$ing = rtrim($ing, ',');
						}
						elseif( $count == 0 ){
							$ing = str_replace(' (),', '', $ing);
							$ing = rtrim($ing, ' ');
							$ing = rtrim($ing, ',');
						}
						else{
							$ing = rtrim($ing, ' ');
							$ing = rtrim($ing, ',');
						}
						if( sizeof( $new_ing_name ) == 1  ) {
							if( strpos( $new_ing_name[0], ')' ) !== false ) {
								$str_pos = stripos( $new_ing_name[0], '(' );
								if( $single_ing == 1 ) {
									$ing = $new_ing_name[0] = rtrim( $new_ing_name[0], ", " );
								} else {
									$new_ing_name[0] = substr( $new_ing_name[0], $str_pos + 1, strlen( $new_ing_name[0] ) );
									$new_ing_name[0] = rtrim($new_ing_name[0], ' ');
									$ing = $new_ing_name[0] = substr( $new_ing_name[0], 0, (strlen( $new_ing_name[0] )-2) ); 
								}
							} else {
								$ing = rtrim($ing, ' ');
								$ing = rtrim($ing, ',');
							}
						} else {
							$ing = rtrim($ing, ' ');
							$ing = rtrim($ing, ',');
						}
					}
					else{
						$ing = '';
						$count = 0;
						foreach ($product_ingredients as $key => $ingredients){
							if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
								$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
								$to_remove = $len+2;
								$ing = substr($ing, 0 , -$to_remove);
								$ing .= '';

								$ing .= get_the_allergen($ingredients->$ki_name,$ingredients->have_all_id,$allergence_words);
								// $ing = rtrim($ing, ' ');
								// $ing .= ', ';
								$single_ing = 1;
							}
							else {
								if( $ingredients->ki_id == 0 ){
									if( $ingredients->ki_name == '(' ){
										if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){

											$ing .= ',';
										}
										else{
											$ing .= ' (';
										}
									}
									elseif( $ingredients->ki_name == ')' ){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){
											$ing .= ', ';
										}
										else{
											$ing .= '), ';
										}
										array_push( $new_ing_name, $ing );
									}
									else{
										$ing .= $ingredients->ki_name;

									}
								}

								if($ingredients->ki_id != 0){
									$count++;
									if($ingredients->prefix == ''){
										$flag = true;
										if($ingredients->$ki_name == '('){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= ' (';
										}
										elseif( $ingredients->$ki_name == ')' ){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= '), ';
										}
										elseif($ingredients->$ki_name == ':'){
											$ing  = rtrim($ing, ' ');
											$ing  = rtrim($ing, ',');
											$ing .= ': ';
										}else{
											$ing .= get_the_allergen($ingredients->$ki_name,$ingredients->have_all_id,$allergence_words);
											// $ing = rtrim($ing, ' ');
											// $ing .= ', ';
										}
									}
								}
							}
						}
						$ing = rtrim($ing," ,");
						if( sizeof( $new_ing_name ) == 1  ) {
							if( strpos( $new_ing_name[0], ')' ) !== false ) {
								$str_pos = stripos( $new_ing_name[0], '(' );
								if( $single_ing == 1 ) {
									$ing = $new_ing_name[0] = rtrim( $new_ing_name[0], ", " );
								} else {
									$new_ing_name[0] = substr( $new_ing_name[0], $str_pos + 1, strlen( $new_ing_name[0] ) );
									$new_ing_name[0] = rtrim($new_ing_name[0], ' ');
									$ing = $new_ing_name[0] = substr( $new_ing_name[0], 0, (strlen( $new_ing_name[0] )-2) ); 
								}
							}
						}
					}
					if($sel_lang == '_dch')
					{
						$ing_dch = $ing;
					}elseif($sel_lang == '_fr'){
						$ing_fr = $ing;
					}else{
						$ing_dch = $ing;
					}
				}

			$all 	 = '';
			$all_fr  = '';
			$all_dch = '';

			if(!empty($product_allergences)){ 
				foreach ($product_allergences as $allergence){
					$all_name = $this->Mproducts->get_all_name( $allergence->ka_id, $sel_lang, 'all' );

					$all 	 .= $all_name[ 'all_name' ];
					$all_fr  .= $all_name[ 'all_name_fr' ];
					$all_dch .= $all_name[ 'all_name_dch' ];

					if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
						$a1 	= '';
						$a1_fr  = '';
						$a1_dch = '';
						if(!empty($product_sub_allergences)){
							$a1 	.= ' (';
							$a1_fr  .= ' (';
							$a1_dch .= ' (';
							foreach ($product_sub_allergences as $sub_allergence){
								if($sub_allergence->parent_ka_id == $allergence->ka_id){
									$sub_all_name = $this->Mproducts->get_sub_all_name( $sub_allergence->sub_ka_id, $sel_lang, 'all' );
									
									$a1 	.= $sub_all_name[ 'all_name' ].', ';
									$a1_fr  .= $sub_all_name[ 'all_name_fr' ].', ';
									$a1_dch .= $sub_all_name[ 'all_name_dch' ].', ';
								}
							}
							//eng
							$a1 = rtrim($a1,', ');
							$a1 .= ')';
							$a1 = str_replace('()', '', $a1);
							//fr
							$a1_fr = rtrim($a1_fr,', ');
							$a1_fr .= ')';
							$a1_fr = str_replace('()', '', $a1_fr);
							//dch
							$a1_dch = rtrim($a1_dch,', ');
							$a1_dch .= ')';
							$a1_dch = str_replace('()', '', $a1_dch);
						}
						$all 	 .= $a1;
						$all_fr  .= $a1_fr;
						$all_dch .= $a1_dch;
					}
					$all 	 .=  ', ';
					$all_fr  .=  ', ';
					$all_dch .=  ', ';
				}
				$all 	 = substr($all, 0, -2);
				$all_fr  = substr($all_fr, 0, -2);
				$all_dch = substr($all_dch, 0, -2);
			}

				
			

			$recipe_wt = $product['recipe_weight'];
			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}else{
				$recipe_wt = 100;
			}

			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($product['id']);
			$nutri_values = array();
			$nutri_str = '';
			$nutri_str_fr = '';
			if (!empty($has_fdd_quant)){
				$nutri_values['e_val_1'] = 0;
				$nutri_values['e_val_2'] = 0;
				$nutri_values['protiens'] = 0;
				$nutri_values['carbo'] = 0;
				$nutri_values['sugar'] = 0;
				$nutri_values['fats'] = 0;
				$nutri_values['sat_fats'] = 0;
				$nutri_values['salt'] = 0;

				foreach ($has_fdd_quant as $has_fdd_qu){
					
					$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
					
					if( !empty( $fdd_pro_info ) ){
						$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
						$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
						$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
						$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
						$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
						$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
						$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
						$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
					}
				}

				$nutri_str = "Voedingswaarden gem. per 100 g: energie: ".defined_money_format($nutri_values['e_val_1'],0)." kcal/".defined_money_format($nutri_values['e_val_2'],0)." kJ. vetten ".defined_money_format($nutri_values['fats'],1)." g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)." g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)." g. waarvan - suikers ".defined_money_format($nutri_values['carbo'],1)." g. eiwitten ".defined_money_format($nutri_values['protiens'],1)." g. zout ".defined_money_format($nutri_values['salt'],1)." g";


				$nutri_str_fr = "Valeurs nutritionnelles moy. par 100 g: valeur énérgétique: ".defined_money_format($nutri_values['e_val_1'],0)." kcal/".defined_money_format($nutri_values['e_val_2'],0)." kJ. graisses ".defined_money_format($nutri_values['fats'],1)." g dont - acides gras saturés ".defined_money_format($nutri_values['sat_fats'],1)." g. glucides ".defined_money_format($nutri_values['carbo'],1)." g. dont - sucres ".defined_money_format($nutri_values['carbo'],1)." g. protéines ".defined_money_format($nutri_values['protiens'],1)." g. sel ".defined_money_format($nutri_values['salt'],1)." g";
				}

				$counter++;
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $product['pro_art_num'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($product['proname']) );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, stripslashes($product['prodescription']) );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $tarief );
				$this->excel->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, rtrim( $ing_dch , ', ') );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, rtrim( $ing_fr , ', ') );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $all_dch );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $all_fr );
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, $nutri_str );
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, $nutri_str_fr );



			}
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(50);


			$datestamp = date("d-m-Y");
			$filename = "export-labels-".$company_id."-".time().".xls";
			$file_zip = "export-labels-".$company_id."-".time();
			$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/label_export/';

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save($path.$filename,'php://output');

			$content =  file_get_contents($path.$filename);

			file_put_contents($path.$filename, $content);
			echo  $filename;
		}
		else{
			echo  false;
		}
    }

    function print_product($company_id = 0){
    	$company_id = $this->company_id;
		$this->load->model('Mproducts');
		$this->load->model('Mcategories');
		$this->load->model('Msubcategories');

		$cat_name = '';
		$subcat_name = '';
		$row_html = '';

		$row_html .= '<h3 style="font-family: arial;">Productenlijst</h3>';

		$cat_arr = $this->Mcategories->get_category(array('company_id' => $company_id));

		if(!empty($cat_arr)){
			//looping categories..
			foreach($cat_arr as $cat){

				$subcat_arr = $this->Msubcategories->get_subcategory(array('categories_id'=> $cat->id));

				if($cat->id > 0){
					$cat_name = $this->Mcategories->get_cat_name($cat->id);
					if(!empty($cat_name))
						$cat_name = $cat_name['name'];
				}

				if(!empty($subcat_arr)){

					//looping subcategories..
					foreach($subcat_arr as $subcat){
						if($subcat->id > 0){
							$subcat_name = $this->Msubcategories->get_sub_cat_name($subcat->id);
							if(!empty($subcat_name))
								$subcat_name = ' > '.$subcat_name['subname'];
						}

						$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

						$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.$subcat_name.'</th></tr></thead><tbody>';

						$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

						$products = $this->Mproducts->get_products($cat->id, $subcat->id,null,null,false);
						if(!empty($products)){
							//Looping products using product ids..
							foreach($products as $product){

								$p_id = $product->id;

								$row_html .= '<tr>';

								$product = $this->db->get_where('products',array('id'=>$p_id,'company_id'=>$company_id))->result();
								$product = $product[0];
								$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';
								$data['fixed'] = $this->Mproducts->fixed($p_id);
								$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
								$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);

								if(empty($data['fixed']))
								{
									$product_allergences=$k_allergence;
									if (!empty($product_allergences))
									{
										foreach ($product_allergences as $allergences_key=>$allergences_val)
										{
											$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
											$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
										}
									}
									$product_sub_allergences=$k_sub_allergence;
									if (!empty($product_sub_allergences))
									{
										foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
										{
											$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
											$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
										}
									}

									$all_aller = $this->Mproducts->get_ing_allergen($p_id,$this->lang_u );
							 		foreach ($all_aller as $all_aller_k => $all_aller_v ) {
										$allergence_lang_d = 'allergence';
										$sub_allergence_d = 'sub_allergence';
							 			if ($this->lang_u == '_dch') {
							 				$allergence_lang_d = 'allergence_dch';
							 				$sub_allergence_d = 'sub_allergence_dch';

							 			}else if ($this->lang_u == '_fr') {
							 				$allergence_lang_d = 'allergence_fr';
							 				$sub_allergence_d = 'sub_allergence_fr';
							 			}

							 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
							 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
							 			}
							 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
							 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
							 			}
							 		}
							 		$all_allergence = array_unique($all_allergence);

									$final_all = array();
									foreach ($all_allergence as $key1 => $value1 ) {
										$ing_aller = explode('-', $value1);
										$final_all = array_merge($ing_aller, $final_all);
									}
									$final_all = array_unique($final_all);
									$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);

									if (empty($final_aller)) {
										$k_allergence = array_values(array_unique($product_allergence, SORT_REGULAR));
									}
									elseif (empty($product_allergence))
									{
										$k_allergence = array_values(array_unique($final_aller, SORT_REGULAR));
									}
									else{
										$allergens_m = array_merge($product_allergence,$final_aller);
										$k_allergence = array_values(array_unique($allergens_m, SORT_REGULAR));
									}



									$final_sub_all = array();
									foreach ($all_sub_allergence as $key1 => $value1 ) {
										$ing_sub_aller = explode('-', $value1);
										$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
									}
									$final_sub_all = array_unique($final_sub_all);
									$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);
									$k_sub_allergence = array_merge($product_sub_allergence,$final_sub_aller);
									$k_sub_allergence = array_values(array_unique($k_sub_allergence, SORT_REGULAR));
								}



								if(!empty($k_allergence)){
									$allrg_str = '';
									//Looping product allergence to create allergence string..
									foreach ($k_allergence as $k_allerg){
										$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
										if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
											$a1 = '';
											if(!empty($k_sub_allergence)){
												$a1 .= ' (';
												foreach ($k_sub_allergence as $k_sub_allerg){
													if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
														$a1 .=  $k_sub_allerg->sub_ka_name.', ';
													}
												}
												$a1 = rtrim($a1,', ');
												$a1 .= ')';
												$a1 = str_replace('()', '', $a1);
											}
											$allrg_str .= $a1;
										}
									}
									if($allrg_str != ''){
										$product->allergence = substr($allrg_str,2);
									}
									else
										$product->allergence = $product->allergence;
								}
								if($product->allergence == '')
									$product->allergence = '--';
								$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

								$row_html .= '</tr>';
							}
						} else {
							$row_html .= '<tr><td style="padding-left: 10px; vertical-align: top;">--</td><td style=" vertical-align: top;">--</td></tr>';
						}
						$row_html .= '</tbody></table><br/><br/>';
					}
				}

				//direct products of this category..
				$products = $this->Mproducts->get_products($cat->id, -1,null,null,false);

				if(!empty($products)){

					$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

					$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.'</th></tr></thead><tbody>';

					$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

					//Looping products using product ids..
					foreach($products as $product){

						$p_id = $product->id;
						$data['fixed'] = $this->Mproducts->fixed($p_id);
						$row_html .= '<tr>';

						$product = $this->db->get_where('products',array('id'=>$p_id,'company_id'=>$company_id))->result();

						$product = $product[0];

						$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

						$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
						$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);

						if(empty($data['fixed']))
						{
							$product_allergences=$k_allergence;
							if (!empty($product_allergences))
							{
								foreach ($product_allergences as $allergences_key=>$allergences_val)
								{
									$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
									$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
								}
							}
							$product_sub_allergences=$k_sub_allergence;
							if (!empty($product_sub_allergences))
							{
								foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
								{
									$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
									$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
								}
							}
							$all_aller = $this->Mproducts->get_ing_allergen($p_id);
							foreach ($all_aller as $all_aller_k => $all_aller_v ) {
								if($all_aller_v->allergence != '' && $all_aller_v->allergence != '0'){
									$all_allergence[] = $all_aller_v->allergence;
								}
								if($all_aller_v->sub_allergence != '' && $all_aller_v->sub_allergence != '0'){
									$all_sub_allergence[] = $all_aller_v->sub_allergence;
								}
							}
							$all_allergence = array_unique($all_allergence);

							$final_all = array();
							foreach ($all_allergence as $key1 => $value1 ) {
								$ing_aller = explode('-', $value1);
								$final_all = array_merge($ing_aller, $final_all);
							}
							$final_all = array_unique($final_all);
							$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);
							$k_allergence = array_merge($product_allergence,$final_aller);
							$k_allergence = array_values(array_unique($k_allergence, SORT_REGULAR));

							$final_sub_all = array();
							foreach ($all_sub_allergence as $key1 => $value1 ) {
								$ing_sub_aller = explode('-', $value1);
								$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
							}
							$final_sub_all = array_unique($final_sub_all);
							$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);

							if (empty($final_sub_aller)) {
								$k_sub_allergence = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
							}
							elseif (empty($product_sub_allergence))
							{
								$k_sub_allergence = array_values(array_unique($final_sub_aller, SORT_REGULAR));
							}
							else{
								$sub_allergens_m = array_merge($product_sub_allergence,$final_sub_aller);
								$k_sub_allergence = array_values(array_unique($sub_allergens_m, SORT_REGULAR));
							}
						}

						if(!empty($k_allergence)){
							$allrg_str = '';
							//Looping product allergence to create allergence string..
							foreach ($k_allergence as $k_allerg){
								$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
								if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
									$a1 = '';
									if(!empty($k_sub_allergence)){
										$a1 .= ' (';
										foreach ($k_sub_allergence as $k_sub_allerg){
											if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
												$a1 .=  $k_sub_allerg->sub_ka_name.', ';
											}
										}
										$a1 = rtrim($a1,', ');
										$a1 .= ')';
										$a1 = str_replace('()', '', $a1);
									}
									$allrg_str .= $a1;
								}
							}
							if($allrg_str != ''){
								$product->allergence = substr($allrg_str,2);
							}
							else
								$product->allergence = $product->allergence;
						}
						if($product->allergence == '')
							$product->allergence = '--';
						$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

						$row_html .= '</tr>';
					}
					$row_html .= '</tbody></table><br/><br/>';
				}
			}
		}

		$datestamp = date("d-m-Y");
		$filename = "print-all-".$company_id."-".time().".pdf";
		$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/print_all_report_import/';

		require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

		$report_name = 'report'.time().'.pdf';
		$mpdf=new mPDF('c');
		$mpdf->WriteHTML($row_html);

		$proname = trim(str_replace('/','',$data['product_information'][0]->proname));
		$mpdf->Output($path.$filename, 'F');
		echo $filename;
	}

	function all_technical_sheets($company_id = 0){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$company_id = $this->company_id;
		

		$this->db->select('id');
		$this->db->where(array('company_id'=>$company_id,'categories_id !='=>0,'direct_kcp'=>0,'recipe_weight >'=> 0));
		$results = $this->db->get('products')->result_array();

		$dir_name = 'Technischefiches_'.$company_id.'_'.date('Y_m_d_H_i_s');

		mkdir(dirname(__FILE__).'/../../../assets/cp/rep_exp_files/tech_import/'.$dir_name, 0777, true);

		if(!empty($results)){
			$all_name = 'all_name'.$this->lang_u;
			

			$this->fdb->select( array('all_id',$all_name) );
			$aller_arr = $this->fdb->get('allergence')->result_array();

			
			foreach ($results as $key=>$result){
				$pro_id = $result['id'];

				$this->load->model('Mproducts');

				$data['product_id']=$pro_id;
				$data['fixed'] = $this->Mproducts->fixed($pro_id);
				$data['approval_stat'] = $this->Mproducts->check_approval_status($pro_id);
				$data['product_information'] = $this->db->get_where('products',array('id'=>$pro_id,'company_id'=>$company_id))->result();

				$data['product_ingredients']=$this->Mproducts->get_product_ingredients_dist($pro_id,1);
				//$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten_dist($pro_id,1);
				//$data['product_additives']=$this->Mproducts->get_product_additives_dist($pro_id,1);

				$data['product_traces']=$this->Mproducts->get_product_traces($pro_id,1);
				$data['product_allergences']=$this->Mproducts->get_product_allergence_dist($pro_id,1);
				$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence_dist($pro_id,1);

				if($data['product_information'][0]->direct_kcp == 1 && $data['product_information'][0]->parent_proid != 0){
					$this->db->select('company.id,company_name,address,zipcode,city,phone');
					$this->db->join('products','products.company_id = company.id');
					$data['comp_det'] =  $this->db->get_where('company',array('products.id'=>$data['product_information'][0]->parent_proid))->result();

					$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$data['comp_det'][0]->id),array('sheet_banner','comp_default_image'));
				}
				else{
					$this->db->select('id,company_name,address,zipcode,city,phone');
					$data['comp_det'] =  $this->db->get_where('company',array('id'=>$company_id))->result();

					$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$company_id),array('sheet_banner','comp_default_image'));
				}

				$data['contains']= $this->M_fooddesk->used_own_pro_info($pro_id);
				$data['marked'] = $this->M_fooddesk->is_marked($pro_id);

				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();

				
				$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($pro_id);

				$recipe_wt = $data['product_information'][0]->recipe_weight;
				if($recipe_wt != 0){
					$recipe_wt = $recipe_wt*1000;
				}else{
					$recipe_wt = 100;
				}

				$nutri_values = array();
				if (!empty($has_fdd_quant)){
					$nutri_values['e_val_1'] = 0;
					$nutri_values['e_val_2'] = 0;
					$nutri_values['protiens'] = 0;
					$nutri_values['carbo'] = 0;
					$nutri_values['sugar'] = 0;
					$nutri_values['poly'] = 0;
					$nutri_values['farina'] = 0;
					$nutri_values['fats'] = 0;
					$nutri_values['sat_fats'] = 0;
					$nutri_values['single_fats'] = 0;
					$nutri_values['multi_fats'] = 0;
					$nutri_values['salt'] = 0;
					$nutri_values['fibers'] = 0;

					foreach ($has_fdd_quant as $has_fdd_qu){
						
						$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
						
						if( !empty( $fdd_pro_info ) ){
							$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
							$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
							$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
							$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
							$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
							$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
							$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
							$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
							$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
							$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
						}
					}

					$data['nutri_values'] = $nutri_values;
				}
				$results = $this->db->get('allergens_words')->result_array();
				$all_words = array();

				foreach ($results as $result){
					$all_words[] = $result['allergens_word'];
				}

				$data['all_words'] = $all_words;

				$data['is_fixed'] = $this->check_if_completed($pro_id);

				$all_id_arr = array();
				if(!empty($data['product_allergences'])){
					foreach ($data['product_allergences'] as $aller){
						$all_id_arr[] = $aller->ka_id;
					}
				}

				$data['allergence_words'] = array();
				foreach ($aller_arr as $val){
					if(in_array($val['all_id'],$all_id_arr))
						$data['allergence_words'][strtolower($val[$all_name])] = $val['all_id'];
				}
				$data['temperature'] = $this->M_fooddesk->get_prod_temp($pro_id);
//				$this->load->view('cp/technical_sheet_view', $data);

				if(empty($data['fixed']))
				{
					$product_allergences=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);
					if (!empty($product_allergences))
					{
						foreach ($product_allergences as $allergences_key=>$allergences_val)
						{
							$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
							$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
						}
					}
					$product_sub_allergences=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);
					if (!empty($product_sub_allergences))
					{
						foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
						{
							$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
							$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
						}
					}
					$all_aller = $this->Mproducts->get_ing_allergen($pro_id,$this->lang_u );

			 		foreach ($all_aller as $all_aller_k => $all_aller_v ) {
						$allergence_lang_d = 'allergence';
						$sub_allergence_d = 'sub_allergence';
			 			if ($this->lang_u == '_dch') {
			 				$allergence_lang_d = 'allergence_dch';
			 				$sub_allergence_d = 'sub_allergence_dch';

			 			}else if ($this->lang_u == '_fr') {
			 				$allergence_lang_d = 'allergence_fr';
			 				$sub_allergence_d = 'sub_allergence_fr';
			 			}

			 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
			 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
			 			}
			 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
			 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
			 			}
			 		}
	 				$all_allergence = array_unique($all_allergence);

					$final_all = array();
					foreach ($all_allergence as $key1 => $value1 ) {
						$ing_aller = explode('-', $value1);
						$final_all = array_merge($ing_aller, $final_all);
					}
					$final_all = array_unique($final_all);
					$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);
					if (empty($final_aller)) {
						$data['product_allergences'] = array_values(array_unique($product_allergence, SORT_REGULAR));
					}
					elseif (empty($product_allergence))
					{
						$data['product_allergences'] = array_values(array_unique($final_aller, SORT_REGULAR));
					}
					else{
						$data['product_allergences'] = array_merge($product_allergence,$final_aller);
						$data['product_allergences'] = array_values(array_unique($data['product_allergences'], SORT_REGULAR));
					}

					$final_sub_all = array();
					foreach ($all_sub_allergence as $key1 => $value1 ) {
						$ing_sub_aller = explode('-', $value1);
						$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
					}
					$final_sub_all = array_unique($final_sub_all);
					$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);
					if (empty($final_sub_aller)) {
						$data['product_sub_allergences'] = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
					}
					elseif (empty($product_sub_allergence))
					{
						$data['product_sub_allergences'] = array_values(array_unique($final_sub_aller, SORT_REGULAR));
					}
					else{
						$data['product_sub_allergences'] = array_merge($product_sub_allergence,$final_sub_aller);
						$data['product_sub_allergences'] = array_values(array_unique($data['product_sub_allergences'], SORT_REGULAR));
					}
				}

				$pdf_html = $this->load->view('cp/technical_sheet_view', $data, true);

				require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');
				$report_name = 'report'.time().'.pdf';
				$mpdf=new mPDF('c');
				$mpdf->WriteHTML($pdf_html);

				$proname = stripslashes(trim(str_replace('/','',$data['product_information'][0]->proname)));
				$mpdf->Output(dirname(__FILE__).'/../../../assets/cp/rep_exp_files/tech_import/'.$dir_name.'/Technische-fiche-'.$proname.$key.'.pdf', 'F');
			}

			$this->load->library('zip');
			$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/tech_import/';
			$this->zip->read_dir($path.$dir_name.'/',FALSE);
			$this->zip->archive($path.$dir_name.'.zip');
			echo $dir_name.'.zip';
		}
		else{
			echo false;
		}
	}

	function all_recipe_sheets($company_id = 0){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$company_id = $this->company_id;
		

		$this->db->select('id');
		$this->db->where(array('company_id'=>$company_id,'categories_id !='=>0,'direct_kcp'=>0,'recipe_weight >'=> 0));
		$results = $this->db->get('products')->result_array();

		$dir_name = 'Recepturenfiches_'.$company_id.'_'.date('Y_m_d_H_i_s');

		mkdir(dirname(__FILE__).'/../../../assets/cp/rep_exp_files/recipe_import/'.$dir_name, 0777, true);

		if(!empty($results)){
			
			$p_name = 'p_name'.$this->lang_u;
			$all_name = 'all_name'.$this->lang_u;
			$this->fdb->select( array( 'all_id', $all_name) );
			$aller_arr = $this->fdb->get('allergence')->result_array();

			foreach ($results as $pro_key=>$result){

				$pro_id = $result['id'];

				

				$this->db->select('id,company_name,address,zipcode,city,phone');
				$data['comp_det'] =  $this->db->get_where('company',array('id'=>$company_id))->result();

				$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$company_id),array('sheet_banner','comp_default_image'));

				$this->load->model('Mproducts');
				$data['product_id']=$pro_id;
				$data['fixed'] = $this->Mproducts->fixed($pro_id);
				$data['product_information'] = $this->db->get_where('products',array('id'=>$pro_id,'company_id'=>$company_id))->result();

				$data['product_ingredients']=$this->Mproducts->get_product_ingredients_dist($pro_id,1);
				//$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten_dist($pro_id,1);
				//$data['product_additives']=$this->Mproducts->get_product_additives_dist($pro_id,1);

				$data['product_traces']=$this->Mproducts->get_product_traces($pro_id,1);
				$data['product_allergences']=$this->Mproducts->get_product_allergence_dist($pro_id,1);
				$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence_dist($pro_id,1);

				$data['marked'] = $this->M_fooddesk->is_marked($pro_id);

				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();

				
				$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($pro_id);

				$recipe_wt = $data['product_information'][0]->recipe_weight;
				if($recipe_wt != 0){
					$recipe_wt = $recipe_wt*1000;
				}else{
					$recipe_wt = 100;
				}

				$nutri_values = array();
				if (!empty($has_fdd_quant)){
					$nutri_values['e_val_1'] = 0;
					$nutri_values['e_val_2'] = 0;
					$nutri_values['protiens'] = 0;
					$nutri_values['carbo'] = 0;
					$nutri_values['sugar'] = 0;
					$nutri_values['poly'] = 0;
					$nutri_values['farina'] = 0;
					$nutri_values['fats'] = 0;
					$nutri_values['sat_fats'] = 0;
					$nutri_values['single_fats'] = 0;
					$nutri_values['multi_fats'] = 0;
					$nutri_values['salt'] = 0;
					$nutri_values['fibers'] = 0;

					foreach ($has_fdd_quant as $has_fdd_qu){
						
						$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
						
						if( !empty( $fdd_pro_info ) ){
							$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
							$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
							$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
							$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
							$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
							$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
							$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
							$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
							$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
							$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
						}
					}

					$data['nutri_values'] = $nutri_values;
				}

				$results = $this->db->get('allergens_words')->result_array();
				$all_words = array();

				foreach ($results as $result){
					$all_words[] = $result['allergens_word'];
				}

				$data['all_words'] = $all_words;
				$data['is_fixed'] = $this->check_if_completed($pro_id);

				$data['semi_contains']= $this->M_fooddesk->semi_contains($pro_id);

				$data['fdd_contains']= $this->M_fooddesk->fdd_contains($pro_id);

				$data['own_contains']= $this->M_fooddesk->own_contains($pro_id);

				$semi_pro_arr = array();

				

				foreach ($data['semi_contains'] as $key=>$semi_p){
					if($semi_p['is_obs_product'] == 0){
						$this->fdb->where('p_id',$semi_p['fdd_pro_id']);
						$this->fdb->select( array( 's_name', $p_name ) );
						$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
						$res = $this->fdb->get('products')->result_array();
						if(!empty($res)){

							$data['semi_contains'][$key]['fabrikant'] = $res[0]['s_name'];
							$data['semi_contains'][$key]['p_name'] = $res[0][$p_name];
						}
					}else{
						
						$this->db->select('proname');
						$this->db->where('id',$semi_p['fdd_pro_id']);
						$fix_pro_names = $this->db->get('products')->result_array();
						$fix_pro_name = $fix_pro_names[0]['proname'];
						

						$data['semi_contains'][$key]['fabrikant'] = 'eigen';
						$data['semi_contains'][$key]['p_name'] = $fix_pro_name;
					}

					if(!in_array($semi_p['semi_product_id'], $semi_pro_arr)){
						array_push($semi_pro_arr, $semi_p['semi_product_id']);
					}
				}

				$semi_pro_array = array();
				
				foreach ($semi_pro_arr as $semi_id){
					$this->db->select('proname');
					$this->db->where('id',$semi_id);
					$semi_pro_names = $this->db->get('products')->result_array();

					$this->db->select_sum('quantity');
					$this->db->where(array('obs_pro_id'=>$pro_id,'semi_product_id'=>$semi_id));
					$sums = $this->db->get('fdd_pro_quantity')->result_array();

					$total = (!empty($sums))?$sums[0]['quantity']:0;

					$new_arr = array(
							'semi_id'=>$semi_id,
							'semi_name'=>$semi_pro_names[0]['proname'],
							'quant' => $total
					);

					array_push($semi_pro_array, $new_arr);
				}
				

				$data['semi_pro_array'] = $semi_pro_array;

				foreach ($data['fdd_contains'] as $key=>$semi_p){
					$this->fdb->where('p_id',$semi_p['fdd_pro_id']);
					$this->fdb->select( array('s_name', $p_name ));
					$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
					$res = $this->fdb->get('products')->result_array();
					if(!empty($res)){
						$data['fdd_contains'][$key]['fabrikant'] = $res[0]['s_name'];
						$data['fdd_contains'][$key]['p_name'] = $res[0][$p_name];
					}
				}

				foreach ($data['own_contains'] as $key=>$semi_p){
					
					$this->db->select('proname');
					$this->db->where('id',$semi_p['fdd_pro_id']);
					$fix_pro_names = $this->db->get('products')->result_array();
					$fix_pro_name = $fix_pro_names[0]['proname'];

					

					$data['own_contains'][$key]['fabrikant'] = 'eigen';
					$data['own_contains'][$key]['p_name'] = $fix_pro_name;
				}

				$all_id_arr = array();
				if(!empty($data['product_allergences'])){
					foreach ($data['product_allergences'] as $aller){
						$all_id_arr[] = $aller->ka_id;
					}
				}

				$data['allergence_words'] = array();
				foreach ($aller_arr as $val){
					if(in_array($val['all_id'],$all_id_arr))
						$data['allergence_words'][strtolower($val[$all_name])] = $val['all_id'];
				}
//	 			$this->load->view('cp/recipe_sheet_view', $data);
				if(empty($data['fixed']))
				{
					$product_allergences=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);
					if (!empty($product_allergences))
					{
						foreach ($product_allergences as $allergences_key=>$allergences_val)
						{
							$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
							$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
						}
					}
					$product_sub_allergences=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);
					if (!empty($product_sub_allergences))
					{
						foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
						{
							$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
							$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
						}
					}
					$all_aller = $this->Mproducts->get_ing_allergen($pro_id,$this->lang_u );
			 		foreach ($all_aller as $all_aller_k => $all_aller_v ) {
						$allergence_lang_d = 'allergence';
						$sub_allergence_d = 'sub_allergence';
			 			if ($this->lang_u == '_dch') {
			 				$allergence_lang_d = 'allergence_dch';
			 				$sub_allergence_d = 'sub_allergence_dch';

			 			}else if ($this->lang_u == '_fr') {
			 				$allergence_lang_d = 'allergence_fr';
			 				$sub_allergence_d = 'sub_allergence_fr';
			 			}

			 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
			 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
			 			}
			 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
			 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
			 			}
			 		}
					$all_allergence = array_unique($all_allergence);


					$final_all = array();
					foreach ($all_allergence as $key1 => $value1 ) {
						$ing_aller = explode('-', $value1);
						$final_all = array_merge($ing_aller, $final_all);
					}
					$final_all = array_unique($final_all);
					$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);
					if (empty($final_aller)) {
						$data['product_allergences'] = array_values(array_unique($product_allergence, SORT_REGULAR));
					}
					elseif (empty($product_allergence))
					{
						$data['product_allergences'] = array_values(array_unique($final_aller, SORT_REGULAR));
					}
					else{
						$data['product_allergences'] = array_merge($product_allergence,$final_aller);
						$data['product_allergences'] = array_values(array_unique($data['product_allergences'], SORT_REGULAR));
					}

					$final_sub_all = array();
					foreach ($all_sub_allergence as $key1 => $value1 ) {
						$ing_sub_aller = explode('-', $value1);
						$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
					}
					$final_sub_all = array_unique($final_sub_all);
					$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);
					if (empty($final_sub_aller)) {
						$data['product_sub_allergences'] = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
					}
					elseif (empty($product_sub_allergence))
					{
						$data['product_sub_allergences'] = array_values(array_unique($final_sub_aller, SORT_REGULAR));
					}
					else{
						$data['product_sub_allergences'] = array_merge($product_sub_allergence,$final_sub_aller);
						$data['product_sub_allergences'] = array_values(array_unique($data['product_sub_allergences'], SORT_REGULAR));
					}
				}

				$pdf_html = $this->load->view('cp/recipe_sheet_view', $data, true);

				require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');
				$report_name = 'report'.time().'.pdf';
				$mpdf=new mPDF('c');
				$mpdf->WriteHTML($pdf_html);

				$proname = stripslashes(trim(str_replace('/','',$data['product_information'][0]->proname)));
				$mpdf->Output(dirname(__FILE__).'/../../../assets/cp/rep_exp_files/recipe_import/'.$dir_name.'/Recipe-'.$proname.$pro_key.'.pdf', 'F');
			}

			$this->load->library('zip');
			$path = dirname(__FILE__).'/../../../assets/cp/rep_exp_files/recipe_import/';
			$this->zip->read_dir($path.$dir_name.'/',FALSE);
			$this->zip->archive($path.$dir_name.'.zip');
			echo $dir_name.'.zip';
		}
		else{
			echo false;
		}
	}


	function download_dat_file($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/digi_export/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_zen($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/zen_export/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_label($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/label_export/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_bizerba($filename = null){
		$filepath = base_url().'assets/CSV-Brizarba/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_all_recipe_sheets($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/recipe_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_all_technical_sheets($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/tech_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function download_dat_file_all_pdf($filename = null){
		$filepath = base_url().'assets/cp/rep_exp_files/print_all_report_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	private function check_if_completed($pro_id = 0){
		$complete = 1;

		if($pro_id != 0){
			if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				$this->db->select('direct_kcp,semi_product');
				$pro_arr = $this->db->get_where('products',array('id'=>$pro_id))->row_array();

				if(!empty($pro_arr)){
					if(($pro_arr['direct_kcp'] == 1) && ($pro_arr['semi_product'] == 0)){
						$this->db->where(array('obs_pro_id'=>$pro_id,'is_obs_product'=>0));
						$result = $this->db->get('fdd_pro_quantity')->result_array();
						if(empty($result)){
							$complete = 0;
						}
					}
					else{
						$this->db->where(array('obs_pro_id'=>$pro_id));
						$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
						if(!empty($result_custom)){
							foreach ($result_custom as $val){
								if($val['is_obs_product'] == 1){
									$complete = 0;
									break;
								}
							}
						}
						else{
							$complete = 0;
						}
					}
				}
			}
		}
		return $complete;
	}
}
?>