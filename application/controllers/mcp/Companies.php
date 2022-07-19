<?php
class Companies extends CI_Controller
{
  var $tempUrl = '';
  var $template = '';

  function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
    ini_set('memory_limit', '1024M');
    $this->load->library('utilities');
    $this->load->helper('phpmailer');

    $this->tempUrl = base_url() . 'application/views/mcp';
    $this->template = "/mcp";
    $this->load->model('mcp/Mcompanies');
    $this->load->model('mcp/Mpackage');
    $this->load->model('mcp/Mcountry');
    $this->load->model('mcp/Mcompany_type');
    $this->load->model('mcp/Maddon');
    $this->load->model('Mapi');

    $this->load->model('Mgeneral_settings');

    $this->temp = "/mcp/companies";

    $current_user          = $this->session->userdata('username');
    $this->current_user_id = $this->session->userdata('admin_id');
    $is_logged_in          = $this->session->userdata('is_logged_in');

    if (!$current_user || !$is_logged_in)
      redirect('mcp/mcplogin', 'refresh');
  }

  function index()
  {
    if ($this->input->post('sub_main')) {
      $maint = '0';
      if ($this->input->post('maintenence')) {
        $maint = $this->input->post('maintenence');
      } else {
        $maint = '0';
      }
      $this->db->where(array('id' => 1));
      $this->db->update('maintenence', array('checked' => $maint));
    }

    if ($this->input->post('label_api_main')) {
      $label_api = '0';
      if ($this->input->post('label_api')) {
        $label_api = $this->input->post('label_api');
      } else {
        $label_api = '0';
      }
      $this->db->where(array('id' => 2));
      $this->db->update('maintenence', array('checked' => $label_api));
    }

    if ($this->input->post('digi_api_main')) {
      $digi_api = '0';
      if ($this->input->post('digi_api')) {
        $digi_api = $this->input->post('digi_api');
      } else {
        $digi_api = '0';
      }
      $this->db->where(array('id' => 3));
      $this->db->update('maintenence', array('checked' => $digi_api));
    }

    if ($this->input->post('xerxes_api_main')) {
      $xerxes_api = '0';
      if ($this->input->post('xerxes_api')) {
        $xerxes_api = $this->input->post('xerxes_api');
      } else {
        $xerxes_api = '0';
      }
      $this->db->where(array('id' => 4));
      $this->db->update('maintenence', array('checked' => $xerxes_api));
    }

    if ($this->input->post('admin_country')) {
      $country_id = $this->input->post('admin_country');
      $companies_upd = $this->Mcompanies->update_admin(array('admin_country' => $country_id), $this->current_user_id);
    }

    if ($this->input->post('btn_search')) {

      $params = $this->input->post();
      $where_array = array('approved' => 1, 'flag' => "1", 'parent_id' => 0);
      $order_by = array();
      if ($params['search_by'] == 'id') {
        $where_array['id'] = $params['search_keyword'];
      } elseif ($params['search_by'] == 'company_name' || $params['search_by'] == 'email' || $params['search_by'] == 'username' || $params['search_by'] == 'city') {
        $where_array['like_columns'] = $params['search_by'];
        $where_array['like_value'] = $params['search_keyword'];

        if ($params['ac_type_id'])
          $where_array['ac_type_id'] = $params['ac_type_id'];

        if ($params['order_by']) {
          $order = 'desc';
          if ($params['order_by'] == 'id' || $params['order_by'] == 'city')
            $order = 'asc';

          $order_by[$params['order_by']] = $order;
        } else
          $order_by['id'] = 'desc';
      }

      $companies_arr = $this->Mcompanies->get_company($where_array, $order_by);
      $data['company_count'] = count($companies_arr);
    } else {
      $admin_data = $this->Mcompanies->get_admin_data($this->current_user_id);
      $where_array = array(
        'approved'   => '1',
        'country_id' => $admin_data['admin_country'],
        'parent_id'  => 0
      );
      $data['company_count'] = $this->Mcompanies->get_company_count($where_array);
    }

    $param = array();
    $data['account_types'] = $this->Mcompanies->get_account_types();

    $xyz = $this->db->get_where('maintenence', array('id' => 1))->result_array();
    $labeler = $this->db->get_where('maintenence', array('id' => 2))->result_array();
    $digi = $this->db->get_where('maintenence', array('id' => 3))->result_array();
    $erxes = $this->db->get_where('maintenence', array('id' => 4))->result_array();
    $data['maint'] = $xyz[0]['checked'];
    $data['label_api'] = $labeler[0]['checked'];
    $data['digi_api'] = $digi[0]['checked'];
    $data['xerxes_api'] = $erxes[0]['checked'];
    $data['admin_data'] = $this->Mcompanies->get_admin_data($this->current_user_id);

    $data['tempUrl'] = $this->tempUrl;
    $data['header'] = $this->template . '/header';
    $data['main'] = $this->template . '/companies';
    $data['footer'] = $this->template . '/footer';
    $this->load->vars($data);
    $this->load->view($this->template . '/mcp_view');
  }

  function ajax_companies()
  {
    ini_set('memory_limit', '20000M');
    set_time_limit(0);
    ini_set('max_execution_time', 0);
    $this->load->library('utilities');
    $this->load->helper('phpmailer');
    $start = $this->input->post('start');
    $limit = $this->input->post('limit');
    $admin_data = $this->Mcompanies->get_admin_data($this->current_user_id);
    $system_info = $this->Mcompanies->get_company_system_info();



    if ($this->input->post('btn_search')) {
      $params = $this->input->post();
      $where_array = array('approved' => 1, 'flag' => "1");
      $order_by = array();
      if ($params['search_by'] == 'id') {
        $where_array['id'] = $params['search_keyword'];
        $where_array['parent_id'] = 0;
      } elseif ($params['search_by'] == 'company_name' || $params['search_by'] == 'email' || $params['search_by'] == 'username' || $params['search_by'] == 'city') {
        $where_array['like_columns'] = $params['search_by'];
        $where_array['like_value'] = $params['search_keyword'];
        $where_array['parent_id'] = 0;

        if ($params['ac_type_id'])
          $where_array['ac_type_id'] = $params['ac_type_id'];

        if ($params['order_by']) {
          $order = 'desc';
          if ($params['order_by'] == 'id' || $params['order_by'] == 'city')
            $order = 'asc';

          $order_by[$params['order_by']] = $order;
        } else
          $order_by['id'] = 'desc';
      }

      $where_array['country_id'] = $admin_data['admin_country'];
      $content = $this->Mcompanies->get_company($where_array, $order_by, null, $start, $limit);
    } else {
      $content = $this->Mcompanies->get_company(array('approved' => 1, 'flag' => "1", 'parent_id' => 0, 'country_id' => $admin_data['admin_country']), array('id' => 'DESC'), null, $start, $limit);
    }

    $result = $this->Mcompanies->get_company_trial();
    $todays_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
    $todays_time = strtotime($todays_date);
    $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));

    $current = strtotime($date);
    if (!empty($content)) {
      foreach ($content as $key => $value) {
        $trial = strtotime($value->trial);
        if ($todays_time == $trial) {
          $update_on_trail_ = array('on_trial' => '0');
          $update = $this->Mcompanies->update_on_trial($update_on_trail_, $value->id);
        }
        $content[$key]->last_30_day_order = $this->Mcompanies->last_30_days_order($value->id);
      }
    }

    $account_types = $this->Mcompanies->get_account_types();
    $all_languales = $this->Mcompanies->get_all_languags();
    $response = array();
    $counter = 0;
    if (!empty($content)) {
      foreach ($content as $cont) {
        $no_of_subadmin = 0;
        $tot_fav = $this->Mcompanies->get_total_pws_and_fav($cont->id);
        if ($cont->role == 'super') {
          $no_of_subadmin = $this->Mcompanies->get_subadmins($cont->id);
        }

        $ct = $this->Mcompany_type->select(array('id' => $cont->type_id));
        if (!empty($ct))
          $company_type = $ct[0]->company_type_name;
        else
          $company_type = _('NONE');

        $status = '<select style="width:90px" class="textbox" type="select" id="status" name="status" onchange="company_status(' . $cont->id . ',this.value);"><option value="0" ' . (($cont->status == 0) ? 'selected="selected"' : '') . '>' . _('INACTIVE') . '</option><option value="1" ' . (($cont->status == 1) ? 'selected="selected"' : '') . '>' . _('ACTIVE') . '</option></select>';

        $data_type = '<select style="width:90px" class="textbox" type="select" id="status" name="status" onchange="change_data_type(' . $cont->id . ',this.value);"><option value="light" ' . (($cont->data_type == 'light') ? 'selected="selected"' : '') . '>' . _('LIGHT') . '</option><option value="basic" ' . (($cont->data_type == 'basic') ? 'selected="selected"' : '') . '>' . _('BASIC') . '</option><option value="premium" ' . (($cont->data_type == 'premium') ? 'selected="selected"' : '') . '>' . _('PREMIUM') . '</option></select>';

        $action = '<a href="' . base_url() . 'mcp/companies/update/' . $cont->id . '">MOREINFO</a>&nbsp;&nbsp;&nbsp;<a style="vertical-align:middle" data-comp_id="' . $cont->id . '" class="login_dropdown dropdown-toggle" data-toggle="dropdown"><img class="login_dropdown" src="' . base_url() . 'assets/mcp/images/login_option.png"/></a>';
        $action .= '<div class="login_options login_option' . $cont->id . '" style="display:none"><ul class="dropdown-menu dropdown-menu-right">';
        $action .= '<li class="option">OPTION</li>';
        $action .= '<li><img src="' . base_url() . 'assets/mcp/images/li-icon.png"/><a href="javascript:void(0);" onclick="get_login(\'' . $cont->id . '\',\'' . $cont->username . '\',\'' . $cont->password . '\');" id="login_' . $cont->id . '">' . _('Login') . '</a></li>';
        $action .= '<li><img src="' . base_url() . 'assets/mcp/images/li-icon.png"/><a href="javascript:void(0);" id="fdd2_login_' . $cont->id . '" onclick="get_login_fdd(\'' . $cont->id . '\');">' . _('Login 20') . '</a></li>';
        $action .= '<li><img src="' . base_url() . 'assets/mcp/images/li-icon.png"/><a href="javascript:;">' . _('Login shop') . '</a></li>';
        $action .= '<li class="option">' . _('STATS') . '</li>';
        $action .= '<li><img src="' . base_url() . 'assets/mcp/images/refresh.png"/><a href="#">' . $cont->total_rece . '/' . $cont->total_product . '</a></li>';
        $action .= '</ul></div>';

        $bestelonline_shop_status = '<select class="textbox" name="bestelonline_status" onchange="change_bo_shop_status(\'' . $cont->id . '\',this.value);">';
        $bestelonline_shop_status .= '<option value="0" ' . (($cont->shop_testdrive == 0) ? 'selected="selected"' : '') . '>' . _('Active') . '</option>';
        $bestelonline_shop_status .= '<option value="1" ' . (($cont->shop_testdrive == 1) ? 'selected="selected"' : '') . '>' . _('TestDrive') . '</option>';
        $bestelonline_shop_status .= '</select>';

        $this->db->select('id, direct_kcp,recipe_version,unfixed');
        $this->db->where('company_id', $cont->id);
        $prod_ids = $this->db->get('products')->result_array();
        $total_products = sizeof($prod_ids);
        $fixed_products = 0;
        $temp_arr = array();
        $temp_arr4 = array();
        $oo = array();
        foreach ($prod_ids as $k => $pro_arr) {
          $complete = 1;
          if ($cont->ac_type_id == 4 || $cont->ac_type_id == 5 || $cont->ac_type_id == 6) {
            if (!empty($pro_arr)) {
              $this->db->select('show_recipe');
              $this->db->where('company_id', $cont->id);
              $comp_det = $this->db->get('general_settings')->row_array();
              $show_recipe_version = $comp_det['show_recipe'];

              if ($pro_arr['recipe_version'] != '2' && $comp_det['show_recipe'] != '2') {
                $show_recipe_version = $pro_arr['recipe_version'];
              }

              if ($show_recipe_version == '0') {
                if ($pro_arr['unfixed'] == '1') {
                  $complete = 0;
                }
              } else {
                $this->db->where(array('obs_pro_id' => $pro_arr['id']));
                $result_custom = $this->db->get('fdd_pro_quantity')->result_array();
                if (empty($result_custom)) {
                  $complete = 0;
                }
              }
              if ($complete == 1) {
                $fixed_products++;
              }
            }
          } else {
            if ($pro_arr['unfixed'] == '1') {
              $complete = 0;
            }
            if ($complete == 1) {
              $fixed_products++;
            }
          }
        }
        if ($total_products != 0 && $fixed_products != 0) {
          $complete_per = ($fixed_products / $total_products) * 100;
          if (is_float($complete_per)) {
            $complete_per = round($complete_per, 2);
          }
        } else {
          $complete_per = 0;
        }

        $ac_type = '';

        if (!empty($account_types)) {
          $ac_type = '<select class="textbox" name="ac_type_id" onchange="change_ac_type(\'' . $cont->id . '\',this.value);">';

          foreach ($account_types as $at) {
            $ac_title = str_replace('FoodDESK ', '', $at->ac_title);
            $ac_type .= '<option value="' . $at->id . '" ' . (($cont->ac_type_id == $at->id) ? 'selected="selected"' : '') . '>' . strtoupper($ac_title) . '</option>';
          }

          $ac_type .= '</select>';
        }

        $obsdesk_status = '';

        $obsdesk_status .= '<select class="textbox" name="obsdesk_status" onchange="change_obsdesk_status(\'' . $cont->id . '\',this.value);">';
        $obsdesk_status .= '<option value="0" ' . (($cont->obsdesk_status == 0) ? 'selected="selected"' : '') . '>' . _('INACTIVE') . '</option>';
        $obsdesk_status .= '<option value="1" ' . (($cont->obsdesk_status == 1) ? 'selected="selected"' : '') . '>' . _('ACTIVE') . '</option>';
        $obsdesk_status .= '</select>';

        $language_html = '';

        if (!empty($all_languales)) {
          $language_html = '<select class="textbox" name="company_language_id">';

          foreach ($all_languales as $lang) {
            $language_html .= '<option value="' . $lang->id . '" ' . (($cont->language_id == $lang->id) ? 'selected="selected"' : '') . '>' . strtoupper($lang->lang_code) . '</option>';
          }

          $language_html .= '</select>';
        }

        $trail_date1 = strtotime($cont->trial);
        $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $current = strtotime($date);
        if ($cont->role == 'super') {
          $comp_name = $cont->company_name . ' (' . $no_of_subadmin . ')';
        } else {
          $comp_name = $cont->company_name;
        }

        $count_emails = 0;
        $system_selected = $cont->system_selected;
        $sys_conn_name = '';
        if ($system_selected != '') {
          $sys_conn     = json_decode($system_selected, true);
          $system_infos = array_column($system_info, 'system_name', 'id');
          if ($sys_conn[0] != 0 && array_key_exists($sys_conn[0], $system_infos)) {
            $sys_conn_name =  $system_infos[$sys_conn[0]];
          }
        }
        $response[] = array(
          $cont->id, //0
          ($comp_name), //1
          ($company_type), //2
          ($cont->city), //3
          date('d-m-Y', strtotime($cont->registration_date)), //4
          ($status), //5
          ($ac_type), //6
          ($action), //7
          ($cont->address . '<br />' . $cont->city . '<br />' . $cont->zipcode), //8
          ($cont->phone), //9
          ($cont->email), //10
          ($cont->first_name . ' ' . $cont->last_name), //11
          ($obsdesk_status), //12
          $cont->ac_type_id, //13
          date('d-m-Y', $trail_date1), //14
          $cont->email, //15
          $cont->excel_import_file_name, //16
          $current, //17
          $trail_date1, //18
          $cont->on_trial, //19
          $total_products, //20
          $fixed_products, //21
          $complete_per, //22
          $cont->fdd_tv, //23
          $cont->shop_version, // 24 
          $language_html, // 25 
          ($cont->whr_get_info), // 26 
          $tot_fav,  //27
          $count_emails,  //28
          $cont->client_no,  //29
          $cont->expiry_date != '0000-00-00' ? date('d-m-Y', strtotime($cont->expiry_date)) : '--', //30
          $cont->last_login != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($cont->last_login)) : '--', //31
          $sys_conn_name, //32
          ($data_type) //33

        );
      }
    }
    echo json_encode($response);
  }

  /* to insert records  */
  function companies_add_edit()
  {
    $this->load->library('form_validation');

    if ($this->input->post()) {

      $data['tempUrl'] = $this->tempUrl;
      $this->form_validation->set_rules('company_name', 'Company Name', 'required');
      $this->form_validation->set_rules('first_name', 'First name', 'required');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required');
      $this->form_validation->set_rules('email', 'Email Id', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required');
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('country_id', 'Country', 'required');
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() == FALSE) {
        $this->form_validation->set_message('required', 'Required');
      } else {
        $fdd_tv = 0;
        if ($this->input->post('fdd_tv')) {
          $fdd_tv = $this->input->post('fdd_tv');
        }
        $hide_download = '0';
        if ($this->input->post('hide_download') && $this->input->post('hide_download') != '') {
          $hide_download = $this->input->post('hide_download');
        }

        $hide_product_download = '0';
        if ($this->input->post('hide_product_download') && $this->input->post('hide_product_download') != '') {
          $hide_product_download = $this->input->post('hide_product_download');
        }

        $a['company_slug'] = $this->create_slug($this->input->post('company_name'));
        $a['company_name'] = $this->input->post('company_name');
        $a['type_id'] = implode("#", $this->input->post('type_id'));
        $a['first_name'] = $this->input->post('first_name');
        $a['last_name'] = $this->input->post('last_name');
        $a['email'] = $this->input->post('email');
        $a['phone'] = $this->input->post('phone');
        $a['website'] = $this->input->post('website');
        $a['address'] = $this->input->post('address');
        $a['admin_remarks'] = $this->input->post('admin_remarks');
        $a['reseller_remarks'] = $this->input->post('reseller_remarks');
        $a['city'] = $this->input->post('city');
        $a['country_id'] = $this->input->post('country_id');
        $a['existing_order_page'] = '';
        $a['username'] = $this->input->post('username');
        $a['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $a['packages_id'] = $this->input->post('packages_id');
        $a['ac_type_id'] = $this->input->post('ac_type_id');
        $a['registered_by'] = $this->input->post('registered_by');
        $a['additional_email'] = json_encode($this->input->post('additional_email'));
        $a['fdd_tv'] = $fdd_tv;
        $a['hide_download'] = $hide_download;
        $a['hide_product_download'] = $hide_product_download;
        $a['show_menukartt_maker'] = ($this->input->post('show_menukartt_maker')) ? $this->input->post('show_menukartt_maker') : 0;
        $a['comp_grp'] = $this->input->post('type_id_cat');
        $a['is_high_priority'] = ($this->input->post('is_high_priority')) ? '1' : '0';

        if ($this->input->post('have_website')) {
          $a['have_website'] = 1;
        } else {
          $a['have_website'] = 0;
        }

        if (!isset($a['domain']) || $a['domain'] == '') {
          $a['website'] = $this->input->post('website');
        }
        if (isset($a['country_id']) && $a['country_id'] == 150) {
          $a['show_recipe'] = 1;
        }
        $a['email_ads'] = $this->input->post('email_ads');
        $a['footer_text'] = $this->input->post('footer_text');
        $a['registration_date'] = $registration_date = $this->input->post('registration_date');
        if ($this->input->post('ac_type_id') == '2' || $this->input->post('ac_type_id') == '3') {
          if ($this->input->post('on_trial')) {
            $a['on_trial'] = '1';
          } else {
            $a['on_trial'] = '0';
          }
        } else if ($this->input->post('ac_type_id') == '5' || $this->input->post('ac_type_id') == '6') {
          $a['cost_price_status'] = '1#2#3#4#5';
          $a['haccp_addon'] = '1';
        } else if ($this->input->post('ac_type_id') == '1' || in_array('20', $this->input->post('type_id')) || in_array('27', $this->input->post('type_id'))) {
          $a['easybutler_status'] = '{"activate_easybutler":"0","easybutler_order_app":"0"}';
        }
        $date = $registration_date;
        $expiry_date = '';
        if ($this->input->post('5year_subscription')) {
          $a['5year_subscription'] = '1';
          $newdate = strtotime('+2 year', strtotime($date));
        } else {
          $a['5year_subscription'] = '0';
          $newdate = strtotime('+1 year', strtotime($date));
        }
        $expiry_date = date('Y-m-d', $newdate);

        $a['expiry_date'] = $expiry_date; //$this->input->post('expiry_date');
        $a['earnings_year'] = $this->input->post('earnings_year');
        $a['zipcode'] = $this->input->post('zipcode');
        $a['parent_id'] = $this->input->post('parent_id');

        if ($this->input->post('role')) {
          $a['role'] = 'super';
        } elseif ($a['parent_id'] == 0) {
          $a['role'] = 'master';
        } elseif ($a['parent_id'] != 0) {
          $a['role'] = 'sub';
        }

        $address = $this->input->post('address') . " " . $this->input->post('zipcode') . " " . $this->input->post('city') . " " . (($this->input->post('country_id') == "21") ? "BELGIE" : "NEDERLAND");
        $this->load->helper("geolocation");
        $location = get_geolocation($address);
        $a["geo_location"] = json_encode($location);

        $a["approved"] = "1";
        $company_id = $this->Mcompanies->insert($a);
        if ($this->input->post('fdd_tv')) {
          $tv['first_name'] = $this->input->post('company_name');
          $tv['shopname'] = $this->input->post('company_name');
          $tv['address'] = $this->input->post('address');
          $tv['city'] = $this->input->post('city');
          $tv['zip_code'] = $this->input->post('zipcode');
          $tv['country'] = $this->input->post('country_id');
          $tv['shop_type'] = json_encode($this->input->post('type_id'));
          $tv['phone'] = $this->input->post('phone');
          $tv['email'] = $this->input->post('email');
          $tv['username'] = $this->input->post('username');
          $tv['password'] = $this->input->post('password');
          $tv['type'] = 'client';
          $tv['date_added'] = date('Y-m-d H:i:s');
          $tv['account_status'] = 1;
        }

        /**
         * Doing default settings
         */
        $this->load->helper('default_setting');
        do_settings($company_id, $a['company_name'], $a['type_id']);
      }
    }

    $data['companies'] = $this->Mcompanies->get_company(array('role' => 'super', 'approved' => 1, 'status' => 1));
    $data['package'] = $this->Mpackage->select();
    $data['country'] = $this->Mcountry->select();
    $data['company_type'] = $this->Mcompany_type->select(array('status' => 'ACTIVE'));
    $data['company_type_group'] = $this->Mcompany_type->all_comp_grp();
    $data['account_types'] = $this->Mcompanies->get_account_types();

    $data['header'] = $this->template . '/header';
    $data['main'] = $this->template . '/companies_add_edit';
    $data['footer'] = $this->template . '/footer';

    $this->load->vars($data);
    $this->load->view($this->template . '/mcp_view');
  }

  private function generate_key()
  {
    $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

    if ($salt === FALSE) {
      $salt = hash('sha256', time() . mt_rand());
    }

    $new_key = substr($salt, 0, 32);
    $new_key = strtoupper($new_key);

    $exist_key = $this->Mcompanies->key_exists($new_key);
    if ($exist_key == $new_key) {
      $this->generate_key();
    } else {
      return $new_key;
    }
  }

  /* to insert records  */
  function companies_ks_add()
  {
    $this->load->library('form_validation');

    if ($this->input->post()) {
      $data['tempUrl'] = $this->tempUrl;
      $this->form_validation->set_rules('company_name', 'Company Name', 'required');
      $this->form_validation->set_rules('first_name', 'First name', 'required');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required');
      $this->form_validation->set_rules('email', 'Email Id', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required');
      $this->form_validation->set_rules('website', 'Website', 'required');
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      //$this->form_validation->set_rules('country_id','Country','required');
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');
      $this->form_validation->set_rules('registration_date', 'Date of registration', 'required');
      $this->form_validation->set_rules('earnings_year', 'Earnings per Year', 'required');

      if ($this->form_validation->run() == FALSE) {
        $this->form_validation->set_message('required', 'Required');
      } else {
        $this->load->helper('string');
        $this->db->select('username');
        $company_usernames = $this->db->get('company')->result_array();
        $usernames_taken = array();
        foreach ($company_usernames as $comp) {
          $usernames_taken[] = $comp['username'];
        }
        $username;
        $username_unique = false;
        while (!$username_unique) {
          $username = trim($this->input->post('company_name')) . random_string('num', 2);
          if (!in_array($username, $usernames_taken)) {
            $username_unique = true;
          }
        }
        $password = random_string('alnum', 6);
        $a['company_slug'] = $this->create_slug($this->input->post('company_name'));
        $a['company_name'] = $this->input->post('company_name');
        $a['type_id'] = 8;
        $a['first_name'] = $this->input->post('first_name');
        $a['last_name'] = $this->input->post('last_name');
        $a['email'] = $this->input->post('email');
        $a['phone'] = $this->input->post('phone');
        $a['address'] = $this->input->post('address');
        $a['admin_remarks'] = $this->input->post('admin_remarks');
        $a['city'] = $this->input->post('city');
        $a['country_id'] = 21;
        $a['existing_order_page'] = $this->input->post('existing_order_page');
        $a['username'] = $username;
        $a['password'] = $password;
        $a['packages_id'] = $this->input->post('packages_id');
        $a['ac_type_id'] = 3;
        $a['registered_by'] = $this->input->post('registered_by');
        $a['k_assoc'] = 1;
        $a['mail_subscription']  = 'subscribed';

        if ($this->input->post('have_website')) {
          $a['have_website'] = 1;
        } else {
          $a['have_website'] = 0;
          $a['package'] = $this->input->post('package');
          $a['domain'] = $this->input->post('domain');
          $a['canregister'] = $this->input->post('canregister');
        }

        if (!isset($a['domain']) || $a['domain'] == '') {
          $a['website'] = $this->input->post('website');
        }

        $a['email_ads'] = $this->input->post('email_ads');
        $a['footer_text'] = $this->input->post('footer_text');
        $a['registration_date'] = $registration_date = $this->input->post('registration_date');
        if ($this->input->post('ac_type_id') == '2' || $this->input->post('ac_type_id') == '3') {
          if ($this->input->post('on_trial')) {
            $a['on_trial'] = '1';
          } else {
            $a['on_trial'] = '0';
          }
        } else if ($this->input->post('ac_type_id') == '5' || $this->input->post('ac_type_id') == '6') {
          $a['cost_price_status'] = '1#2#3#4#5';
        }
        $date = $registration_date;
        $expiry_date = '';
        if ($this->input->post('5year_subscription')) {
          $a['5year_subscription'] = '1';
          $newdate = strtotime('+2 year', strtotime($date));
        } else {
          $a['5year_subscription'] = '0';
          $newdate = strtotime('+1 year', strtotime($date));
        }
        $expiry_date = date('Y-m-d', $newdate);

        $a['expiry_date'] = $expiry_date; //$this->input->post('expiry_date');
        $a['earnings_year'] = $this->input->post('earnings_year');
        $a['zipcode'] = $this->input->post('zipcode');
        $a['parent_id'] = $this->input->post('parent_id');

        if ($this->input->post('role')) {
          $a['role'] = 'super';
        } elseif ($a['parent_id'] == 0) {
          $a['role'] = 'master';
        } elseif ($a['parent_id'] != 0) {
          $a['role'] = 'sub';
        }

        $address = $this->input->post('address') . " " . $this->input->post('zipcode') . " " . $this->input->post('city') . " " . (($this->input->post('country_id') == "21") ? "BELGIE" : "NEDERLAND");
        $this->load->helper("geolocation");
        $location = get_geolocation($address);
        $a["geo_location"] = json_encode($location);

        $company_id = $this->Mcompanies->insert($a);

        /**
         * Doing default settings
         */
        do_settings($company_id, $a['company_name']);
      }
    }

    $data['companies'] = $this->Mcompanies->get_company(array('role' => 'super', 'approved' => 1, 'status' => 1));
    $data['package'] = $this->Mpackage->select();
    $data['country'] = $this->Mcountry->select();
    $data['company_type'] = $this->Mcompany_type->select(array('status' => 'ACTIVE'));
    $data['account_types'] = $this->Mcompanies->get_account_types();

    $data['header'] = $this->template . '/header';
    $data['main'] = $this->template . '/companies_ks_add_edit';
    $data['footer'] = $this->template . '/footer';
    $this->load->vars($data);
    $this->load->view($this->template . '/mcp_view');
  }

  function division_add_edit()
  {
    $this->load->library('form_validation');

    $params = $this->uri->uri_to_assoc(4);
    $data['ID'] = array_key_exists('company_id', $params) ? $params['company_id'] : 0;

    $data['division_id'] = array_key_exists('division_id', $params) ? $params['division_id'] : 0;
    $data['subcomp_id'] = array_key_exists('subcomp_id', $params) ? $params['subcomp_id'] : 0;

    if ($this->input->post('ID') && $this->input->post('first_name')) {
      $data['tempUrl'] = $this->tempUrl;
      $this->form_validation->set_rules('company_name', 'Company Name', 'required');
      $this->form_validation->set_rules('first_name', 'First name', 'required');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required');
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() == FALSE) {
      } else {
        $subcompany_id = $this->input->post('subcomp_id');
        if (isset($subcompany_id) &&  $subcompany_id != 0) {
          $parent_company = $this->Mcompanies->get_company(array('id' => $subcompany_id));
        } else {
          $parent_company = $this->Mcompanies->get_company(array('id' => $this->input->post('ID')));
        }

        $a['parent_id'] = $parent_company[0]->id;
        $a['company_name'] = $this->input->post('company_name');
        $a['first_name'] = $this->input->post('first_name');
        $a['last_name'] = $this->input->post('last_name');
        $a['email'] = $this->input->post('email');
        $a['phone'] = $this->input->post('phone');
        $a['city'] = $this->input->post('address');
        $a['address'] = $this->input->post('city');
        $a['zipcode'] = $this->input->post('zipcode');
        $a['username'] = $this->input->post('username');
        $a['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $a['registration_date'] = $parent_company[0]->registration_date;
        $a['expiry_date'] = $parent_company[0]->expiry_date;
        $a['earnings_year'] = $parent_company[0]->earnings_year;
        $a["role"] = "division";
        $a["type_id"] = $parent_company[0]->type_id;
        $a['ac_type_id'] = $parent_company[0]->ac_type_id;
        $a["country_id"] = $parent_company[0]->country_id;
        $a["approved"] = $this->input->post('approved'); //$parent_company[0]->approved;
        $a['ac_type_id'] = $parent_company[0]->ac_type_id;
        $a['show_menukartt_maker'] = $parent_company[0]->show_menukartt_maker;
        $a['show_on_app'] = $parent_company[0]->show_on_app;
        $a["status"] = $this->input->post('status'); //"0";
        $a["email_ads"] = $parent_company[0]->email_ads;
        $a["website"] = $parent_company[0]->website;
        $a["footer_text"] = $parent_company[0]->footer_text;
        $a["5year_subscription"] = $parent_company[0]->{'5year_subscription'};
        $a["show_menukartt"] = $parent_company[0]->show_menukartt;
        $a["haccp_status"] = 0;
        if ($this->input->post('ID') && $this->input->post('division_id')) {
          //Update
          $a['id'] = $this->input->post('division_id');
          $result = $this->Mcompanies->update($a);
        } else {
          //Insert
          $a['company_slug'] = $this->create_slug($this->input->post('company_name'));
          $result = $this->Mcompanies->insert($a);
          $last_ins = $this->db->insert_id();
          $this->Mcompanies->generateApi($last_ins);

          $general_settings = array();
          $general_settings["company_id"] = $last_ins;
          $general_settings["language_id"] = $parent_company[0]->current_lang;
          $general_settings["emailid"] = $this->input->post('email');
          $this->Mgeneral_settings->do_general_settings($general_settings);

          $allergencard_settings = array();
          $allergencard_settings["company_id"] = $last_ins;
          $this->Mgeneral_settings->do_allergencard_settings($allergencard_settings);

          if (!empty($parent_company[0]) && ($parent_company[0]->ac_type_id == 4 || $parent_company[0]->ac_type_id == 5 || $parent_company[0]->ac_type_id == 6 || $parent_company[0]->ac_type_id == 7)) {
            $type_id = $parent_company[0]->type_id;
            $type_id = explode('#', $type_id);
            if (in_array('20', $type_id) || in_array('27', $type_id)) {
              $update_desk_arr = array(
                'obsdesk_status' => 1
              );
              $this->db->where('id', $last_ins);
              $this->db->update('company', $update_desk_arr);
            }
          }
          if (!empty($parent_company) && ($parent_company[0]->ac_type_id == 4 || $parent_company[0]->ac_type_id == 5 || $parent_company[0]->ac_type_id == 6 || $parent_company[0]->ac_type_id == 7)) {
            $insrt_easybutler_array = array(
              'company_id' => $last_ins
            );
            $this->db->insert('easybutler_settings', $insrt_easybutler_array);
            $this->db->insert('menucard_layout_setting', $insrt_easybutler_array);
            $this->load->model('mcp/Mcompanies');
            $this->Mcompanies->insert_feedback_ques_row($last_ins, $parent_company[0]->type_id);
          }
          if (!empty($parent_company) && isset($parent_company[0]->type_id)) {
            $type_id = $parent_company[0]->type_id;
            $type_id = explode('#', $type_id);
            if (in_array('14', $type_id)) {
              $default_cat_fr_restaurant = array(
                'Voorgerecht',
                'Hoofdgerecht',
                'Nagerecht'
              );
              for ($i = 0; $i < sizeof($default_cat_fr_restaurant); $i++) {
                $this->db->insert('categories', array('company_id' => $last_ins, 'name' => $default_cat_fr_restaurant[$i]));
                $insert_id = $this->db->insert_id();
                if (isset($insert_id)) {
                  $this->db->insert('categories_name', array('cat_id' => $insert_id, 'comp_id' => $last_ins, 'name_dch' => $default_cat_fr_restaurant[$i]));
                }
              }
            }
          }
        }

        redirect('mcp/companies/update/' . $this->input->post('ID'));
      }
    }
    if ($data['division_id']) {
      $data['division_comp'] = $this->Mcompanies->get_company(array('id' => $data['division_id']));
    }

    $this->load->vars($data);
    $this->load->view($this->template . '/division_add_edit');
  }



  function subadmin_add_edit()
  {
    $this->load->library('form_validation');

    $params = $this->uri->uri_to_assoc(4);
    $data['ID'] = array_key_exists('company_id', $params) ? $params['company_id'] : 0;
    $data['subid'] = array_key_exists('subid', $params) ? $params['subid'] : 0;

    if ($this->input->post('ID') && $this->input->post('first_name')) {
      $data['tempUrl'] = $this->tempUrl;
      $this->form_validation->set_rules('company_name', 'Company Name', 'required');
      $this->form_validation->set_rules('first_name', 'First name', 'required');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required');
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() == FALSE) {
      } else {
        $parent_company = $this->Mcompanies->get_company(array('id' => $this->input->post('ID')));
        $a['parent_id'] = $this->input->post('ID');
        $a['company_name'] = $this->input->post('company_name');
        $a['first_name'] = $this->input->post('first_name');
        $a['last_name'] = $this->input->post('last_name');
        $a['email'] = $this->input->post('email');
        $a['phone'] = $this->input->post('phone');
        $a['city'] = $this->input->post('address');
        $a['address'] = $this->input->post('city');
        $a['zipcode'] = $this->input->post('zipcode');
        $a['username'] = $this->input->post('username');
        $a['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $a['ac_type_id'] = $parent_company[0]->ac_type_id;
        $a['type_id'] = $parent_company[0]->type_id;
        $a['show_on_app'] = $parent_company[0]->show_on_app;
        $a['show_menukartt_maker'] = $parent_company[0]->show_menukartt_maker;
        $a['registration_date'] = $parent_company[0]->registration_date;
        $a['expiry_date'] = $parent_company[0]->expiry_date;
        $a['earnings_year'] = $parent_company[0]->earnings_year;
        $a["role"] = "sub";
        $a["country_id"] = $parent_company[0]->country_id;
        $a["approved"] = $this->input->post('approved'); //$parent_company[0]->approved;
        $a["status"] = $this->input->post('status'); //"0";
        $a["email_ads"] = $parent_company[0]->email_ads;
        $a["website"] = $parent_company[0]->website;
        $a["footer_text"] = $parent_company[0]->footer_text;
        $a["haccp_status"] = 0;
        $a["5year_subscription"] = $parent_company[0]->{'5year_subscription'};
        $a["show_menukartt"] = $parent_company[0]->show_menukartt;

        if ($this->input->post('ID') && $this->input->post('subid')) {
          //Update

          $a['id'] = $this->input->post('subid');
          $result = $this->Mcompanies->update($a);
        } else {
          $a['company_slug'] = $this->create_slug($this->input->post('company_name'));
          $result = $this->Mcompanies->insert($a);
          $last_ins = $this->db->insert_id();

          $this->Mcompanies->generateApi($last_ins);

          $general_settings = array();
          $general_settings["company_id"] = $last_ins;
          $general_settings["language_id"] = $parent_company[0]->current_lang;
          $general_settings["emailid"] = $this->input->post('email');
          $this->Mgeneral_settings->do_general_settings($general_settings);
          $allergencard_settings = array();
          $allergencard_settings["company_id"] = $last_ins;
          $this->Mgeneral_settings->do_allergencard_settings($allergencard_settings);


          if (!empty($parent_company[0]) && ($parent_company[0]->ac_type_id == 4 || $parent_company[0]->ac_type_id == 5 || $parent_company[0]->ac_type_id == 6 || $parent_company[0]->ac_type_id == 7)) {
            $type_id = $parent_company[0]->type_id;
            $type_id = explode('#', $type_id);
            if (in_array('20', $type_id) || in_array('27', $type_id)) {
              $update_desk_arr = array(
                'obsdesk_status' => 1
              );
              $this->db->where('id', $last_ins);
              $this->db->update('company', $update_desk_arr);
            }
          }
          if (!empty($parent_company) && ($parent_company[0]->ac_type_id == 4 || $parent_company[0]->ac_type_id == 5 || $parent_company[0]->ac_type_id == 6 || $parent_company[0]->ac_type_id == 7)) {
            $insrt_easybutler_array = array(
              'company_id' => $last_ins
            );
            $this->db->insert('easybutler_settings', $insrt_easybutler_array);
            $this->db->insert('menucard_layout_setting', $insrt_easybutler_array);
            $this->load->model('mcp/Mcompanies');
            $this->Mcompanies->insert_feedback_ques_row($last_ins, $parent_company[0]->type_id);
          }
          if (!empty($parent_company) && isset($parent_company[0]->type_id)) {
            $type_id = $parent_company[0]->type_id;
            $type_id = explode('#', $type_id);
            if (in_array('14', $type_id)) {
              $default_cat_fr_restaurant = array(
                'Voorgerecht',
                'Hoofdgerecht',
                'Nagerecht'
              );
              for ($i = 0; $i < sizeof($default_cat_fr_restaurant); $i++) {
                $this->db->insert('categories', array('company_id' => $last_ins, 'name' => $default_cat_fr_restaurant[$i]));
                $insert_id = $this->db->insert_id();
                if (isset($insert_id)) {
                  $this->db->insert('categories_name', array('cat_id' => $insert_id, 'comp_id' => $last_ins, 'name_dch' => $default_cat_fr_restaurant[$i]));
                }
              }
            }
          }
        }
        redirect('mcp/companies/update/' . $this->input->post('ID'));
      }
    }



    if ($data['subid']) {
      $data['subcompany'] = $this->Mcompanies->get_company(array('id' => $data['subid']));
    }

    $this->load->vars($data);
    $this->load->view($this->template . '/subadmin_add_edit');
  }

  function delete($id = NULL)
  {
    if ($id != '') {
      $result = $this->Mcompanies->delete($id);
      redirect($this->temp, 'refresh');
    }
  }

  /* to display data in fields to update */
  function update($id = NULL, $param = NULL)
  {
    $this->load->helper(array('form', 'url'));
    $this->load->library('form_validation');

    if ($this->input->post('company_name')) {
      $this->form_validation->set_rules('company_name', 'Company Name', 'required');
      $this->form_validation->set_rules('email', 'Email Id', 'required');
      $this->form_validation->set_rules('phone', 'Phone Number', 'required');
      $this->form_validation->set_rules('address', 'Address', 'required');
      $this->form_validation->set_rules('zipcode', 'Zipcode', 'required');
      $this->form_validation->set_rules('city', 'City', 'required');
      $this->form_validation->set_rules('country_id', 'Country', 'required');
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('registration_date', 'Date of registration', 'required');

      if ($this->form_validation->run() == FALSE) {
        //echo 'error';
      } else {

        $hide_download = '0';
        if ($this->input->post('hide_download') && $this->input->post('hide_download') != '') {
          $hide_download = $this->input->post('hide_download');
        }

        $hide_product_download = '0';
        if ($this->input->post('hide_product_download') && $this->input->post('hide_product_download') != '') {
          $hide_product_download = $this->input->post('hide_product_download');
        }

        $gs = array();
        $id = $a['id'] = $this->input->post('company_id');
        $a['company_name'] = $this->input->post('company_name');
        $a['type_id'] = implode("#", $this->input->post('type_id'));
        $a['comp_grp'] = $this->input->post('type_id_cat');
        $a['first_name'] = $this->input->post('first_name');
        $a['last_name'] = $this->input->post('last_name');
        $a['client_no'] = $this->input->post('client_no');
        $a['email'] = $this->input->post('email');
        $a['phone'] = $this->input->post('phone');
        $a['website'] = $this->input->post('website');
        $a['address'] = $this->input->post('address');
        $a['admin_remarks'] = $this->input->post('admin_remarks');
        $a['reseller_remarks'] = $this->input->post('reseller_remarks');
        $a['city'] = $this->input->post('city');
        $a['country_id'] = $this->input->post('country_id');
        $a['existing_order_page'] = $this->input->post('existing_order_page');
        $a['username'] = $this->input->post('username');
        $a['ac_type_id'] = $this->input->post('ac_type_id');
        $a['packages_id'] = $this->input->post('packages_id');
        $a['email_ads'] = $this->input->post('email_ads');
        $a['footer_text'] = $this->input->post('footer_text');
        $a['additional_email'] = json_encode(array_unique(array_filter($this->input->post('additional_email'))));
        $a['hide_download'] = $hide_download;
        $a['hide_product_download'] = $hide_product_download;
        $a['registration_date'] = $this->input->post('registration_date');
        $a['is_high_priority'] = ($this->input->post('is_high_priority')) ? '1' : '0';

        $registration_date = explode("-", $this->input->post('registration_date'));

        // fetching current account type
        $this->db->select('ac_type_id,type_id,parent_id, data_type');
        $this->db->where('id', $a['id']);
        $current_ac_type_id = $this->db->get('company')->result();
        if ($current_ac_type_id[0]->type_id != $a['type_id']) {
          $this->Mcompanies->update_feedback_ques_row($a['id'], $a['type_id']);
        }
        $parent_id = $current_ac_type_id['0']->parent_id;

        if (($current_ac_type_id['0']->ac_type_id == 4 || $current_ac_type_id['0']->ac_type_id == 5 || $current_ac_type_id['0']->ac_type_id == 6) &&  ($a['ac_type_id'] == 7)) {
          $res = $this->Mgeneral_settings->basic_to_light($a['id']);
        }

        if ($this->input->post('5year_subscription')) {
          $a['5year_subscription'] = '1';
          $expiry_date  = mktime(0, 0, 0, $registration_date[1], $registration_date[2], $registration_date[0] + 2);
          $a['expiry_date'] = date("Y-m-d", $expiry_date);
        } else {
          $a['5year_subscription'] = '0';
          $expiry_date  = mktime(0, 0, 0, $registration_date[1], $registration_date[2], $registration_date[0] + 1);
          $a['expiry_date'] = date("Y-m-d", $expiry_date);
        }

        if ($this->input->post('hide_next_step')) {
          $gs['hide_next_step'] = '1';
        } else {
          $gs['hide_next_step'] = '0';
        }
        $a['third_pary_con'] = $this->input->post("third_pary_con");
        $hide_recipe_in_cp = $this->input->post("hide_recipe_in_cp");
        if ($hide_recipe_in_cp == '') {
          $hide_recipe_in_cp = '0';
        }
        $a['hide_recipe_in_cp'] = $hide_recipe_in_cp;

        $a['earnings_year'] = $this->input->post('earnings_year');
        $a['zipcode'] = $this->input->post('zipcode');
        $a['parent_id'] = $this->input->post('parent_id') ? $this->input->post('parent_id') : $parent_id;

        if ($this->input->post('role') == 'super') {
          $a['role'] = 'super';
        } elseif ($a['parent_id'] != 0) {
          $a['role'] = 'sub';
        } else {
          $a['role'] = 'master';
        }

        $a['fdd_product_credit'] = $this->input->post('fdd_credits');

        /* Keurslager Association */

        $a['ingredient_system'] = $this->input->post('ingredient_system') ? 1 : 0;
        $a['show_demoshop_link'] = $this->input->post('show_demoshop_link') ? 1 : 0;
        $a['show_recipe'] = $this->input->post('show_recipe') ? 1 : 0;
        $sys_selected = $this->input->post('client_system_name');
        if ($sys_selected == 2) {
          $sub_comp_for_digi = $this->input->post('sub_comp_for_digi');

          $a['system_selected'] = json_encode(array($sys_selected, $sub_comp_for_digi));
        } else {
          $sub_comp_for_digi = "";
          $a['system_selected'] = json_encode(array($sys_selected, $sub_comp_for_digi));
        }

        $selectncopy = $this->input->post("copy_content");

        if ($selectncopy == '') {
          $selectncopy = '0';
        } else if ($selectncopy == 'on' || $selectncopy == 1) {
          $selectncopy = '1';
        }
        $a['select_n_copy'] = $selectncopy;
        $a['enable_infodesk'] = $this->input->post('enable_infodesk') ? 1 : 0;
        $a['hide_checkout'] = $this->input->post('hide_checkout') ? 1 : 0;
        $a['obsdesk_status'] = $this->desk_status($this->input->post('ac_type_id'));
        $a['ingredient_article_status'] = $this->input->post('ingredient_article_status') ? 1 : 0;

        if ($this->input->post('ac_type_id') == "1") {
          $easybutler_status = array(
            'activate_easybutler' => "0",
            'easybutler_order_app' => "0"
          );
          $a['easybutler_status'] = json_encode($easybutler_status);
          $a['show_menukartt'] = "0";
          $a['show_menukartt_maker']  = "0";
        }

        $this->Mcompanies->update($a);
        $tv['first_name'] = $this->input->post('company_name');
        $tv['shopname'] = $this->input->post('company_name');
        $tv['address'] = $this->input->post('address');
        $tv['city'] = $this->input->post('city');
        $tv['zip_code'] = $this->input->post('zipcode');
        $tv['country'] = $this->input->post('country_id');
        $tv['shop_type'] = json_encode($this->input->post('type_id'));
        $tv['phone'] = $this->input->post('phone');
        $tv['email'] = $this->input->post('email');
        $tv['username'] = $this->input->post('username');
        $tv['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $tv['type'] = 'client';
        $tv['date_added'] = date('Y-m-d H:i:s');
        $tv['account_status'] = 1;

        $this->db->select('show_simple_list');
        $res = $this->db->get_where('general_settings', array('company_id' => $this->input->post('company_id')))->row_array();

        if (!empty($res)) {
          if (in_array_any(getHCType(), $a['type_id']) && !in_array($res['show_simple_list'], [0, 2])) {
            $gs['show_simple_list'] = 2;
          } elseif (!in_array_any(getHCType(), $a['type_id']) && !in_array($res['show_simple_list'], [1, 3])) {
            $gs['show_simple_list'] = 3;
          }
        } else {
          if (in_array_any(getHCType(), $a['type_id']) && !in_array($res['show_simple_list'], [0, 2])) {
            $gs['show_simple_list'] = 2;
          } elseif (!in_array_any(getHCType(), $a['type_id']) && !in_array($res['show_simple_list'], [1, 3])) {
            $gs['show_simple_list'] = 3;
          }
        }

        $this->Mgeneral_settings->update_company_general_settings($id, $gs);

        if ($this->input->post('show_demoshop_link')) {
          if (!file_exists(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/general_settings_" . $id . ".json")) {
            $this->load->library('Shop');
            $this->shop->update_demo_files($id, $a['role']);
          }
        } else {
          if (file_exists(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/general_settings_" . $id . ".json")) {
            unlink(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/delivery_product_cat_" . $id . ".json");
            unlink(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/pickup_product_cat_" . $id . ".json");
            unlink(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/search_delivery_product_" . $id . ".json");
            unlink(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/search_pickup_product_" . $id . ".json");
            unlink(dirname(__FILE__) . "/../../../../testdrive/shop_js/json/general_settings_" . $id . ".json");
          }
        }

        $this->session->set_userdata('action', 'general_setting_json');
        redirect('mcp/companies/update/' . $id);
      }
    }

    if ($this->input->post('save_text')) {
      $id = $a['id'] = $this->input->post('company_id');
      $a['company_footer_text'] = $this->input->post('company_footer_text');
      $a['company_footer_link'] = $this->input->post('company_footer_link');
      $a['text_bg_color'] = $this->input->post('text_bg_color');
      $a['text_color'] = $this->input->post('text_color');
      $this->Mcompanies->update($a);

      redirect('mcp/companies/update/' . $id);
    }

    if ($this->input->post('assign_partner')) {
      $id = $a['id'] = $this->input->post('p_company_id');
      $a['partner_id'] = json_encode($this->input->post('partner_id'));
      $a['partner_total_commission'] = $this->input->post('total_commission');
      $a['partner_invoice_date'] = $this->input->post('invoive_date');
      $a['invoice_end_date'] = $this->input->post('invoive_end_date');
      $a['partner_message'] = $this->input->post('message');
      $this->Mcompanies->update($a);

      // --- >> Fetch Partner & Company Data

      $this->load->model('mcp/MPartners');

      $partner_ids = $this->input->post('partner_id');
      foreach ($partner_ids as $key => $partner_id) {
        if ($partner_id != '0') {
          $partner = $this->MPartners->get_partners(array('id' => $partner_id));

          if (!empty($partner)) {
            $partner = $partner[0];
            $partner_email = $partner->p_email;

            $company = $this->Mcompanies->get_company(array('id' => $id));

            // --- >> Send mail to partner

            if ($partner_email && !empty($company)) {
              $company = $company[0];

              $mail_data = array(
                'company_name' => $company->company_name,
                'first_name' => $company->first_name,
                'last_name' => $company->last_name,
                'email' => $company->email,
                'website' => $company->website,
                'address' => $company->address,
                'city' => $company->city,
                'zipcode' => $company->zipcode,
                'phone' => $company->phone,
                'username' => $company->username,
                'password' => $company->password
              );
              $mail_body = $this->load->view('mail_templates/rp_client_assigned', $mail_data, true);
              send_email($partner_email, $this->config->item('no_reply_email'), _('OBS Admin has updated Paid Status'), $mail_body, NULL, NULL, NULL, 'no_reply', 'partner', 'company_status_updated');
            }
          }
        }
      }

      redirect('mcp/companies/update/' . $id);
    }

    if ($this->input->post('assign_affiliate')) {
      $id = $a['id'] = $this->input->post('a_company_id');
      $a['affiliate_id'] = $this->input->post('affiliate_id');
      $a['affiliate_status'] = $this->input->post('affiliate_status');
      $this->Mcompanies->update($a);

      // --- >> Fetch Partner & Company Data

      $this->load->model('mcp/MAffiliates');
      $affiliate = $this->MAffiliates->get_affiliates(array('id' => $this->input->post('affiliate_id')));
      if (!empty($affiliate)) {
        $affiliate = $affiliate[0];
        $affiliate_email = $affiliate->a_email;

        $company = $this->Mcompanies->get_company(array('id' => $id));
        $company = $company[0];
      }

      redirect('mcp/companies/update/' . $id);
    }

    if ($this->input->post('submit_ibsoft')) {
      $id = $a['id'] = $this->input->post('ib_company_id');
      $a['ibsoft_active'] = $this->input->post('ibsoft_active');
      $a['email_to_send'] = $this->input->post('email_to_send');
      $a['client_number'] = $this->input->post('client_number');
      $this->Mcompanies->update($a);

      redirect('mcp/companies/update/' . $id);
    }

    if ($this->input->post('update_addon_cost')) {
      $id = $this->input->post('addon_company_id');
      $hide_bp_intro = $this->input->post("hide_bp_intro");
      if (isset($hide_bp_intro)) {
        $hide_bp_intro = $this->input->post("hide_bp_intro");
      } else {
        $hide_bp_intro = 0;
      }
      $hiddenfield   = $this->input->post("hiddenfield");
      if (($hiddenfield == 1 && $hide_bp_intro == 0) || ($hiddenfield == 0 && $hide_bp_intro == 1)) {
        $this->Mcompanies->update_all_check($id);
      }

      if ($this->input->post('easing_measure') == "1") {
        $er_arr = array('hide_bp_intro' => $hide_bp_intro, 'easing_measure' => "1", 'show_simple_list' => "1", "task_haccp" => "1");
      } else {
        $er_arr = array('hide_bp_intro' => $hide_bp_intro, 'easing_measure' => "0");
      }

      $this->Mcompanies->update_hide_bp_intro($er_arr, $id);

      if ($this->input->post('activate_easybutler') == "1") {
        $activate_easybutler = "1";
      } else {
        $activate_easybutler = "0";
      }

      if ($this->input->post('easybutler_order_app') == "1") {
        $easybutler_order_app = "1";
      } else {
        $easybutler_order_app = "0";
      }
      $easybutler_status = array(
        'activate_easybutler' => $activate_easybutler,
        'easybutler_order_app' => $easybutler_order_app
      );
      $gs['easybutler_status'] = json_encode($easybutler_status);
      if ($this->input->post('haccp_addon') == "1") {
        $haccp_addon = "1";
      } else {
        $haccp_addon = "0";
      }
      $gs['haccp_addon'] = $haccp_addon;

      $cost_price_status = $this->input->post('cost_price_status') ?  implode("#", $this->input->post('cost_price_status')) : '';
      $gs['cost_price_status'] = $cost_price_status;
      $gs['show_week_menu'] = ($this->input->post('show_week_menu')) ? $this->input->post('show_week_menu') : '0';
      $gs['show_infodesk'] = ($this->input->post('show_infodesk')) ? $this->input->post('show_infodesk') : '0';
      $gs['show_menukartt'] = ($this->input->post('show_menukartt')) ? $this->input->post('show_menukartt') : '0';
      $show_menukartt_maker = ($this->input->post('show_menukartt_maker')) ? $this->input->post('show_menukartt_maker') : 0;
      $sub_and_div = array();
      if (isset($show_menukartt_maker)) {
        $sub_and_div = $this->db->select('id')->get_where('company', array('parent_id' => $id))->result_array();
        $sub_and_div = array_column($sub_and_div, 'id');
        array_push($sub_and_div, $id);
        $show_menukartt_maker_upd = array('show_menukartt_maker' => $show_menukartt_maker);
        $this->db->where_in('id', $sub_and_div);
        $this->db->update('company', $show_menukartt_maker_upd);
      }
      $this->Mcompanies->update_company($id, $gs);

      redirect('mcp/companies/update/' . $id);
    }

    if ($this->input->post('image_name8')) {

      $this->image = $this->input->post('image_name8');
      $image_file = file_get_contents(base_url() . 'assets/temp_uploads/' . $this->input->post('image_name8'));
      file_put_contents(dirname(__FILE__) . '/../../../assets/mcp/images/sheet_banner/' . $this->image, $image_file);

      $result = $this->Mgeneral_settings->update_sheet_banner_settings($id, $this->input->post('image_name8'));
      redirect('mcp/companies/update/' . $id);
    }

    $data['tempUrl']   = $this->tempUrl;
    $data['content']   = $this->Mcompanies->get_company(array('id' => $id));

    $data['divisions']   = $this->Mcompanies->get_division(array('parent_id' => $id));

    if (!empty($data['content']) && isset($data['content'][0]->type_id) && $data['content'][0]->theme == '') {
      $data['theme_as_per_type_id'] = $this->Mcompanies->get_theme_as_per_type_id($data['content'][0]->type_id);
    }

    $data['labeler_settings'] = $this->Mgeneral_settings->get_general_settings(array('company_id' => $id));
    $data['datalogger'] = $this->Mgeneral_settings->dataloggers($id);
    $data['api'] = $this->Mapi->get_company_api($id);
    $data['addons'] = $this->Maddon->get_addons();

    $data['system_info'] = $this->Mcompanies->get_company_system_info();
    $data['package'] = $this->Mpackage->select();
    $data['country'] = $this->Mcountry->select();
    $data['company_type'] = $this->Mcompany_type->select(array('status' => 'ACTIVE', 'grp_typ' => $data['content'][0]->comp_grp));
    $data['company_type_group'] = $this->Mcompany_type->all_comp_grp();

    $data['account_types'] = $this->Mcompanies->get_account_types();
    $this->load->model('mcp/MPartners');
    $data['partners'] = $this->MPartners->get_partners(array('p_status' => 1));

    $this->load->model('mcp/MAffiliates');
    $data['affiliates'] = $this->MAffiliates->get_affiliates(array('a_status' => 1));

    $data['third_party_connections'] = $this->Mcompanies->get_all_third_party();
    $data['header'] = $this->template . '/header';
    $data['main'] = $this->template . '/more_info';
    $data['footer'] = $this->template . '/footer';

    $this->load->vars($data);
    $this->load->view($this->template . '/mcp_view');
  }

  function validate()
  {
    $data = '';
    if ($this->input->post('check') == 'email') {
      $text = array();
      $text['email'] = $this->input->post('email');
      $result = $this->Mcompanies->get_company(array('email' => $text['email']));
      $data = '';

      if (!empty($result)) {
        $data = 'duplicate';
      } else {
        $data = 'notexist';
      }
    }

    if ($this->input->post('check') == 'username') {
      $text = array();
      $text['username'] = $this->input->post('username');
      $result = $this->Mcompanies->get_company($text);

      if (!empty($result)) {
        $data = 'duplicate';
      } else {
        $data = 'notexist';
      }
    }

    echo json_encode(array("RESULT" => $data));
    exit;
  }

  function company_status()
  {
    $data['id'] = $this->input->post('id');
    $data['status'] = $this->input->post('status');
    $data["approved"] = "1";
    $result = $this->Mcompanies->update($data);
    if ($result) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  function change_data_type()
  {
    $data['id'] = $this->input->post('id');
    $data['data_type'] = $this->input->post('data_type');
    $result = $this->Mcompanies->update_products_as_per_compnay_types($data);
    if ($result) {
      $result1 = $this->Mcompanies->update($data);
      if ($result1) {
        echo json_encode(array('RESULT' => "success"));
      } else {
        echo json_encode(array('RESULT' => "fail"));
      }
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   * This public function is used to change the account type of given company (also add trial date too)
   * @access public
   */
  function change_ac_type()
  {
    $data['id'] = $this->input->post('id');
    $data['ac_type_id'] = $this->input->post('ac_type_id');
    // fetching current account type
    $this->db->select('ac_type_id');
    $this->db->where('id', $data['id']);
    $current_ac_type_id = $this->db->get('company')->result();

    if ($current_ac_type_id['0']->ac_type_id == 1 && ($this->input->post('ac_type_id') == 2 || $this->input->post('ac_type_id') == 3)) {
      $data['on_trial'] = "1";
      $data['trial'] = date("Y-m-d H:i:s", strtotime("+1 month", time()));
    } else {
      $data['on_trial'] = "0";
      $this->db->where('company_id', $data['id']);
      $this->db->update('general_settings', array('show_hide_bp_shop' => 'detail_shop', 'shop_testdrive' => '1', 'allow_orders_bo' => '1', 'portal_free_order_type' => 'pickup', 'portal_free_ordertime' => '2'));
    }
    if (($current_ac_type_id['0']->ac_type_id == 4 || $current_ac_type_id['0']->ac_type_id == 5 || $current_ac_type_id['0']->ac_type_id == 6) &&  ($data['ac_type_id'] == 7)) {
      $res = $this->Mgeneral_settings->basic_to_light($data['id']);
    }

    $data['obsdesk_status'] = $this->desk_status($this->input->post('ac_type_id'));
    $result = $this->Mcompanies->update($data);
    if ($result) {
      $this->db->select('ac_type_id,type_id');
      $ac_type = $this->db->get_where('company', array('id' => $data['id']))->result();
      if (!empty($ac_type) && ($ac_type[0]->ac_type_id == "1")) {
        $easybutler_status = array(
          'activate_easybutler' => "0",
          'easybutler_order_app' => "0"
        );
        $a['id'] = $data['id'];
        $a['easybutler_status'] = json_encode($easybutler_status);
        $a['show_menukartt'] = "0";
        $a['show_menukartt_maker']  = "0";
        $this->Mcompanies->update($a);
      }
      if (!empty($ac_type) && ($ac_type[0]->ac_type_id == 4 || $ac_type[0]->ac_type_id == 5 || $ac_type[0]->ac_type_id == 6 || $ac_type[0]->ac_type_id == 7)) {
        $type_id = $ac_type[0]->type_id;
        $type_id = explode('#', $type_id);
        if ($ac_type[0]->ac_type_id == '7' && (!in_array('20', $type_id) || !in_array('27', $type_id) || !in_array('28', $type_id))) {
          $this->db->where('company_id', $data['id']);
          $this->db->update('general_settings', array('hide_bp_intro' => '1'));
        }
        if (in_array('20', $type_id) || in_array('27', $type_id)) {
          $update_desk_arr = array(
            'obsdesk_status' => 1
          );
          $this->db->where('id', $data['id']);
          $this->db->update('company', $update_desk_arr);
        }
        if ($ac_type[0]->ac_type_id == 5) {
          if (in_array('20', $type_id) || in_array('27', $type_id) || in_array('28', $type_id)) {
            $this->db->where('company_id', $data['id']);
            $this->db->update('general_settings', array('easing_measure' => '0'));
          }
        }
        if (in_array('20', $type_id) || in_array('27', $type_id) || in_array('28', $type_id)) {
          $this->db->where('company_id', $data['id']);
          $this->db->update('general_settings', array('show_traces' => '1'));
        }
        if ($ac_type[0]->ac_type_id != 1) {
          if (!in_array('20', $type_id) || !in_array('27', $type_id)) {
            $easybutler_status = array(
              'easybutler_status' => '{"activate_easybutler":"1","easybutler_order_app":"0"}'
            );
            $this->db->where('id', $data['id']);
            $this->db->update('company', $easybutler_status);
          }
        }
        $insrt_easybutler_array = array(
          'company_id' => $data['id']
        );
        $this->db->insert('easybutler_settings', $insrt_easybutler_array);
        $this->db->insert('menucard_layout_setting', $insrt_easybutler_array);
        $this->db->insert('allergenencard_settings', $insrt_easybutler_array);
        $this->load->model('mcp/Mcompanies');
        $this->Mcompanies->insert_feedback_ques_row($data['id'], $ac_type[0]->type_id);
      }

      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   * This function is sued to change shop status of company at Bestelonline
   */
  function change_bo_status()
  {

    $companyId = $this->input->post('id');
    $status = $this->input->post('value');
    $gs['shop_testdrive'] = $status;
    $result = $this->Mgeneral_settings->update_company_general_settings($companyId, $gs);
    if ($result) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  function obsdesk_status()
  {
    $data['id'] = $this->input->post('id');
    $data['obsdesk_status'] = $this->input->post('status');

    $result = $this->Mcompanies->update($data);

    if ($result) {
      $desk_setting = $this->db->get_where("desk_settings", array("company_id" => $data['id']))->result_array();
      if (empty($desk_setting)) {
        $this->db->insert("desk_settings", array("company_id" => $data['id']));
      }

      $desk_section_design = $this->db->get_where("desk_section_design", array("company_id" => $data['id']))->result_array();
      if (empty($desk_section_design)) {
        $this->db->insert("desk_section_design", array("company_id" => $data['id']));
      }

      $company_info = $this->Mcompanies->get_company(array('id' => $data['id']));

      $desk_url = '';
      $msg = '';

      if ($data['obsdesk_status']) {
        $msg = _('OBS Admin has updated desk status. Please Check') . ':';
        $msg = _('You can open the link below on your tablet. In that way your clients can immediately can check the allergens from a specific product.') . ' :';
        $desk_url = _("Desk url: ") . '<a href="' . $this->config->item('obs-desk-root-url') . $company_info['0']->company_slug . '" >' . $this->config->item('obs-desk-root-url') . $company_info['0']->company_slug . '</a>';
      } else {
        $msg = _('OBS Admin has updated desk status. Please Check') . ':';
        $desk_url = _("Your Desk is deactivated by admin.");
      }

      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  function detail($company_id)
  {
    echo $company_id;
  }


  function get_companies_without_logo()
  {

    $result = $this->Mcompanies->get_companies_without_logo();
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle(_('Company without logos'));
    $counter = 1;
    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('Company id'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Company'))->getStyle('B' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('C' . $counter, _('Company Type'))->getStyle('C' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('D' . $counter, _('City'))->getStyle('D' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('E' . $counter, _('Registraion Date'))->getStyle('E' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('F' . $counter, _('Type'))->getStyle('F' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('G' . $counter, _('Status'))->getStyle('H' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('H' . $counter, _('DESK STATUS'))->getStyle('G' . $counter)->getFont()->setBold(true);


    if (!empty($result)) {
      foreach ($result as $result_row) {
        $counter++;
        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $result_row['id']);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $result_row['company_name']);
        $this->excel->getActiveSheet()->setCellValue('C' . $counter, $result_row['company_type_name']);
        $this->excel->getActiveSheet()->setCellValue('D' . $counter, $result_row['city']);
        $this->excel->getActiveSheet()->setCellValue('E' . $counter, $result_row['registration_date']);
        $this->excel->getActiveSheet()->setCellValue('F' . $counter, $result_row['ac_title']);
        $this->excel->getActiveSheet()->setCellValue('G' . $counter, (($result_row['status']) ? "Active" : "Inactive"));
        $this->excel->getActiveSheet()->setCellValue('H' . $counter, (($result_row['obsdesk_status']) ? "Active" : "Inactive"));
      }
    }
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(100);

    $datestamp = date("d-m-Y");
    $filename = "Company-without-logo-Info-" . $datestamp . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }


  function download_client_excel()
  {

    $datestamp = date("d-m-Y");
    $filename = "Company-Info-" . $datestamp . ".xls";
    $query = ("
						SELECT `company`.`id`,
								`company`.`company_name`,
								`company_type`.`company_type_name`,
								`packages`.`package_name`,
								`company`.`role`,
								`company`.`first_name`,
								`company`.`last_name`,
								`company`.`email`,
								`company`.`vat`,
								`company`.`address`,
								`company`.`city`,
								`country`.`country_name`,
								`company`.`zipcode`,
								`company`.`phone`,
								`company`.`website`,
								`company`.`username`,
								`company`.`password`,
								`account_type`.`ac_title`,
								`company`.`obsdesk_status`,
								`company`.`status`,
								`company`.`admin_remarks`,
								`general_settings`.`language_id`							
						FROM `company` 
						LEFT JOIN `account_type` ON `company`.`ac_type_id` = `account_type`.`id`
						LEFT JOIN `company_type` ON `company`.`type_id` = `company_type`.`id`
						LEFT JOIN `country` ON `company`.`country_id` = `country`.`id`
						LEFT JOIN `packages` ON `company`.`packages_id` = `packages`.`id`
						LEFT JOIN `general_settings` ON  `company`.`id` = `general_settings`.`company_id`
					");
    $result = $this->db->query($query)->result_array();

    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle(_('Company Categories details'));

    $counter = 1;
    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('Company id'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Company'))->getStyle('B' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('C' . $counter, _('Company Type'))->getStyle('C' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('D' . $counter, _('Package'))->getStyle('D' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('E' . $counter, _('Role'))->getStyle('E' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('F' . $counter, _('First Name'))->getStyle('F' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('G' . $counter, _('Last Name'))->getStyle('G' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('H' . $counter, _('Email'))->getStyle('H' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('I' . $counter, _('Vat'))->getStyle('I' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('J' . $counter, _('Address'))->getStyle('J' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('K' . $counter, _('City'))->getStyle('K' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('L' . $counter, _('Country'))->getStyle('L' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('M' . $counter, _('Lang'))->getStyle('M' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('N' . $counter, _('Zipcode'))->getStyle('N' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('O' . $counter, _('Phone'))->getStyle('O' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('P' . $counter, _('Website'))->getStyle('P' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('Q' . $counter, _('Username'))->getStyle('Q' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('R' . $counter, _('Password'))->getStyle('R' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('S' . $counter, _('Type'))->getStyle('S' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('T' . $counter, _('DESK STATUS'))->getStyle('T' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('U' . $counter, _('Favorites'))->getStyle('U' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('V' . $counter, _('Remarks'))->getStyle('V' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('W' . $counter, _('Status'))->getStyle('W' . $counter)->getFont()->setBold(true);


    if (!empty($result)) {
      foreach ($result as $result_row) {

        $tot_fav = $this->Mcompanies->get_total_pws_and_fav($result_row['id']);
        $counter++;
        $language = '';
        if ($result_row['language_id'] == '1') {
          $language = 'EN';
        } elseif ($result_row['language_id'] == '2') {
          $language = 'DU';
        } elseif ($result_row['language_id'] == '3') {
          $language = 'FR';
        }
        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $result_row['id']);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $result_row['company_name']);
        $this->excel->getActiveSheet()->setCellValue('C' . $counter, $result_row['company_type_name']);
        $this->excel->getActiveSheet()->setCellValue('D' . $counter, $result_row['package_name']);
        $this->excel->getActiveSheet()->setCellValue('E' . $counter, $result_row['role']);
        $this->excel->getActiveSheet()->setCellValue('F' . $counter, $result_row['first_name']);
        $this->excel->getActiveSheet()->setCellValue('G' . $counter, $result_row['last_name']);
        $this->excel->getActiveSheet()->setCellValue('H' . $counter, $result_row['email']);
        $this->excel->getActiveSheet()->setCellValue('I' . $counter, $result_row['vat']);
        $this->excel->getActiveSheet()->setCellValue('J' . $counter, $result_row['address']);
        $this->excel->getActiveSheet()->setCellValue('K' . $counter, $result_row['city']);
        $this->excel->getActiveSheet()->setCellValue('L' . $counter, $result_row['country_name']);
        $this->excel->getActiveSheet()->setCellValue('M' . $counter, $language);
        $this->excel->getActiveSheet()->setCellValue('N' . $counter, $result_row['zipcode']);
        $this->excel->getActiveSheet()->setCellValue('O' . $counter, $result_row['phone']);
        $this->excel->getActiveSheet()->setCellValue('P' . $counter, $result_row['website']);
        $this->excel->getActiveSheet()->setCellValue('Q' . $counter, $result_row['username']);
        $this->excel->getActiveSheet()->setCellValue('R' . $counter, ' ' . $result_row['password']);
        $this->excel->getActiveSheet()->setCellValue('S' . $counter, $result_row['ac_title']);
        $this->excel->getActiveSheet()->setCellValue('T' . $counter, (($result_row['obsdesk_status']) ? "Active" : "Inactive"));
        $this->excel->getActiveSheet()->setCellValue('U' . $counter, $tot_fav);
        $this->excel->getActiveSheet()->setCellValue('V' . $counter, $result_row['admin_remarks']);
        $this->excel->getActiveSheet()->setCellValue('W' . $counter, (($result_row['status']) ? "Active" : "Inactive"));
      }
    }
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(100);


    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }

  function download_client_system_detail_excel()
  {
    $blank_val = "---";
    $datestamp = date("d-m-Y");
    $filename = "Company-system-detail-" . $datestamp . ".xls";
    $query = ("
						SELECT  `company`.`id`,
								`company`.`company_name`,
								`company_type`.`company_type_name`,
								`company`.`city`,
								`account_type`.`ac_title`,
								`company`.`status`,
								`company`.`email`,
								`company`.`system_selected`
						FROM `company`
						LEFT JOIN `account_type` ON `company`.`ac_type_id` = `account_type`.`id`
						LEFT JOIN `company_type` ON `company`.`type_id` = `company_type`.`id`
					ORDER BY `company`.`id`
					");
    $result = $this->db->query($query)->result_array();
    if (!empty($result)) {
      foreach ($result as $result_key => $result_val) {
        if (json_decode($result_val['system_selected'])[0] == 0) {
          $result[$result_key]['system_name'] = $blank_val;
        } else {
          $system_query = ("
							SELECT  
									`company_system`.`system_name`
							FROM `company_system`
							WHERE `company_system`.`id`=" . json_decode($result_val['system_selected'])[0] . "
						");
          $system_name = $this->db->query($system_query)->result_array();
          $result[$result_key]['system_name'] = $system_name[0]['system_name'];
        }
      }
    }
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle("XLS export van systemen");

    $counter = 1;
    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('Bedrijf'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Company'))->getStyle('B' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('C' . $counter, _('Company Type'))->getStyle('C' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('D' . $counter, _('Stad'))->getStyle('D' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('E' . $counter, _('Type'))->getStyle('E' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('F' . $counter, _('Status'))->getStyle('F' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('G' . $counter, _('Email'))->getStyle('G' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('H' . $counter, _('System'))->getStyle('H' . $counter)->getFont()->setBold(true);

    if (!empty($result)) {
      foreach ($result as $result_row) {
        $counter++;
        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $result_row['id']);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $result_row['company_name']);
        $this->excel->getActiveSheet()->setCellValue('C' . $counter, $result_row['company_type_name']);
        $this->excel->getActiveSheet()->setCellValue('D' . $counter, $result_row['city']);
        $this->excel->getActiveSheet()->setCellValue('E' . $counter, $result_row['ac_title']);
        $this->excel->getActiveSheet()->setCellValue('F' . $counter, (($result_row['status']) ? "Active" : "Inactive"));
        $this->excel->getActiveSheet()->setCellValue('G' . $counter, $result_row['email']);
        $this->excel->getActiveSheet()->setCellValue('H' . $counter, $result_row['system_name']);
      }
    }

    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }

  /*===================create slug======================*/

  function create_slug($companyname)
  {
    $slug_str = strtolower(trim($companyname));

    $slug_str = preg_replace('/\s+/', '-', $slug_str);
    $slug_str = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $slug_str));
    $slug_str = preg_replace('/-+/', '-', $slug_str);
    $slug_str = rtrim($slug_str, "-");


    $company_slugs_array = $this->db->select('company_slug')->get('company')->result();
    $company_slugs = array();
    foreach ($company_slugs_array as $company_slug) {
      $company_slugs[] = $company_slug->company_slug;
    }
    $old_str = $slug_str;
    for ($company_counter = 2;; $company_counter++) {
      if (in_array($slug_str, $company_slugs)) {
        $slug_str = $old_str . '-' . $company_counter;
      } else {
        break;
      }
    }
    return $slug_str;
  }

  function download_active_emails()
  {
    $Companies = $this->Mcompanies->get_company(array('approved' => 1, 'status' => 1), array('id' => 'DESC'));
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle(_('ACTIVE Companies Email'));

    $counter = 1;

    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('Company ID'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Email Address'))->getStyle('A' . $counter)->getFont()->setBold(true);

    if (!empty($Companies)) {
      $counter = 1;
      foreach ($Companies as $C) {
        $counter++;

        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $C->id);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $C->email);
      }
    }

    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

    $datestamp = date("d-m-Y");
    $filename = "Active-Email-" . $datestamp . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }

  function download_no_images()
  {
    $Companies = $this->Mcompanies->get_company_noimages();
    $account_types = $this->Mcompanies->get_account_types();
    $comp_type = $this->Mcompanies->get_company_type();


    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle(_('NOIMAGES found List'));

    $counter = 1;

    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('ID'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Shop Name'))->getStyle('B' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('C' . $counter, _('Type'))->getStyle('C' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('D' . $counter, _('City'))->getStyle('D' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('E' . $counter, _('Email address'))->getStyle('E' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('F' . $counter, _('Register date'))->getStyle('F' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('G' . $counter, _('License type'))->getStyle('G' . $counter)->getFont()->setBold(true);

    if (!empty($Companies)) {
      $counter = 1;
      foreach ($Companies as $C) {
        $counter++;
        foreach ($account_types as $type) {
          if ($C->ac_type_id == $type->id) {
            $acc_type = $type->ac_title;
            break;
          }
        }
        $type = explode('#', $C->type_id);
        $company_types = array();
        foreach ($type as $key => $value) {
          foreach ($comp_type as $type_comp_name) {
            if ($value == $type_comp_name->id) {
              array_push($company_types, $type_comp_name->company_type_name);
            }
          }
        }

        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $C->id);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $C->company_name);
        $this->excel->getActiveSheet()->setCellValue('C' . $counter, implode(',', $company_types));
        $this->excel->getActiveSheet()->setCellValue('D' . $counter, $C->city);
        $this->excel->getActiveSheet()->setCellValue('E' . $counter, $C->email);
        $this->excel->getActiveSheet()->setCellValue('F' . $counter, $C->registration_date);
        $this->excel->getActiveSheet()->setCellValue('G' . $counter, $acc_type);
      }
    }

    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

    $datestamp = date("d-m-Y");
    $filename = "NO-IMAGES-basic/pro-" . $datestamp . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }
  function download_light_admins()
  {
    $Companies = $this->Mcompanies->get_light_admins();
    $account_types = $this->Mcompanies->get_account_types();
    $comp_type = $this->Mcompanies->get_company_type();
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle(_('Overview of backlinks'));

    $counter = 1;

    $this->excel->getActiveSheet()->setCellValue('A' . $counter, _('ID'))->getStyle('A' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('B' . $counter, _('Shop Name'))->getStyle('B' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('C' . $counter, _('Type'))->getStyle('C' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('D' . $counter, _('City'))->getStyle('D' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('E' . $counter, _('Email address'))->getStyle('E' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('F' . $counter, _('Register date'))->getStyle('F' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('G' . $counter, _('Backlink'))->getStyle('G' . $counter)->getFont()->setBold(true);
    $this->excel->getActiveSheet()->setCellValue('H' . $counter, _('Social Backlink'))->getStyle('H' . $counter)->getFont()->setBold(true);

    if (!empty($Companies)) {
      $counter = 1;
      foreach ($Companies as $C) {
        $counter++;
        foreach ($account_types as $type) {
          if ($C->ac_type_id == $type->id) {
            $acc_type = $type->ac_title;
            break;
          }
        }
        $type = explode('#', $C->type_id);
        $company_types = array();
        foreach ($type as $key => $value) {
          foreach ($comp_type as $type_comp_name) {
            if ($value == $type_comp_name->id) {
              array_push($company_types, $type_comp_name->company_type_name);
            }
          }
        }

        $this->excel->getActiveSheet()->setCellValue('A' . $counter, $C->id);
        $this->excel->getActiveSheet()->setCellValue('B' . $counter, $C->company_name);
        $this->excel->getActiveSheet()->setCellValue('C' . $counter, implode(',', $company_types));
        $this->excel->getActiveSheet()->setCellValue('D' . $counter, $C->city);
        $this->excel->getActiveSheet()->setCellValue('E' . $counter, $C->email);
        $this->excel->getActiveSheet()->setCellValue('F' . $counter, $C->registration_date);
        $this->excel->getActiveSheet()->setCellValue('G' . $counter, $C->backlink);
        $this->excel->getActiveSheet()->setCellValue('H' . $counter, $C->soc_med_link);
      }
    }

    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);

    $datestamp = date("d-m-Y");
    $filename = "Social-backlink-overview-" . $datestamp . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    $objWriter->save('php://output');
  }
  function trial_date_insert()
  {
    $company_id = $this->input->post('company_id');
    $date = $this->input->post('date');
    $createDate = new DateTime($date);
    $trial1 = $createDate->format('d-m-Y');
    $data['trial'] = $this->input->post('date');
    $co_email = $this->input->post('co_email');
    $result = $this->Mcompanies->update_trial($data, $company_id);
    $from_mail = $this->config->item('no_reply_email');
    if ($result) {
      $mail_data = array(
        "date" => $trial1
      );
      $mail_body = $this->load->view('mail_templates/trial_prolongate_mail', $mail_data, true);

      $query = send_email($co_email, $from_mail, _("OBS: Company Trial Prolongated"), $mail_body, NULL, NULL, NULL, 'no_reply', 'company', 'company_trial_prolongated');
      if ($query)
        echo json_encode(array('RESULT' => "success"));
      else
        echo json_encode(array('RESULT' => "fail"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   * This function is used to end the trial of any particular company
   */
  public function trial_date_end()
  {
    $company_id = $this->input->post('company_id');
    $co_email = $this->input->post('co_email');

    $this->db->where('id', $company_id);
    if ($this->db->update('company', array('on_trial' => 0, 'trial' => '0000-00-00 00:00:00'))) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   * Fetch DESK status based on account type id
   * @param number $ac_type_id
   * @return number
   * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
   */
  function desk_status($ac_type_id = 0)
  {
    $desk_status = 0;

    if ($ac_type_id == 1 || $ac_type_id == 2 || $ac_type_id == 3) {
      $desk_status = 0;
    } elseif ($ac_type_id == 4 || $ac_type_id == 5 || $ac_type_id == 6 || $ac_type_id == 7) {
      $desk_status = 1;
    }

    return $desk_status;
  }

  function export_empty_recipes_xls($comp_id = 0)
  {

    if ($comp_id) {
      $products = $this->Mcompanies->get_empty_recipes_xls($comp_id);
      $product = $products[1];
      $category = $products[0];

      $product_no = count($product);

      $this->load->library('excel');
      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle(_('Overzicht recepturen'));

      $phpExcel = new PHPExcel();

      $styleArray = array(
        'font'  => array(
          'bold'  => true,
          'size'  => 12,
        )
      );

      $this->excel->getActiveSheet()->setCellValue('A' . '1', 'Overzicht recepturen nog in te geven (' . $product_no . '):');
      $this->excel->getActiveSheet()->getStyle('A' . '1')->applyFromArray($styleArray);
      $this->excel->getActiveSheet()->setCellValue('A' . '2', 'Gelieve de naam op te geven van de grondstof / naam fabrikant / hoeveelheid (in gram)');

      $counter = 3;
      if (!empty($product) && !empty($category)) {
        foreach ($category as $key => $value) {
          $counter = $counter + 1;

          $this->excel->getActiveSheet()->setCellValue('A' . $counter, $value);
          $this->excel->getActiveSheet()->getStyle('A' . $counter)->applyFromArray($styleArray);

          $counter = $counter + 2;

          foreach ($product as $key1 => $value1) {
            if ($value1['categories_id'] == $key && $value1['subcategories_id'] == -1) {
              $this->excel->getActiveSheet()->setCellValue('A' . $counter, $value1['proname']);
              $counter = $counter + 5;
            }
          }
          $subcat = $this->db->get_where('subcategories', array('categories_id' => $key))->result_array();

          foreach ($subcat as $key2 => $value2) {
            $this->excel->getActiveSheet()->setCellValue('A' . $counter, '-' . $value2['subname']);
            $this->excel->getActiveSheet()->getStyle('A' . $counter)->applyFromArray($styleArray);

            $counter = $counter + 2;

            foreach ($product as $key1 => $value1) {
              if ($value1['categories_id'] == $key && $value1['subcategories_id'] == $value2['id']) {
                $this->excel->getActiveSheet()->setCellValue('A' . $counter, $value1['proname']);
                $counter = $counter + 5;
              }
            }
          }
        }
      }
      $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);

      $datestamp = date("d-m-Y");
      $filename = "Export_Report_without_sheet-" . $datestamp . ".xls";

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $filename . '"');
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
      $objWriter->save('php://output');
    }
  }

  function aller_upload_banner($id)
  {
    $comp_id = $id;
    if ($this->input->post('image_name5')) {
      if ($this->input->post('transparency')) {
        $transparency = $this->input->post('transparency');
      } else {
        $transparency = 0;
      }
      $prefix = 'cropped_' . $comp_id . '_';
      $str = $this->input->post('image_name5');
      if (substr($str, 0, strlen($prefix)) == $prefix) {
        $str = substr($str, strlen($prefix));
      }


      if (isset($str) && $str != '') {
        $this->image = $comp_id . '_all_check' . $str;
        $image_file = file_get_contents(base_url() . 'assets/temp_uploads/' . $this->input->post('image_name5'));
        file_put_contents(dirname(__FILE__) . '/../../../assets/aller_checker_banner/' . $this->image, $image_file);

        $this->Mgeneral_settings->upload_aller_banner($comp_id, $this->image, $transparency);
      }
    }
    redirect('mcp/companies/update/' . $id);
  }

  function aller_upload_image($id)
  {
    $comp_id = $id;
    if ($this->input->post('image_name7')) {

      $prefix = 'cropped_' . $comp_id . '_';
      $str = $this->input->post('image_name7');
      if (substr($str, 0, strlen($prefix)) == $prefix) {
        $str = substr($str, strlen($prefix));
      }


      if (isset($str) && $str != '') {
        $this->image = $comp_id . '_all_check' . $str;
        $image_file = file_get_contents(base_url() . 'assets/temp_uploads/' . $this->input->post('image_name7'));
        file_put_contents(dirname(__FILE__) . '/../../../assets/aller_upload_image/' . $this->image, $image_file);

        $this->Mgeneral_settings->aller_upload_image($comp_id, $this->image);
      }
    }
    redirect('mcp/companies/update/' . $id);
  }

  function delete_banner()
  {

    $banner_name   = $this->input->post('banner_name');
    $company_id   = $this->input->post('company_id');
    $table_column   = $this->input->post('table_column');
    if ($table_column == 'aller_upload_image') {
      $target_dir   = FCPATH . 'assets/aller_upload_image/';
    } else if ($table_column == 'aller_banner_sheet') {
      $target_dir   = FCPATH . 'assets/aller_checker_banner/';
    } else if ($table_column == 'sheet_banner') {
      $target_dir   = FCPATH . 'assets/mcp/images/sheet_banner/';
    }
    $result     = $this->Mcompanies->delete_banner($banner_name, $company_id, $table_column);
    if ($result) {
      if (file_exists($target_dir . $banner_name)) {
        unlink($target_dir . $banner_name);
        echo "success";
        exit();
      }
    } else {
      echo "failed";
      exit();
    }
  }

  function update_tv_webshop_status()
  {
    $status   = $this->input->post('status');
    $column   = $this->input->post('column');
    $comp_id   = $this->input->post('comp_id');
    $result   = $this->Mcompanies->update_tv_webshop_status($comp_id, $column, $status);
    if ($result) {
      echo json_encode(array('status' => "success"));
    } else {
      echo json_encode(array('status' => "fail"));
    }
  }

  function update_partner_id_column()
  {
    $this->db->select('id, partner_id');
    $query = $this->db->get('company')->result_array();

    foreach ($query as $key => $value) {
      if ($value['partner_id'] != '0') {
        $this->db->where('id', $value['id']);
        $this->db->update('company', array('partner_id' => json_encode(array('0' => $value['partner_id']))));
      } else {
        $this->db->where('id', $value['id']);
        $this->db->update('company', array('partner_id' => ''));
      }
    }
  }

  /**
   *
   * Function to update each company type
   *
   */
  function update_theme($company_id = '')
  {
    if ($company_id) {
      $theme = $this->input->post('theme');
      $this->db->where('id', $company_id);
      $this->db->update('company', array('theme' => $theme));
      redirect(base_url() . "mcp/companies/update/" . $company_id);
    } else {
      redirect(base_url() . "mcp/companies");
    }
  }

  /**
   *
   * Function to get the division of the parent
   *
   */
  function get_division()
  {
    $parent_id = $this->input->post('parent');
    $company_id = $this->input->post('company_id');
    if ($parent_id) {
      $divisions = $this->Mcompanies->get_division(array('parent_id' => $parent_id));
    }
    $account_types = $this->Mcompanies->get_account_types();
    $company_types = $this->Mcompany_type->select(array('status' => 'ACTIVE'));
    $html = '';
    foreach ($divisions as $key => $division) {

      $html .= '<tr><td width="150px" height="30">' . $division->company_name . '</td>';
      $html .= '<td width="175px" height="30">' . $division->first_name . ' ' . $division->last_name . '</td>';
      $html .= '<td width="175px" height="30">' . $division->email . '</td>';
      $html .= '<td width="130px" height="30">' . $division->phone . '</td>';
      $html .= '<td width="120px" height="30">' . $division->city . '</td>';
      $html .= '<td width="175px" height="30"><select style="width:150px" class="textbox" type="select" id="ac_type_id" name="selected_ac_type_id" onchange="change_acc_type_sub(' . $division->id . ',this)">';

      foreach ($account_types as $key => $account_type) {

        if ($account_type->id == $division->ac_type_id) {
          $html .= '<option value=' . $account_type->id . ' selected="selected" >' . $account_type->ac_title . '</option>';
        } else {
          $html .= '<option value=' . $account_type->id . '>' . $account_type->ac_title . '</option>';
        }
      }
      $html .= '</select></td>';


      $html .= '<td width="175px" class = "select_type_id" >&nbsp;
                         <select style="width:190px" class="textbox select_comp_type" type="select" id="sub_type_id"  multiple onchange="company_type(' . $division->id . ',this)">
                                <option value="-1" style="background: none repeat scroll 0 0 #CCCCCC;">-- Select Company Type; --</option>';

      $company_types_sub = explode("#", $division->type_id);

      foreach ($company_types as $company_type) {

        if (in_array($company_type->id, $company_types_sub)) {

          $html .= '<option value=' . $company_type->id . ' selected="selected" >' . $company_type->company_type_name . '</option>';
        } else {

          $html .= '<option value=' . $company_type->id . ' >' . $company_type->company_type_name . '  </option>';
        }
      }
      $activate_easybutler = '';
      $easybutler_order_app = '';
      $show_menukartt = '';
      if (isset($division->easybutler_status) && (json_decode($division->easybutler_status)->activate_easybutler == 1)) {
        $activate_easybutler = "checked";
      }
      if (isset($division->easybutler_status) && (json_decode($division->easybutler_status)->easybutler_order_app == 1)) {
        $easybutler_order_app = "checked";
      }
      if (isset($division->show_menukartt) && $division->show_menukartt == 1) {
        $show_menukartt = "checked";
      }

      $html .= '<td style="padding-left:2px" width="150px">
                  <span>
                    <p><input type="checkbox" data-comp_id ="' . $division->id . '" ' . $activate_easybutler . ' class="activate_eb_sup">' . _("Act. EB") . '</p>
                    <p><input type="checkbox" data-comp_id ="' . $division->id . '" ' . $easybutler_order_app . ' class="activate_order_app_sup">' . _("Ord. App") . '</p>
                  </span>
              </td>';
      $html .= '<td style="padding-left:2px" width="150px">
                  <span>
                    <p><input type="checkbox" data-comp_id ="' . $division->id . '" ' . $show_menukartt . ' class="show_menucard">' . _("Show Menucard") . '</p>
                  </span>
              </td><td></td>';


      $inactive_selected = '';
      $active_selected = '';
      if ($division->status == 0) {
        $inactive_selected = 'selected="selected"';
      } else if ($division->status == 1) {
        $active_selected = 'selected="selected"';
      }

      $html .= '<td width="100px" height="30"><select name="status" id="status_' . $division->id . '" onchange="company_status(' . $division->id . ',this);"><option value="0" ' . $inactive_selected . '> INACTIVE</option><option value="1"' . $active_selected . '>ACTIVE</option></select></td>';

      $html .= '<td width="70px" height="30">&nbsp;<a class="sub" href="' . base_url() . 'mcp/companies/division_add_edit/act/edit/company_id/' . $company_id . '/subcomp_id/' . $division->parent_id . '/division_id/' . $division->id . '"><img src="' . base_url() . 'assets/mcp/images/update.png" title="Update"  width="16" height="16" border="0" style="cursor:pointer" /></a>&nbsp;&nbsp;<a href="' . base_url() . 'mcp/companies/delete/' . $division->id . '"><img src="' . base_url() . 'assets/mcp/images/delete1.png" title="Delete"  width="16" height="16" border="0" style="cursor:pointer" /></a></td><tr>';
    }

    if ($html == '') {
      $html = '<tr><td colspan="7" height="30" style="color:#FF0000">No Division added !!</td></tr>';
    }
    echo json_encode($html);
    exit();
  }

  /**
   *
   * Function to change the account type of company
   *
   */

  function change_acc_type_sub()
  {
    $data['id'] = $this->input->post('comp_id');
    $data['ac_type_id'] = $this->input->post('acc_type');
    $result = $this->Mcompanies->update($data);
    if ($result) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   *
   * Function to change the company type of company
   *
   */
  function change_company_type_sub()
  {
    $data['id'] = $this->input->post('comp_id');
    $data['type_id'] = implode("#", $this->input->post('type_id'));
    $result = $this->Mcompanies->update($data);
    if ($result) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  /**
   *
   * Function to change 
   *
   */
  function update_subcompany()
  {
    $data['id'] = $this->input->post('comp_id');
    if ($this->input->post('haccp')) {
      $data['haccp_status'] = $this->input->post('haccp_status');
    } elseif ($this->input->post('type_id')) {
      $data['type_id'] = implode("#", $this->input->post('type_id'));
    } elseif ($this->input->post('acc_type')) {
      $data['ac_type_id'] = $this->input->post('acc_type');
    }

    $result = $this->Mcompanies->update($data);

    if ($result) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  function update_easybutler_status()
  {
    $company_id = $this->input->post('company_id');
    $activate_easybutler = $this->input->post('activate_easybutler');
    $easybutler_order_app = $this->input->post('easybutler_order_app');
    if (isset($company_id) && $company_id != '') {
      $update_arr = json_encode(array('activate_easybutler' => $activate_easybutler, 'easybutler_order_app' => $easybutler_order_app));
      $this->db->where('id', $company_id);
      $this->db->update('company', array('easybutler_status' => $update_arr));

      if ($this->db->trans_status()) {
        echo json_encode(array('result' => _('Updated Sucessfully'), 'status' => 1));
      } else {
        echo json_encode(array('result' => _('Updation Failed'), 'status' => 0));
      }
    }
  }

  function update_show_menucard()
  {
    $company_id = $this->input->post('company_id');
    $show_menukartt = $this->input->post('show_menukartt');
    if (isset($company_id) && $company_id != '') {
      $this->db->where('id', $company_id);
      $this->db->update('company', array('show_menukartt' => $show_menukartt));

      if ($this->db->trans_status()) {
        echo json_encode(array('result' => _('Updated Sucessfully'), 'status' => 1));
      } else {
        echo json_encode(array('result' => _('Updation Failed'), 'status' => 0));
      }
    }
  }

  public function update_mailing_package()
  {
    $company_id = $this->input->post('company_id');
    $pack_id = $this->input->post('pack_id');

    $this->db->where('id', $company_id);
    if ($this->db->update('company', array('mailing_package' => $pack_id))) {
      echo json_encode(array('RESULT' => "success"));
    } else {
      echo json_encode(array('RESULT' => "fail"));
    }
  }

  function update_sho_leads()
  {
    if ($this->input->post()) {
      $comp_id        = $this->input->post('comp_id');
      $is_cho_checked = $this->input->post('is_sho_lead');
      echo $this->Mcompanies->update_company($comp_id, array('show_sho_leads' => $is_cho_checked));
      die;
    }
  }

  function check_email()
  {
    if ($this->input->post()) {
      $email        = $this->input->post('email');
      $comp_id      = $this->input->post('comp_id');
      if (isset($comp_id) && $comp_id != '') {
        $whr = array('email' => $email, 'id !=' => $comp_id);
      } else {
        $whr = array('email' => $email);
      }
      $comps = $this->Mcompanies->check_email_comp($whr);
      if (!empty($comps)) {
        echo 'duplicate_email';
      } else {
        echo 'not_found';
      }
      die;
    }
  }

  public function forget_password()
  {
    $id = $this->input->post('id');
    $token = $this->Mcompanies->store_token($id);
    $this->db->select('email');
    $this->db->where('id', $id);
    $email = $this->db->get('company')->row()->email;
    $parse_data['hello_txt'] = _('Hello');
    $parse_data['click_here'] = _('Click Here');
    $parse_data['follow_this_link'] = _('Follow this link to reset your password');
    $parse_data['thanks_regard'] = _('Thanks and Regards');
    $parse_data['token'] = $token;
    $parse_data['id'] = $id;
    /*=====lines tp parse mail======*/
    $message = $this->load->view('mail_templates/mail_templates_nl/forgot_password.php', $parse_data, true);
    /*=============================*/
    if ($message) {
      /*=====lines to send mail ======*/
      $From = $this->config->item('no_reply_email');
      $To = $email;
      $subject = _('Forgot Password Recovery Message');

      send_email($To, $From, $subject, $message, NULL, NULL, NULL, 'no_reply', 'company', 'forgot_password');
    }
  }

  function reg_bluecherry()
  {
    $id = $this->input->post('comp_id');
    $comp_data = $this->db->get_where('company', array('id' => $id))->row_array();
    $curl_data = array(
      'email'          => $comp_data['email'],
      'password'       => $id . '_bLuEcHeRrY_' . $id,
      'repeatPassword'   => $id . '_bLuEcHeRrY_' . $id,
      'title'             => 1,
      'firstname'     => $comp_data['first_name'],
      'lastname'         => $comp_data['last_name'],
      'locale'        => 'en-US',
      'mobile'           => $comp_data['phone'],
      'domain'            => 'sso.bluecherry.io'
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://sso.bluecherry.io/api/sso/account/register",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($curl_data),
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo false;
    } else {
      if ($response) {
        $this->db->select('count(*) as count');
        if ($this->db->get_where('datalogger_bluecherry', array('comp_id' => $id))->row()->count == 1) {
          $insert_data = array(
            'bluecherry_reg_reponse' =>  $response,
            'date_updated' => date("Y-m-d H:i:s")
          );
          echo $this->db->update('datalogger_bluecherry', $insert_data, "comp_id = $id") ? $response : 'false';
        } else {
          $insert_data = array(
            'comp_id' => $id,
            'bluecherry_reg_reponse' =>  $response,
            'bluecherry_activation' => "1"
          );
          echo $this->db->insert('datalogger_bluecherry', $insert_data) ? $response : 'false';
        }
      } else {
        echo 'false';
      }
    }
  }



  function get_types()
  {
    $this->db->select('id,company_type_name');
    $this->db->where('grp_typ', $this->input->post('grp_id'));
    $result = $this->db->get('company_type')->result_array();
    echo json_encode($result);
  }


  function update_comp_grp()
  {

    $this->db->select('id, type_id');
    $result = $this->db->get('company')->result_array();
    foreach ($result as $key => $value) {
      $comp_grp = 0;
      // echo $value['id'];
      $type_id = explode('#', $value['type_id']);
      // echo "<pre>";
      // print_r ($value['type_id']);
      // echo "</pre>";
      if (!array_diff($type_id, array(20, 27, 28))) {
        $comp_grp = 1;
      } else if (!array_diff($type_id, array(1, 2, 3, 8, 9, 10, 11, 12, 13, 19, 23, 24, 25))) {
        $comp_grp = 2;
      } else if (!array_diff($type_id, array(7, 14, 15, 16, 17, 18, 21, 22, 26))) {
        $comp_grp = 3;
      }
      // echo "<pre>";
      // print_r ($comp_grp);
      // echo "</pre>";
      $this->db->where('id', $value['id']);
      $this->db->update('company', array('comp_grp' => $comp_grp));
      die;
    }
  }

  function update_fav_setting()
  { 
    $company_id = $this->input->post('company_id');
    $show_fav_list = $this->input->post('show_fav_list');
    $fav_list = json_encode(array_unique(array_filter($this->input->post('fav_list'))));
   
    // new fav list added
    $this->db->where('id', $company_id);
    $updated = $this->db->update('company', array('show_fav_list' => $show_fav_list, 'fav_list'=> $fav_list ));

    if ($updated) {
      echo json_encode(array('result' => _('Updated Sucessfully'), 'status' => 1));
    } else {
      echo json_encode(array('result' => _('Updation Failed'), 'status' => 0));
    }
  }
}
