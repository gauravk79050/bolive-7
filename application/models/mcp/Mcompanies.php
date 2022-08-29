<?php
class MCompanies extends CI_Model
{
  function __construct()
  {
    // Call the Model constructor
    parent::__construct();
    $this->fdb = $this->load->database('fdb', TRUE);
  }

  /* query to insert data */
  function insert($result)
  {
    $this->db->insert('company', $result);
    $id = $this->db->insert_id();
    return $id;
  }

  function insert_feedback_ques_row($company_id, $comp_type_id)
  {
    $insert_array = array();
    if (!empty($comp_type_id)) {
      $ques_type_id = array();
      $quetion_type_ids = array('1', '8', '14');
      $type_id = explode('#', $comp_type_id);
      $flip_type_id = array_flip($type_id);
      if (!empty($flip_type_id)) {
        foreach ($flip_type_id as $key => $value) {
          if (in_array($key, $quetion_type_ids)) {
            array_push($ques_type_id, $key);
            break;
          }
        }
      }

      if (!empty($ques_type_id)) {
        if ($ques_type_id[0] == '14') {
          $insert_array = array(
            'company_id'        => $company_id,
            'first_question'       => 'How was the food?',
            'first_question_dch'     => 'Hoe was het eten?',
            'first_question_fr'     => 'Comment était la nourriture?',
            'second_question'       => 'How was the service?',
            'second_question_dch'     => 'Hoe was de service?',
            'second_question_fr'     => 'Comment était le service?',
            'third_question'       => 'How was the total experience?',
            'third_question_dch'     => 'Hoe was de totale ervaring?',
            'third_question_fr'     => "Comment était l'expérience total_rece",
            'first_question_title'     => 'FOODSCORE',
            'first_question_title_dch'   => 'FOODSCORE',
            'first_question_title_fr'   => 'ALIMENTAIRE',
            'second_question_title'   => 'SERVICE',
            'second_question_title_dch' => 'SERVICE',
            'second_question_title_fr'   => 'SERVICE',
            'third_question_title'     => 'EXPERIENCE',
            'third_question_title_dch'   => 'ERVARING',
            'third_question_title_fr'   => 'EXPÉRIENCE',
            'added_date_time'       => date('Y-m-d h:i:s')

          );
        } else if ($ques_type_id[0] == '1' || $ques_type_id[0] == '8') {
          $insert_array = array(
            'company_id'        => $company_id,
            'first_question'       => 'How are our products?',
            'first_question_dch'     => 'Hoe zijn onze producten?',
            'first_question_fr'     => 'Comment sont nos produits?',
            'second_question'       => 'How is the service?',
            'second_question_dch'     => 'Hoe is de service?',
            'second_question_fr'     => 'Comment était le service?',
            'third_question'       => 'How is the price/quality?',
            'third_question_dch'     => 'Hoe is de prijs/kwaliteit?',
            'third_question_fr'     => 'Comment est le prix/qualité?',
            'first_question_title'     => 'PRODUCTS',
            'first_question_title_dch'   => 'PRODUCTEN',
            'first_question_title_fr'   => 'PRODUCTS',
            'second_question_title'   => 'SERVICE',
            'second_question_title_dch' => 'SERVICE',
            'second_question_title_fr'   => 'SERVICE',
            'third_question_title'     => 'PRICE/QUALITY',
            'third_question_title_dch'   => 'Prijs/kwaliteit',
            'third_question_title_fr'   => 'PRIX/QUALITE',
            'added_date_time'       => date('Y-m-d h:i:s')

          );
        }
      } else {
        $insert_array = array(
          'company_id'        => $company_id,
          'first_question'       => 'How was the food?',
          'first_question_dch'     => 'Hoe was het eten?',
          'first_question_fr'     => 'Comment était la nourriture?',
          'second_question'       => 'How was the service?',
          'second_question_dch'     => 'Hoe was de service?',
          'second_question_fr'     => 'Comment était le service?',
          'third_question'       => 'How was the total experience?',
          'third_question_dch'     => 'Hoe was de totale ervaring?',
          'third_question_fr'     => "Comment était l'expérience total_rece",
          'first_question_title'     => 'FOODSCORE',
          'first_question_title_dch'   => 'FOODSCORE',
          'first_question_title_fr'   => 'ALIMENTAIRE',
          'second_question_title'   => 'SERVICE',
          'second_question_title_dch' => 'SERVICE',
          'second_question_title_fr'   => 'SERVICE',
          'third_question_title'     => 'EXPERIENCE',
          'third_question_title_dch'   => 'ERVARING',
          'third_question_title_fr'   => 'EXPÉRIENCE',
          'added_date_time'       => date('Y-m-d h:i:s')

        );
      }

      $this->db->insert('easybutler_feedback_questions', $insert_array);
    }
  }

  function update_feedback_ques_row($company_id, $comp_type_id)
  {

    $insert_array = array();
    if (!empty($comp_type_id)) {
      $ques_type_id = array();
      $quetion_type_ids = array('1', '8', '14');
      $type_id = explode('#', $comp_type_id);
      $flip_type_id = array_flip($type_id);
      if (!empty($flip_type_id)) {
        foreach ($flip_type_id as $key => $value) {
          if (in_array($key, $quetion_type_ids)) {
            array_push($ques_type_id, $key);
            break;
          }
        }
      }

      if (!empty($ques_type_id)) {
        if ($ques_type_id[0] == '14') {
          $update_array = array(

            'first_question'       => 'How was the food?',
            'first_question_dch'     => 'Hoe was het eten?',
            'first_question_fr'     => 'Comment était la nourriture?',
            'second_question'       => 'How was the service?',
            'second_question_dch'     => 'Hoe was de service?',
            'second_question_fr'     => 'Comment était le service?',
            'third_question'       => 'How was the total experience?',
            'third_question_dch'     => 'Hoe was de totale ervaring?',
            'third_question_fr'     => "Comment était l'expérience total_rece",
            'first_question_title'     => 'FOODSCORE',
            'first_question_title_dch'   => 'FOODSCORE',
            'first_question_title_fr'   => 'ALIMENTAIRE',
            'second_question_title'   => 'SERVICE',
            'second_question_title_dch' => 'SERVICE',
            'second_question_title_fr'   => 'SERVICE',
            'third_question_title'     => 'EXPERIENCE',
            'third_question_title_dch'   => 'ERVARING',
            'third_question_title_fr'   => 'EXPÉRIENCE',
            'updated_date_time'       => date('Y-m-d h:i:s')

          );
        } else if ($ques_type_id[0] == '1' || $ques_type_id[0] == '8') {
          $update_array = array(
            'first_question'       => 'How are our products?',
            'first_question_dch'     => 'Hoe zijn onze producten?',
            'first_question_fr'     => 'Comment sont nos produits?',
            'second_question'       => 'How is the service?',
            'second_question_dch'     => 'Hoe is de service?',
            'second_question_fr'     => 'Comment était le service?',
            'third_question'       => 'How is the price/quality?',
            'third_question_dch'     => 'Hoe is de prijs/kwaliteit?',
            'third_question_fr'     => 'Comment est le prix/qualité?',
            'first_question_title'     => 'PRODUCTS',
            'first_question_title_dch'   => 'PRODUCTEN',
            'first_question_title_fr'   => 'PRODUCTS',
            'second_question_title'   => 'SERVICE',
            'second_question_title_dch' => 'SERVICE',
            'second_question_title_fr'   => 'SERVICE',
            'third_question_title'     => 'PRICE/QUALITY',
            'third_question_title_dch'   => 'Prijs/kwaliteit',
            'third_question_title_fr'   => 'PRIX/QUALITE',
            'updated_date_time'       => date('Y-m-d h:i:s')

          );
        }
      } else {
        $update_array = array(

          'first_question'       => 'How was the food?',
          'first_question_dch'     => 'Hoe was het eten?',
          'first_question_fr'     => 'Comment était la nourriture?',
          'second_question'       => 'How was the service?',
          'second_question_dch'     => 'Hoe was de service?',
          'second_question_fr'     => 'Comment était le service?',
          'third_question'       => 'How was the total experience?',
          'third_question_dch'     => 'Hoe was de totale ervaring?',
          'third_question_fr'     => "Comment était l'expérience total_rece",
          'first_question_title'     => 'FOODSCORE',
          'first_question_title_dch'   => 'FOODSCORE',
          'first_question_title_fr'   => 'ALIMENTAIRE',
          'second_question_title'   => 'SERVICE',
          'second_question_title_dch' => 'SERVICE',
          'second_question_title_fr'   => 'SERVICE',
          'third_question_title'     => 'EXPERIENCE',
          'third_question_title_dch'   => 'ERVARING',
          'third_question_title_fr'   => 'EXPÉRIENCE',
          'updated_date_time'     => date('Y-m-d h:i:s')

        );
      }

      $this->db->where('company_id', $company_id);
      $this->db->update('easybutler_feedback_questions', $update_array);
    }
  }

  /* query to update data */
  function update($params = array())
  {
    $company = array();
    $text = array();

    if (!empty($params)) {
      if ($params['id']) {
        $sql = "UPDATE `company` SET ";

        $id1 = $params['id'];

        unset($params['id']);

        foreach ($params as $k => $v) {
          $text[] = '`' . $k . '`=' . $this->db->escape($v);
        }
        $r = implode(", ", $text);
        $sql = $sql . $r . " WHERE `id`= " . $id1;
        $execute = $this->db->query($sql);
        return 1;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


  function update_hide_bp_intro($update, $where)
  {
    $this->db->where('company_id', $where);
    $this->db->update('general_settings', $update);
  }

  function update_company($id, $update)
  {
    $this->db->where('id', $id);
    $result = $this->db->update('company', $update);
    return $result;
  }

  /**
   *This function is used to update details of meattime
   *@name update_meattime
   *@author Monu Singh Yadav <monuyadav@cedcoss.com>
   */
  // function update_meattime($id=0,$update=array())
  // {
  // 	$this->db->where('company_id',$id);
  // 	$this->db->update('meattime_settings',$update);
  // }


  /**
   *This function is used to update details of meattime
   *@name get_meattime
   *@author Monu Singh Yadav <monuyadav@cedcoss.com>
   */
  // function get_meattime($where=array())
  // {
  // 	return $this->db->get_where('meattime_settings',$where)->row_array();
  // }

  /* query to delete data */
  function delete($company_id = null)
  {
    /* ==before deleting a company delete all rows form the other tables listing below== */
    // delete from general settings
    if ($company_id && is_numeric($company_id)) {

      $this->db->delete('general_settings',       array('company_id' => $company_id));
      $this->db->delete('api',               array('company_id' => $company_id));
      $this->db->delete('pickup_delivery_timings',     array('company_id' => $company_id));
      $this->db->delete('order_settings',         array('company_id' => $company_id));
      $this->db->delete('desk_section_design',       array('company_id' => $company_id));
      $this->db->delete('desk_settings',        array('company_id' => $company_id));
      $this->db->delete('company_delivery_areas',     array('company_id' => $company_id));
      $this->db->delete('company_delivery_settings',   array('company_id' => $company_id));
      $this->db->delete('clients',             array('company_id' => $company_id));
      $this->db->delete('company_renewal',         array('company_id' => $company_id));
      $this->db->delete('company_language',       array('company_id' => $company_id));
      $this->db->delete('comments',            array('company_id' => $company_id));
      $this->db->delete('suggested_correction',      array('company_id' => $company_id));
      $this->db->delete('saved_orders',          array('company_id' => $company_id));
      $this->db->delete('opening_hours',        array('company_id' => $company_id));
      $this->db->delete('section_designs',         array('company_id' => $company_id));
      $this->db->delete('groups',             array('company_id' => $company_id));
      $this->db->delete('client_numbers',         array('company_id' => $company_id));
      $this->db->delete('company_css',           array('company_id' => $company_id));
      $this->db->delete('company_ftp_details',       array('company_id' => $company_id));
      $this->db->delete('correction_reports',       array('company_id' => $company_id));
      $this->db->delete('saved_reports',         array('company_id' => $company_id));
      $this->db->delete('delivery_areas',         array('company_id' => $company_id));
      $this->db->delete('mail_manager',         array('company_id' => $company_id));
      $this->db->delete('newsletters',           array('company_id' => $company_id));
      $this->db->delete('mail_manager_sent_mail',     array('company_id' => $company_id));
      $this->db->delete('order_details_bo',       array('company_id' => $company_id));
      $this->db->delete('orders_bo',           array('company_id' => $company_id));
      $this->db->delete('order_request_portal',     array('company_id' => $company_id));
      $this->db->delete('upgrade_requests',       array('company_id' => $company_id));
      $this->db->delete('widgets',             array('company_id' => $company_id));
      $this->db->delete('cp_merchant_info',       array('company_id' => $company_id));
      $this->db->delete('payment_transaction',      array('company_id' => $company_id));

      $orders_array = $this->db->where(array('company_id' => $company_id))->get('orders')->result();
      if (!empty($orders_array)) {
        foreach ($orders_array as $order) {
          $this->db->delete('order_details',     array('orders_id' => $order->id));
          $this->db->delete('orders',         array('id' => $order->id));
        }
      }

      $products_array = $this->db->where(array('company_id' => $company_id))->get('products')->result();
      if (!empty($products_array)) {
        foreach ($products_array as $product) {
          $this->db->delete('products_discount', array('products_id' => $product->id));
          $this->db->delete('groups_products', array('products_id' => $product->id));
          $this->db->delete('groups_order', array('products_id' => $product->id, 'company_id' => $company_id));
          $this->db->delete('products_allergence', array('product_id' => $product->id));
          // delete image of product from the upload folder//

          $filepath = dirname(__FILE__);
          $image_name = end(explode('/', $product->image));
          $output = @unlink($filepath . '/../../../assets/cp/images/product/' . $image_name);

          // =============================================//
          $this->db->delete('products',       array('id' => $product->id));
        }
      }

      $delivery_areas = $this->db->where(array('company_id' => $company_id))->get('delivery_areas')->result();
      if (!empty($delivery_areas)) {
        foreach ($delivery_areas as $delivery_area) {
          $this->db->delete('delivery_settings',   array('delivery_areas_id' => $delivery_area->id));
          $this->db->delete('delivery_areas',     array('id' => $delivery_area->id, 'company_id' => $company_id));
        }
      }

      $category_array = $this->db->where(array('company_id' => $company_id))->get('categories')->result();
      if (!empty($category_array)) {
        foreach ($category_array as $category) {
          $filepath = dirname(__FILE__);
          $subcategories = $this->db->get('subcategories',     array('categories_id' => $category->id))->result_array();
          if (!empty($subcategories)) {
            foreach ($subcategories as $subcategory) {
              @unlink($filepath . '/../../../' . $subcategory->subimage);
            }
          }
          $this->db->delete('subcategories',     array('categories_id' => $category->id));
          $this->db->delete('categories',       array('id' => $category->id, 'company_id' => $company_id));
          $output = @unlink($filepath . '/../../../' . $category->image);
        }
      }

      $menucard_setting = $this->db->where(array('company_id' => $company_id))->get('menucard_setting')->result();
      if (!empty($menucard_setting)) {
        foreach ($menucard_setting as $menucard_setting_key => $menucard_setting_val) {
          $menucard_categories = $this->db->where(array('menu_group_id' => $menucard_setting_val->menu_group_id))->get('menucard_categories')->result();
          if (!empty($menucard_categories)) {
            foreach ($menucard_categories as $menucard_categ_key => $menucard_categ_key_val) {
              $menucard_subcategories = $this->db->where(array('cat_id' => $menucard_categ_key_val->id))->get('menucard_subcategories')->result();
              if (!empty($menucard_subcategories)) {
                foreach ($menucard_subcategories as $key => $value) {
                  $this->db->delete('menucard_products', array('cat_id' => $menucard_categ_key_val->id, 'sub_cat_id' => $value->id));
                  $this->db->delete('menucard_subcategories', array('cat_id' => $menucard_categ_key_val->id));
                }
              } else {
                $this->db->delete('menucard_products', array('cat_id' => $menucard_categ_key_val->id));
              }
              $this->db->delete('menucard_categories', array('menu_group_id' => $menucard_setting_val->menu_group_id));
            }
          }
        }
        $this->db->delete('menucard_setting', array('company_id' => $company_id));
        $this->db->delete('menucard_layout_setting', array('company_id' => $company_id));
      }

      $division = $this->db->where(array('parent_id' => $company_id))->get('company')->result();

      if (!empty($division)) {
        foreach ($division as $key => $value) {
          $menucard_setting = $this->db->where(array('company_id' => $value->id))->get('menucard_setting')->result();
          if (!empty($menucard_setting)) {
            foreach ($menucard_setting as $menucard_setting_key => $menucard_setting_val) {
              $menucard_categories = $this->db->where(array('menu_group_id' => $menucard_setting_val->menu_group_id))->get('menucard_categories')->result();
              if (!empty($menucard_categories)) {
                foreach ($menucard_categories as $menucard_categ_key => $menucard_categ_key_val) {
                  $menucard_subcategories = $this->db->where(array('cat_id' => $menucard_categ_key_val->id))->get('menucard_subcategories')->result();
                  if (!empty($menucard_subcategories)) {
                    foreach ($menucard_subcategories as $key => $value) {
                      $this->db->delete('menucard_products', array('cat_id' => $menucard_categ_key_val->id, 'sub_cat_id' => $value->id));
                      $this->db->delete('menucard_subcategories', array('cat_id' => $menucard_categ_key_val->id));
                    }
                  } else {
                    $this->db->delete('menucard_products', array('cat_id' => $menucard_categ_key_val->id));
                  }
                  $this->db->delete('menucard_categories', array('menu_group_id' => $menucard_setting_val->menu_group_id));
                }
              }
            }
            $this->db->delete('menucard_setting', array('company_id' => $value->id));
            $this->db->delete('menucard_layout_setting', array('company_id' => $value->id));
          }
          $this->db->delete('company', array('id' => $value->id));
          $this->db->delete('company', array('id' => $value->id));
        }
      }

      /* ================================================================================ */
      $return_data = $this->db->delete('company', array('id' => $company_id));

      return $return_data;
    } else {
      return false;
    }
  }

  function search_company($params)
  {
    if ($params['search_by'] == 'id') {
      $query = $this->db->get_where('company', array(
        $params['search_by'] => $params['search_keyword']
      ));
    } elseif ($params['search_by'] == 'company_name' || $params['search_by'] == 'email' || $params['search_by'] == 'username' || $params['search_by'] == 'city') {
      $this->db->where("`approved`='1' AND (`" . $params['search_by'] . "` LIKE '%" . $params['search_keyword'] . "%')", NULL, FALSE);

      if ($params['ac_type_id'])
        $this->db->where('ac_type_id', $params['ac_type_id']);

      if ($params['order_by']) {
        $order = 'desc';
        if ($params['order_by'] == 'id' || $params['order_by'] == 'city')
          $order = 'asc';

        $this->db->order_by($params['order_by'], $order);
      } else
        $this->db->order_by('id', 'desc');

      $query = $this->db->get('company');
    }

    return $query->result();
  }

  function get_division($params = array())
  {
    $this->db->where($params);
    $this->db->where('role', 'division');
    $query = $this->db->get('company');
    return $query->result();
  }

  function get_subadmins($parent_id)
  {
    // $this->db->from('company');
    $this->db->where('parent_id', $parent_id);
    $this->db->where('role', 'sub');
    return $this->db->count_all_results('company');
  }


  function get_company($params = array(), $orderby = array(), $return_type = '', $start = 0, $limit = 0)
  {
    $company = array();
    $text = array();
    $orderbyarr = array();
    $sql = " SELECT company.*,`company_type`.company_type_name,`account_type`.ac_title FROM `company`
		JOIN `company_type` ON `company`.type_id = `company_type`.id
		JOIN `account_type` ON `company`.ac_type_id = `account_type`.id ";

    if (!empty($params) && isset($params['flag']) && $params['flag'] == "1") {
      $sql = " SELECT `company`.mailing_package, `company`.id,`company`.client_no,`company`.role,`company`.trial,`company`.type_id, `company`.status,`company`.username,`company`.password,`company`.ac_type_id,`company`.data_type,`company`.obsdesk_status,`company`.company_name,`company`.city,`company`.registration_date,`company`.address,`company`.zipcode,`company`.phone,`company`.email,`company`.first_name,`company`.last_name,`company`.excel_import_file_name,`company`.tv_id,`company`.on_trial,`company`.partner_invoice_date,`company`.invoice_end_date,`company`.partner_status,`company`.partner_message,`company`.reseller_remarks, `company_type`.company_type_name,`company`.shop_version, `company`.fdd_tv, `account_type`.ac_title,`general_settings`.`shop_testdrive`,`general_settings`.`language_id`,`company`.third_pary_con,`company`.expiry_date,`company`.last_login,`company`.system_selected,`company`.hide_recipe_in_cp,whr_get_info FROM `company`
			JOIN `company_type` ON `company`.type_id = `company_type`.id
			JOIN `general_settings` ON `company`.id = `general_settings`.company_id
			JOIN `account_type` ON `company`.ac_type_id = `account_type`.id ";
      unset($params['flag']);
    }

    $like_string = '';
    if (!empty($params) && isset($params['like_columns']) && isset($params['like_value'])) {
      $search_value = $params['like_value'];
      $like_string = " AND `company`." . $params['like_columns'] . " LIKE '%" . mysqli_real_escape_string($this->db->conn_id, $search_value) . "%'";
      unset($params['like_columns']);
      unset($params['like_value']);
    }

    if (!empty($params) && isset($params['partner_id'])) {
      $partner_id = $params['partner_id'];
      $search_partner_id = str_replace($params['partner_id'], "\"$partner_id\"", $params['partner_id']);
      $like_string .= " AND `company`.partner_id LIKE '%" . mysqli_real_escape_string($this->db->conn_id, $search_partner_id) . "%'";
      unset($params['partner_id']);
    }

    if (!empty($params)) {
      if (!empty($params) && isset($params['invoice_end_date'])) {
        $text[] = "`company`.`invoice_end_date` " . $params['invoice_end_date'];
        unset($params['invoice_end_date']);
      }
      foreach ($params as $k => $v) {
        if ($k != 'partner_id') {
          $text[] = 'company.' . $k . '="' . $v . '"';
        }
      }
      $r = implode(" AND ", $text);
      $sql = $sql . " WHERE " . $r;
    }

    $sql = $sql . $like_string;

    if (sizeof($orderby) > 0) {
      foreach ($orderby as $k => $v) {
        $orderbyarr[] = '`company`.' . $k . ' ' . $v;
      }
      $sql .= ' ORDER BY ' . implode(', ', $orderbyarr);
    }

    if ($start || $limit) {
      if ($start && $limit)
        $sql .= ' LIMIT ' . $start . ',' . $limit;
      else if ($limit)
        $sql .= ' LIMIT ' . $limit;
    }

    $execute = $this->db->query($sql);

    if ($execute->num_rows() > 0) {
      if ($return_type == 'ARRAY') {
        foreach ($execute->result_array() as $row) {

          // $row['id'] = $row['company_id'];

          if ($row['role'] == 'super') {
            $childs = $this->get_company(array(
              'parent_id' => $row['id']
            ));

            if (!empty($childs))
              $row['children'] = $childs[0];
          }

          $company[] = $row;
        }
      } else {
        foreach ($execute->result() as $row) {

          // $row->id = $row->company_id;

          if (isset($row->role)  && $row->role == 'super') {
            $childs = $this->get_company(array(
              'parent_id' => $row->id
            ));
            if (!empty($childs)) {
              foreach ($childs as $key => $value) {
                $childs_sub = $this->get_company(array(
                  'parent_id' => $value->id
                ));

                $childs[$key]->company_name = $childs[$key]->company_name . ' (' . count($childs_sub) . ' )';
              }
            }
            $row->children = $childs;
          }

          $company[] = $row;
        }
      }
    }

    foreach ($company as $comp) {
      $id = $comp->id;
      $this->db->select('id');
      $this->db->where('company_id', $id);
      $res = $this->db->get('products')->result_array();
      $arr = array_column($res, 'id');
      $total_product = count($res);
      $comp->total_product = $total_product;
      $total_rece = 0;
      if (!empty($arr)) {
        $this->db->distinct();
        $this->db->select('obs_pro_id');
        $this->db->group_start();
        $products_id = array_chunk($arr, 500);
        foreach ($products_id as $p_ids) {
          $this->db->where_in('obs_pro_id', $p_ids);
        }
        $this->db->group_end();
        $count = $this->db->get('fdd_pro_quantity')->result_array();
        $total_rece = count($count);
      }
      $comp->total_rece = $total_rece;
      $lang_id = $this->db->get('general_settings', array('company_id' => $id))->row_array();
      if (!empty($lang_id)) {
        $comp->current_lang = $lang_id['language_id'];
      } else {
        $comp->current_lang = 2;
      }
    }
    return $company;
  }

  function get_company_system_info()
  {
    $query = $this->db->get('company_system')->result_array();
    return $query;
  }

  function get_companies_without_logo()
  {
    $this->db->select(array('company.*', 'company_type.company_type_name', 'account_type.ac_title', 'country.country_name'));
    $this->db->join('company_type', 'company.type_id=company_type.id');
    $this->db->join('account_type', 'company.ac_type_id=account_type.id');
    $this->db->join('country', 'company.country_id=country.id');

    $this->db->where("(company.ac_type_id='5' OR company.ac_type_id='6' OR company.ac_type_id='4') AND (company.obsdesk_status = '1') AND (company.obsdesk_logo = '')");
    return $this->db->get('company')->result_array();
  }

  function get_account_types($params = array())
  {
    $this->db->flush_cache();
    if (!empty($params)) {
      foreach ($params as $col => $val)
        $this->db->where($col, $val);
    }

    $query = $this->db->get('account_type');

    if ($query->num_rows() >= 1) {
      return $query->result();
    } else {
      return false;
    }
  }

  /**
   * Validate user login
   *
   * @access public
   */
  function validateCompany()
  {
    if (!empty($_GET) && $_GET['username'] != NULL && $_GET['direct_login'] != NULL) {
      //$this->db->where('direct_login_id',$_GET['direct_login']);
      $this->db->where('username', $_GET['username']);
      $row = $this->db->get('company')->row();
      if (md5($row->password) == $_GET['direct_login']) {
        return $row;
      }
    } else {
      $this->db->where('username', $this->input->post('username'));
      $query = $this->db->get('company');
      if ($query->num_rows() == 1 && password_verify($this->input->post('password'), $query->row()->password)) {
        return $query->row();
      }
    }

    return false;
  }

  function get_companies_expiring_this_month()
  {
    $company = array();

    $date = date('Y-m-d', time());

    $sql = "SELECT `company`.*, `company_type`.company_type_name FROM `company` JOIN `company_type` ON `company`.type_id = `company_type`.id WHERE `ac_type_id` <> 1 AND `expiry_date` BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 30 DAY)";

    $execute = $this->db->query($sql);

    if ($execute->num_rows() > 0) {
      foreach ($execute->result() as $row) {
        $company[] = $row;
      }
    }

    return $company;
  }

  function get_companies_expiring_next_month()
  {
    $company = array();

    $date = date('Y-m-d', time());

    $sql = "SELECT `company`. * ,`company_type`.company_type_name FROM `company` JOIN `company_type` ON `company`.type_id = `company_type`.id WHERE `ac_type_id` <> 1 AND `expiry_date` BETWEEN DATE_ADD(NOW(),INTERVAL 30 DAY) AND DATE_ADD(NOW(),INTERVAL 60 DAY)";

    $execute = $this->db->query($sql);

    if ($execute->num_rows() > 0) {
      foreach ($execute->result() as $row) {
        $company[] = $row;
      }
    }

    return $company;
  }

  /* ======function called when user clickes for forgot password====== */
  function forgot_password($email)
  {
    if ($email) {
      $result = $this->db->where('email', $email)->get('company')->result();
      if ($result != array()) {
        $details_array = array(
          'first_name' => $result[0]->first_name,
          'last_name' => $result[0]->last_name,
          'username' => $result[0]->username,
          'password' => $result[0]->password,
          'email' => $result[0]->email
        );
        $return_data = array(
          'success' => $details_array
        );
      } else {
        $return_data = array(
          'error' => _('The specified email address is not in our database.')
        );
      }
    } else {
      $return_data = array(
        'error' => _('Didn\'t Receive Any Email id!Plese enter Email Id Again')
      );
    }
    return $return_data;
  }

  /* =============================================================== */
  function get_approved_company_ftp_settings()
  {
    $this->db->select('company.id as cmp_id, company.company_name, company.first_name, company.last_name, company.email, company.status, company.username, company.password, company.approved, company_ftp_details.id as ftp_id, company_ftp_details.shop_url, company_ftp_details.ftp_hostname , company_ftp_details.access_permission');
    $this->db->where("`company`.`approved`='1' AND (`company`.`role`='master' OR `company`.`role`='super')", NULL, FALSE);
    $this->db->join('company_ftp_details', 'company_ftp_details.company_id = company.id', 'left');
    $this->db->order_by('cmp_id', 'desc');
    $companies = $this->db->get('company')->result();

    if (!empty($companies))
      return $companies;
    else
      return false;
  }

  function get_approved_company_ftp_settings_new()
  {
    $this->db->select('company.id as cmp_id, company.company_name, company.first_name, company.last_name, company.email, company.status, company.username, company.password, company.approved, company_ftp_details.id as ftp_id, company_ftp_details.shop_url, company_ftp_details.ftp_hostname , company_ftp_details.access_permission');
    $this->db->where("`company`.`approved`='1' AND `company`.`status`='1' AND `company`.`shop_version`='2' AND (`company`.`role`='master' OR `company`.`role`='super')", NULL, FALSE);
    $this->db->join('company_ftp_details', 'company_ftp_details.company_id = company.id', 'left');
    $this->db->order_by('cmp_id', 'desc');
    $companies = $this->db->get('company')->result();

    if (!empty($companies))
      return $companies;
    else
      return false;
  }

  function get_approved_company_api()
  {
    $this->db->select('company.id as cmp_id, company.company_name, company.first_name, company.last_name, company.email, company.status, company.username, company.password, company.approved, api.api_id, api.api_secret, api.domain, api.company_id');

    // $this->db->where('company.approved','1');
    // $this->db->where('company.role','master');
    // $this->db->or_where('company.role','super');

    $this->db->where("`company`.`approved`='1'", NULL, FALSE);

    $this->db->join('api', 'api.company_id = company.id', 'left');

    $this->db->order_by('cmp_id', 'desc');

    $companies = $this->db->get('company')->result();

    if (!empty($companies))
      return $companies;
    else
      return false;
  }

  function search_approved_company_api($params)
  {
    if ($params['search_by'] == 'id') {

      $this->db->select('company.id as cmp_id, company.company_name, company.first_name, company.last_name, company.email, company.status, company.username, company.password, company.approved, api.api_id, api.api_secret, api.domain, api.company_id');
      $this->db->where("`company`.`approved`='1' AND (`company`.`" . $params['search_by'] . "` = '" . $params['search_keyword'] . "')", NULL, FALSE);
      $this->db->join('api', 'api.company_id = company.id', 'left');
      $this->db->order_by('cmp_id', 'desc');
      $query = $this->db->get('company');
    } elseif ($params['search_by'] == 'company_name' || $params['search_by'] == 'email' || $params['search_by'] == 'username' || $params['search_by'] == 'city') {

      $this->db->select('company.id as cmp_id, company.company_name, company.first_name, company.last_name, company.email, company.status, company.username, company.password, company.approved, api.api_id, api.api_secret, api.domain, api.company_id');
      $this->db->where("`company`.`approved`='1' AND (`company`.`" . $params['search_by'] . "` LIKE '%" . $params['search_keyword'] . "%')", NULL, FALSE);
      $this->db->join('api', 'api.company_id = company.id', 'left');
      $this->db->order_by('cmp_id', 'desc');

      $query = $this->db->get('company');
    }

    return $query->result();
  }

  function genRandomString($length = 10, $type = 'Both')
  {
    $string = '';

    if ($type == 'Both')
      $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    elseif ($type == 'Num')
      $characters = '0123456789';
    elseif ($type == 'Str')
      $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    for ($p = 0; $p < $length; $p++) {
      $string .= @$characters[mt_rand(0, strlen($characters))];
    }

    return $string;
  }

  function generateApi($company_id)
  {
    do {
      $api_secret = $this->genRandomString(10, 'Both');
      $this->db->where('api_secret', $api_secret);
      $api1 = $this->db->get('api')->result();
    } while (!empty($api1) || strlen($api_secret) < 10);

    do {
      $api_id = $this->genRandomString(6, 'Num');
      $this->db->where('api_id', $api_id);
      $api2 = $this->db->get('api')->result();
    } while (!empty($api2) || strlen($api_id) < 6);

    $this->db->where('id', $company_id);
    $company = $this->db->get('company')->row();

    $domain = '';
    if (!empty($company)) {
      $website = ($company->website) ? ($company->website) : ($company->domain);
      $url = parse_url($website); // , PHP_URL_HOST);

      // print_r($url);

      $domain = str_replace('www.', '', isset($url['path']) ? $url['path'] : $url['host']);
    }

    // echo $api_secret.'--'.$api_id.'--'.$domain;
    // die();

    $insert = array();

    $insert['api_id'] = $api_id;
    $insert['api_secret'] = $api_secret;
    $insert['domain'] = $domain;
    $insert['company_id'] = $company_id;

    $this->db->insert('api', $insert);
    return $api = $this->db->insert_id();
  }

  function get_posted_suggestions()
  {
    $this->db->select('correction_reports.*, company.id as `company_id`, company.company_name,company.username,company.password');
    $this->db->join('company', 'company.id = correction_reports.company_id', 'left');
    $reports = $this->db->get('correction_reports');
    $reports = $reports->result();

    return $reports;
  }

  function block_ip($ip_address)
  {
    $this->db->insert('block_ips', array(
      'ip_address ' => $ip_address
    ));
    return $api = $this->db->insert_id();
  }

  function delete_report($report_id)
  {
    $this->db->where(array(
      'id' => $report_id
    ));
    return $this->db->delete('correction_reports');
  }

  function update_trial($data, $company_id)
  {
    $this->db->where('id', $company_id);
    $this->db->update('company', $data);
    return true;
  }

  function fetching_company_details($value)
  {
    $this->db->like('company_name', $value);
    $this->db->order_by("company_name", "asc");
    $query = $this->db->get('company')->result_array();
    return $query;
  }

  function update_trial_sent_mail($data, $company_id)
  {
    $this->db->where('id', $company_id);
    $this->db->update('company', $data);
    return true;
  }

  function get_company_trial()
  {
    $this->db->where_not_in('trial_mail_sent', '1');
    $query = $this->db->get('company')->result_array();
    return $query;
  }

  function update_on_trial($data, $company_id)
  {
    $this->db->where('id', $company_id);
    $this->db->update('company', $data);
    return true;
  }

  /**
   * This model function is used to fetch number of orders in last 30 days
   *
   * @param int $companyId
   *        	Company ID for which order count is required
   * @return int Orders count
   */
  function last_30_days_order($companyId = null)
  {
    $this->db->where("company_id", $companyId);
    $this->db->where('`created_date` BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
    return $this->db->count_all_results("orders");
  }

  /**
   * This function is used to count all companies
   * @param array $where_array Array of conditions
   */
  function get_company_count($where_array = array())
  {
    if (!empty($where_array)) {
      foreach ($where_array as $col => $val)
        $this->db->where($col, $val);
    }

    $total_companies = $this->db->count_all_results('company');
    return $total_companies;
  }

  function update_remark($company_id = null, $remark)
  {
    $response = false;
    if ($company_id) {
      $this->db->where(array('id' => $company_id));
      $this->db->set('reseller_remarks', $remark);
      if ($this->db->update('company')) {
        $response = true;
      }
    }
    return $response;
  }

  function get_empty_recipes_xls($company_id)
  {
    $this->db->select('name,id');
    $this->db->where('company_id', $company_id);
    $cat = $this->db->get('categories');
    $cat_arr = $cat->result_array();

    foreach ($cat_arr as $key => $value) {
      $category[$value['id']] = $value['name'];
    }

    $prod = array();
    $prod_cat_without_recepi = array();
    $prod_without_recepi = array();

    foreach ($cat_arr as $key) {
      $this->db->select('proname,id,company_id,subcategories_id,categories_id');
      $this->db->where(array('categories_id' => $key['id'], 'company_id' => $company_id, 'direct_kcp' => 0));
      $prod[] = $this->db->get('products')->result_array();
    }

    foreach ($prod as $key => $value) {
      foreach ($value as $key1 => $value1) {
        $this->db->where('obs_pro_id', $value1['id']);
        $recipe_product = $this->db->get('fdd_pro_quantity')->result_array();
        if (empty($recipe_product)) {
          $prod_without_recepi[] = $value1;
        }
      }
    }
    $products = array();
    $products = array('0' => $category, '1' => $prod_without_recepi);
    return $products;
  }

  function delete_banner($banner_name, $company_id, $table_column)
  {
    $this->db->where('company_id', $company_id);
    $this->db->where($table_column, $banner_name);
    return $this->db->update('general_settings', array($table_column => ''));
  }

  function get_company_noimages()
  {
    $acc_type = array('4', '5', '6', '7');
    $use_type_id = array('2', '11', '14', '15', '16', '17', '18', '21', '22');
    $this->db->select('company.id,company.company_name,company.type_id,company.website,company.city,company.email,company.registration_date,company.ac_type_id,company.email,general_settings.aller_banner_sheet,general_settings.aller_upload_image');
    $this->db->group_start();
    $this->db->where('general_settings.aller_banner_sheet', '');
    $this->db->or_where('general_settings.aller_upload_image', '');
    $this->db->group_end();
    $this->db->where_in('company.ac_type_id', $acc_type);
    $this->db->join('general_settings', 'company.id = general_settings.company_id', 'left');
    $reports = $this->db->get('company');
    $reports = $reports->result();
    if (!empty($reports)) {
      foreach ($reports as $key => $value) {
        if (strpos($value->type_id, '#') !== false) {
          $type_id = explode('#', $value->type_id);
          $shop_name = array();
          foreach ($type_id as $k => $t_id) {
            if (in_array($t_id, $use_type_id)) {
              $this->db->select('company_type_name');
              $this->db->where('id', $t_id);
              $company_type = $this->db->get('company_type')->row();
              array_push($shop_name, $company_type->company_type_name);
              $reports[$key]->type_id = $t_id;
            }
          }
          if (empty($shop_name)) {
            unset($reports[$key]);
          }
        } else {
          if (in_array($value->type_id, $use_type_id)) {
          } else {
            unset($reports[$key]);
          }
        }
      }
      //print_r(array_values($reports));die;
    }
    return $reports;
  }
  function get_company_type()
  {
    $this->db->select('*');
    $res = $this->db->get('company_type');
    $comp_types = $res->result();
    return $comp_types;
  }
  function get_light_admins()
  {
    $use_type_id = array('2', '11', '14', '15', '16', '17', '18', '21', '22');
    $this->db->select('*');
    $this->db->where('ac_type_id', '7');
    $this->db->where('backlink !=', '');
    $this->db->or_where('soc_med_link !=', '');
    $reports = $this->db->get('company');
    $reports = $reports->result();
    if (!empty($reports)) {
      foreach ($reports as $key => $value) {
        if (strpos($value->type_id, '#') !== false) {
          $type_id = explode('#', $value->type_id);
          $shop_name = array();
          foreach ($type_id as $k => $t_id) {
            if (in_array($t_id, $use_type_id)) {
              $this->db->select('company_type_name');
              $this->db->where('id', $t_id);
              $company_type = $this->db->get('company_type')->row();
              array_push($shop_name, $company_type->company_type_name);
              $reports[$key]->type_id = $t_id;
            }
          }
          if (empty($shop_name)) {
            unset($reports[$key]);
          }
        } else {
          if (in_array($value->type_id, $use_type_id)) {
          } else {
            unset($reports[$key]);
          }
        }
      }
      //print_r(array_values($reports));die;
    }
    return $reports;
  }

  function get_assignee($start = '', $limit = '')
  {
    if ($start != '' && $limit != '')
      $this->db->limit($limit, $start);
    $this->db->select('id, company_name, first_name, last_name, email, phone, username, password');
    $this->db->where('approved', '1');
    $this->db->where('role', 'assignee');
    return $this->db->get('company')->result_array();
  }

  function get_all_languags()
  {
    $this->db->select('id, lang_code');
    return $this->db->get('language')->result();
  }

  function update_tv_webshop_status($comp_id, $column, $status)
  {
    $this->db->where('id', $comp_id);
    return $this->db->update('company', array($column => $status));
  }

  function get_all_third_party()
  {
    $this->fdb->select('id,name');
    return  $this->fdb->get('partners')->result_array();
  }

  /**
   *
   * Function to get the theme as per company type
   *
   */
  function get_theme_as_per_type_id($type_id)
  {
    if ($type_id) {
      $type_id = explode('#', $type_id);
      $type_id = $type_id[0];

      $this->db->select('theme');
      $this->db->where('id', $type_id);
      return $this->db->get('company_type')->row_array();
    }
  }
  function get_mailing_package()
  {
    $mailing = $this->db->get('mailing_package')->result_array();
    return $mailing;
  }
  function get_total_email($company_id = '')
  {

    $this->db->select('easybutler_feedback_emails.id,easybutler_feedback_emails.send_to,GROUP_CONCAT(easybutler_feedback_emails.feedback_id SEPARATOR "#") as feedback_ids,GROUP_CONCAT(easybutler_rating.allow_promo_notifi SEPARATOR "#") as allow_promo_notifi');
    $this->db->where('easybutler_feedback_emails.feedback_id !=', '0');
    $this->db->join('easybutler_rating', 'easybutler_rating.id = easybutler_feedback_emails.feedback_id');
    $this->db->where(array('easybutler_feedback_emails.company_id' => $company_id));
    $this->db->group_by('easybutler_feedback_emails.send_to');
    $this->db->order_by('easybutler_feedback_emails.send_to', 'ASC');
    return $this->db->get('easybutler_feedback_emails')->result_array();
  }

  function update_admin($upd_arr, $admin_id)
  {
    $this->db->where('id', $admin_id);
    $this->db->update('admin', $upd_arr);
  }

  function get_admin_data($id)
  {
    return $this->db->get_where('admin', array('id' => $id))->row_array();
  }

  function check_email_comp($whr)
  {
    return $this->db->get_where('company', $whr)->result_array();
  }
  function update_all_check($company_id)
  {
    if (isset($company_id)) {
      $this->db->where(array('company_id' => $company_id));
      $this->db->update('allergenkaart_design', array('allergencard_activated' => 1));
    }
  }

  function store_token($com_id)
  {
    $token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 50);
    $this->db->select('count(*) as count');
    $count = $this->db->get_where('password_token', ['comp_id' => $com_id])->row()->count;
    if ($count == 1) {
      $this->db->set(array('token' => $token));
      $this->db->where('comp_id', $com_id);
      if ($this->db->update('password_token')) {
        return $token;
      }
    } else if ($count == 0 || $count == false) {
      $data = array(
        'comp_id' => $com_id,
        'token'    => $token,
      );
      if ($this->db->insert('password_token', $data)) {
        return $token;
      }
    }
    return false;
  }

  function get_total_pws_and_fav($company_id)
  {
    $total     = 0;
    $totalpws   = 0;

    $this->db->select('fdd_pro_id, obs_pro_id');
    $this->db->where('company_id', $company_id);
    $pro_ids = $this->db->get('fdd_pro_fav')->row_array();

    if (!empty($pro_ids) && !empty(json_decode($pro_ids['fdd_pro_id']))) {
      $fdd_ids = json_decode($pro_ids['fdd_pro_id']);
      foreach ($fdd_ids as $exist_key => $exist_val) {

        $this->fdb->select('products.p_id');
        $this->fdb->join('gs1_products', 'gs1_products.p_id = products.p_id', 'left');
        $this->fdb->join('ps1_products', 'ps1_products.p_id = products.p_id', 'left');
        $this->fdb->where('products.p_id', $exist_val->fdd_pro_id);
        $query = $this->fdb->get('products')->result_array();

        if (empty($query)) {
          continue;
        }

        $exploded_data = explode(',', $exist_val->supplier_id);
        foreach ($exploded_data as $exp_k => $exp_v) {
          $total = $total + 1;
        }
      }
    }
    $query1 = array();

    if (!empty($pro_ids) && !empty(json_decode($pro_ids['obs_pro_id']))) {
      $obs_ids = json_decode($pro_ids['obs_pro_id']);

      $this->db->distinct();
      $this->db->select('products.id as p_id');
      $this->db->join('products_pending', 'products_pending.product_id = products.id');
      $this->db->where('products_pending.company_id', $company_id);
      $this->db->group_start();
      $obs_ids = array_chunk($obs_ids, 500);
      foreach ($obs_ids as $key => $value) {
        $this->db->or_where_in('products.id', $value);
      }
      $this->db->group_end();
      $query1 = $this->db->get('products')->result_array();
    }
    $totalpws = count($query1);
    return $total + $totalpws;
  }

  function update_products_as_per_compnay_types($data = array())
  {
    $method         = $data['data_type'];
    $company_id       = $data['id'];
    $res           = array();

    $db_fdd         = $this->config->item('db_fdd');
    $db_obs         = $this->config->item('db_obs');
    $this->db->select('data_type');
    $this->db->where('id', $company_id);
    $old_comp_type = $this->db->get('company')->result_array();

    $fdd_result = $this->db->query("SELECT DISTINCT (`" . $db_fdd . "`.`products`.`p_id`) , `" . $db_fdd . "`.`products`.`priority` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_fdd . "`.`products`.`p_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE  `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '0' AND `" . $db_obs . "`.`company`.`id` = '" . $company_id . "'")->result_array();

    $obs_result = $this->db->query("SELECT DISTINCT `" . $db_fdd . "`.`products`.`p_id` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`pws_products_sheets` ON `" . $db_obs . "`.`pws_products_sheets`.`data_sheet` = `" . $db_fdd . "`.`products`.`data_sheet`JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_obs . "`. `pws_products_sheets`.`obs_pro_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE  `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '1' AND `" . $db_obs . "`.`company`.`status` = '1' AND `" . $db_obs . "`.`pws_products_sheets`.`checked` = '0' AND `" . $db_obs . "`.`company`.`id` = '" . $company_id . "'")->result_array();

    $products =  array_merge($fdd_result, $obs_result);
    if (!empty($products)) {
      $products = array_column($products, 'p_id');
    }

    $Where = '';
    if (!empty($products)) {
      $Where = 'AND ';
      $p_ids_chunk = array_chunk($products, 500);
      $Where .= '(';
      foreach ($p_ids_chunk as $p_ids) {
        $Where .= '`' . $db_fdd . '`.`products`.`p_id` IN (';
        $Where .= implode(', ', $p_ids);
        $Where .= ') OR ';
      }
      $Where = chop($Where, " OR ");
      $Where .= ')';
    } else {
      $Where = '';
    }

    if ($method == 'basic') {
      $result = $this->db->query("SELECT DISTINCT `" . $db_fdd . "`.`products`.`p_id` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_fdd . "`.`products`.`p_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '0' AND `" . $db_obs . "`.`company`.`data_type` = 'premium' AND `" . $db_obs . "`.`company`.`id` != '" . $company_id . "' $Where")->result_array();

      $result1 = $this->db->query("SELECT DISTINCT `" . $db_fdd . "`.`products`.`p_id` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`pws_products_sheets` ON `" . $db_obs . "`.`pws_products_sheets`.`data_sheet` = `" . $db_fdd . "`.`products`.`data_sheet`JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_obs . "`. `pws_products_sheets`.`obs_pro_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '1' AND `" . $db_obs . "`.`company`.`status` = '1' AND `" . $db_obs . "`.`company`.`data_type` = 'premium' AND `" . $db_obs . "`.`pws_products_sheets`.`checked` = '0' AND `" . $db_obs . "`.`company`.`id` != '" . $company_id . "' $Where")->result_array();

      $res = array_merge($result, $result1);
    } else if ($method == 'light') {
      $result = $this->db->query("SELECT DISTINCT `" . $db_fdd . "`.`products`.`p_id` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_fdd . "`.`products`.`p_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '0' AND (`" . $db_obs . "`.`company`.`data_type` = 'premium'  OR `" . $db_obs . "`.`company`.`data_type` = 'basic') AND `" . $db_obs . "`.`company`.`id` != '" . $company_id . "' $Where ")->result_array();

      $result1 = $this->db->query("SELECT DISTINCT `" . $db_fdd . "`.`products`.`p_id` FROM `" . $db_fdd . "`.`products` JOIN `" . $db_obs . "`.`pws_products_sheets` ON `" . $db_obs . "`.`pws_products_sheets`.`data_sheet` = `" . $db_fdd . "`.`products`.`data_sheet`JOIN `" . $db_obs . "`.`fdd_pro_quantity` ON `" . $db_obs . "`.`fdd_pro_quantity`.`fdd_pro_id` = `" . $db_obs . "`. `pws_products_sheets`.`obs_pro_id` JOIN `" . $db_obs . "`.`company` ON `" . $db_obs . "`.`company`.`id` = `" . $db_obs . "`.`fdd_pro_quantity`.`comp_id` WHERE `" . $db_obs . "`.`fdd_pro_quantity`.`is_obs_product` = '1' AND `" . $db_obs . "`.`company`.`status` = '1' AND (`" . $db_obs . "`.`company`.`data_type` = 'premium'  OR `" . $db_obs . "`.`company`.`data_type` = 'basic') AND `" . $db_obs . "`.`pws_products_sheets`.`checked` = '0' AND `" . $db_obs . "`.`company`.`id` != '" . $company_id . "'  $Where")->result_array();

      $res = array_merge($result, $result1);
    }
    if (!empty($res)) {
      $res = array_column($res, 'p_id');
      $all_pids = array_values(array_diff($products, $res));
    } else {
      $all_pids = $products;
    }
    $extraWhere = '';
    if (!empty($all_pids)) {
      $p_ids_chunk = array_chunk($all_pids, 500);
      $extraWhere = '(';
      foreach ($p_ids_chunk as $p_ids) {
        $extraWhere .= '`' . $db_fdd . '`.`products`.`p_id` IN (';
        $extraWhere .= implode(', ', $p_ids);
        $extraWhere .= ') OR ';
      }
      $extraWhere = chop($extraWhere, " OR ");
      $extraWhere .= ')';
    } else {
      $extraWhere = '';
    }

    if (!empty($extraWhere)) {
      $this->db->where($extraWhere);
      $this->db->where($db_fdd . '.products.priority != ', $method);

      if (($old_comp_type[0]['data_type'] == 'premium' && ($method == 'basic' || $method == 'light')) || ($old_comp_type[0]['data_type'] == 'basic' && $method == 'light')) {
        $this->db->update($db_fdd . '.products', array($db_fdd . '.products.priority' => $method, $db_fdd . '.products.is_priority_updt' => '0'));
      } else {
        $this->db->update($db_fdd . '.products', array($db_fdd . '.products.priority' => $method, $db_fdd . '.products.is_priority_updt' => '1',  $db_fdd . '.products.assigned_to_dietist' => 0));
      }
    }
    return true;
  }
  public function production_api($company_id = 0, $api_data =0){
    $this->db->select('api_enable');
    $this->db->where('company_id' , $company_id);
    $res = $this->db->get('general_settings')->row_array();  
      if(isset($res)){
      $this->db->where('company_id',$company_id);
      $this->db->update('general_settings',array('api_enable'=>$api_data));
     
      }
    }

  
}
