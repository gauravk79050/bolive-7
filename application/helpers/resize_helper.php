<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This helper function is used to resize images to the provided setting
 * @author Rishabh Chauhan <rishabhchauhan@cedcoss.com>
 */
function resize_images($type = null,$image_name = null,$is_refresh = false,$item_id = null){
	$CI =& get_instance();
	
	$dimensions = array(//Do not change indexes of this array as they are same as dirnames in assets/cp/images
		'company_img'		=> array(46,array(96,135),170,array(400,365)),//will generate images of dimensions 46*46 | 96*135 | 170*170 | 400*365
		'categories'		=> array(100),
		'subcategories'		=> array(100),
		'product'			=> array(60,100,270,600)//will generate images of dimensions 60*60 | 100*100 | 270*270
	);
	
	$db_details = array(//['TABLE_NAME','column in DB','prefix in image name']
		'company_img'	=> array('company','company_img',null),
		'categories'	=> array('categories','image','assets/cp/images/categories/'),
		'subcategories'	=> array('subcategories','subimage','assets/cp/images/subcategories/'),
		'product'		=> array('products','image',null)	
	);
	
	if($type && $image_name){
		$resize_dimensions = $dimensions[$type];
		
		switch($type){
			default:

				$is_pre_img		= false;
				$pre_img_name	= null;
				$srcpath = './assets/cp/images/'.$type.'/';
				
				//PREVIOUS IMAGE NAME
				if(isset($db_details[$type])){
				
					$db_name		=  isset($db_details[$type][0])?$db_details[$type][0]:null;
					$column_name	=  isset($db_details[$type][1])?$db_details[$type][1]:'image';
					$image_prefix	=  isset($db_details[$type][2])?$db_details[$type][2]:null;
					
					$CI->db->select($column_name);
					
					$pre_product_img = $CI->db->get_where($db_name,array('id'=>$item_id))->result();
					
					if($image_prefix){//for categories and subcategories as their name is stored as full path
						$prefix = $image_prefix;
						$str = $image_name;
							
						if (substr($str, 0, strlen($prefix)) == $prefix) {
							$str = substr($str, strlen($prefix));
						}
						
						if(isset($str)){
							$image_name	= $str;
						}
					}
					
					if(!empty($pre_product_img) && isset($pre_product_img[0]->{$column_name}) && $pre_product_img[0]->{$column_name}){
						$pre_img_name	= $pre_product_img[0]->{$column_name};
						
						if($image_prefix){//for categories and subcategories as their name is stored as full path
							$prefix = $image_prefix;
							$str = $pre_img_name;
								
							if (substr($str, 0, strlen($prefix)) == $prefix) {
								$str = substr($str, strlen($prefix));
							}
							
							if(isset($str)){
								$pre_img_name	= $str;
							}
						}
						
						if($pre_img_name != $image_name){
							$is_pre_img		= true;
							if(file_exists($srcpath.$pre_img_name)){
								unlink($srcpath.$pre_img_name);
							}
						}
					}
				}
				
				foreach($resize_dimensions as $dim){
					$width	= isset($dim[0])?$dim[0]:$dim;
					$height	= isset($dim[1])?$dim[1]:$dim;
						
					$newpath = './assets/cp/images/'.$type.'_'.$width.'_'.$height.'/';
					
					if(resize_image($image_name,$srcpath,$newpath,$width,$height,$is_refresh) && !$is_refresh && $item_id){
						switch($type){
							case 'nocase':
								break;
							default:
							//print_r($pre_product_img);die;
							if($is_pre_img && file_exists($newpath.$pre_img_name)){
								//echo $newpath.$pre_product_img[0]->image;die;
								unlink($newpath.$pre_img_name);
							}
							break;
						}
					}
				}
		}
		
	}
}

function resize_image($image_name = null,$srcpath = null,$newpath = null,$width = null,$height = null,$is_refresh = false){
	$response = false;
	$CI =& get_instance();
	
	if(isset($width) && isset($height) && file_exists($srcpath.$image_name) && (!$is_refresh || !file_exists($newpath.$image_name))){//if is refresh then check if file does not exists already
		$CI->load->library('image_lib');
		
		$config['width']			= $width;
		$config['height']			= $height;
		$config['new_image']		= $newpath.$image_name;
		$config['source_image']		= $srcpath.$image_name;
		//$config['image_library']	= 'gd2';
		$config['maintain_ratio']	= TRUE;
		
		$CI->image_lib->initialize($config);
		
		if ( ! $CI->image_lib->resize())//is_writable($newpath)
		{
			echo ' ERROR:(for image:-'.$image_name.') : ';
			echo $CI->image_lib->display_errors();
		}
		else{
			$response = true;
		}
		
		$CI->image_lib->clear();
		
		return $response;
	}
}

function delete_rsz_imgs($type = null, $item_id = null){
	$CI =& get_instance();

	$dimensions = array(//Do not change indexes of this array as they are same as dirnames in assets/cp/images
		'company_img'		=> array(46,array(96,135),170,array(400,365)),//will generate images of dimensions 46*46 | 96*135 | 170*170 | 400*365
		'categories'		=> array(100),
		'subcategories'		=> array(100),
		'product'			=> array(60,100,270,600)//will generate images of dimensions 60*60 | 100*100 | 270*270
	);
	
	$db_details = array(//['TABLE_NAME','column in DB','prefix in image name']
		'company_img'	=> array('company','company_img',null),
		'categories'	=> array('categories','image','assets/cp/images/categories/'),
		'subcategories'	=> array('subcategories','subimage','assets/cp/images/subcategories/'),
		'product'		=> array('products','image',null)	
	);

	if($type){
		$resize_dimensions = $dimensions[$type];

		switch($type){
			case 'categories'://Don't break continue to default
				//PRODUCT IMAGES ALREADY DELETED IN THE mcategories
				$CI->db->select('id');
				$products		= $CI->db->get_where('products',array('categories_id'=>$item_id))->result();
				foreach($products as $product){
					delete_rsz_imgs('product',$product->id);
				}
				
				$CI->db->select('id');
				$subcategories	= $CI->db->get_where('subcategories',array('categories_id'=>$item_id))->result();
				foreach($subcategories as $subcategory){
					delete_rsz_imgs('subcategories',$subcategory->id);
				}
				
			default:

				$is_pre_img		= false;
				$pre_img_name	= null;
				$srcpath = './assets/cp/images/'.$type.'/';

				//PREVIOUS IMAGE NAME
				if(isset($db_details[$type])){

					$db_name		=  isset($db_details[$type][0])?$db_details[$type][0]:null;
					$column_name	=  isset($db_details[$type][1])?$db_details[$type][1]:'image';
					$image_prefix	=  isset($db_details[$type][2])?$db_details[$type][2]:null;
						
					$CI->db->select($column_name);
						
						
					$pre_product_img = $CI->db->get_where($db_name,array('id'=>$item_id))->result();
						
						
					if(!empty($pre_product_img) && isset($pre_product_img[0]->{$column_name}) && $pre_product_img[0]->{$column_name}){
						$pre_img_name	= $pre_product_img[0]->{$column_name};

						if($image_prefix){//for categories and subcategories as their name is stored as full path
							$prefix = $image_prefix;
							$str = $pre_img_name;

							if (substr($str, 0, strlen($prefix)) == $prefix) {
								$str = substr($str, strlen($prefix));
							}
								
							if(isset($str)){
								$pre_img_name	= $str;
							}
						}

						if($pre_img_name){//if($pre_img_name != $image_name)
							$is_pre_img		= true;
							if(file_exists($srcpath.$pre_img_name)){
								unlink($srcpath.$pre_img_name);
							}
						}
					}
				}

				foreach($resize_dimensions as $dim){
					$width	= isset($dim[0])?$dim[0]:$dim;
					$height	= isset($dim[1])?$dim[1]:$dim;
					$newpath = './assets/cp/images/'.$type.'_'.$width.'_'.$height.'/';
						
					if($is_pre_img && file_exists($newpath.$pre_img_name)){
						unlink($newpath.$pre_img_name);
					}
				}
		}

	}
}

function refresh_all_images($type = null,$company_id = null){
	
	$CI =& get_instance();
	
	$db_details = array(//['TABLE_NAME','column in DB','prefix in image name']
		'company_img'	=> array('company','company_img',null),
		'categories'	=> array('categories','image','assets/cp/images/categories/'),
		'subcategories'	=> array('subcategories','subimage','assets/cp/images/subcategories/'),
		'product'		=> array('products','image',null)	
	);
	
	$images_array		= array();
	$column_name	= 'image';//column name in db
	
	if(isset($db_details[$type])){
		
		$db_name		=  isset($db_details[$type][0])?$db_details[$type][0]:null;
		$column_name	=  isset($db_details[$type][1])?$db_details[$type][1]:'image';
		$image_prefix	=  isset($db_details[$type][2])?$db_details[$type][2]:null;
		
		if($db_name){
			if($company_id){
				if($type != 'company_img'){
					$CI->db->where('company_id',$company_id);
				}
				else{
					$CI->db->where('id',$company_id);
				}
			}
			$CI->db->select($column_name);
			$CI->db->distinct($column_name);
			
			if($type == 'subcategories'){//no company id in subcategories 
				$CI->db->join('categories','categories.id = subcategories.categories_id');
			}
			
			$images_array = $CI->db->get($db_name)->result();
		}
		// echo $CI->db->last_query(); die;
		//echo count($images_array);
		/*$all_images_array = array();
		foreach($images_array as $images){
			$str = $images->{$column_name};
		   if($type == 'categories' || $type == 'subcategories'){
		    $col_prefix = $image_prefix;
		    
		    if (substr($str, 0, strlen($col_prefix)) == $col_prefix) {
		     $str = substr($str, strlen($col_prefix));
		    }
		    if(!in_array($str, $all_images_array)){
		     $unused_images_array[] = $str;
		    }
		    
		   }
		   $all_images_array[] = $str;
		}
		
		$prefix = './assets/cp/images/subcategories/';
		// $prefix = '';
		$tot_img = glob($prefix.'*.*');
		echo count($tot_img);
		$unused_images_array = array();
		foreach($tot_img as $filename){
			$str = $filename;
		
			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
			if(!in_array($str, $all_images_array)){
				$unused_images_array[] = $str;
			}
		}
		echo "--".count($unused_images_array);
		print_r($unused_images_array);die;*/
		
		$count = 1;
		foreach($images_array as $images){
			if(!empty($images) && $images->{$column_name} != ''){
				//$this->Mproducts->resize_product_images($images->image,true);
				resize_images($type,$images->{$column_name},true);
				$count++;
			}
		}
		// echo "--".$count;
	}
	else{
		echo "NO DATABASE DETAILS FOUND.";
	}	
}
//OBS ADMIN URLS 
//{base_url}cp/bestelonline/refresh_all_images/{type(:product:company_img:categories:subcategories)}[/{company_id(:105)}]