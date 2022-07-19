<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; UTF-8" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/css/tabs-frontend.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/css/core.css">
<script src="<?php echo base_url();?>assets/js/general_functions.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/hint.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.tooltip.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.tooltip.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/tabs-frontend.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/util.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/validator.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/javascript-md5.js" type="text/javascript"></script>
<script type="text/javascript">
		function renew_captcha()
		{
		    jQuery.post('<?php echo base_url();?>welcome/renew_captcha',{},function(data){
			    //alert(data);
			    $('#cap-img').html(data);
			});
		}
</script>
<title>ONLINE BESTELSYSTEEM VOOR KLEINHANDELAREN</title>
</head>
<body>
<style type="text/css">
.register td{
	height:30px;
	padding-left:10px
}
.red_star{
	color:#000000;
}
</style>
<link type="image/x-icon" href="../favicon.ico" rel="shortcut icon">
<script src="<?php echo base_url();?>assets/js/jquery-1.4.2.js" type="text/javascript"></script>
<div class="mainwrapper">
  <div class="shadowbg">
    <div class="maincon">
      <div class="topbg">
        <div class="topinner">
          <div class="logocon"><a href="#"><img alt="" src="<?php echo base_url();?>assets/images/logo.gif"></a></div>
          <div class="toprgt">
            <div class="toplinks">
              <form onsubmit="return validate_login();" action="<?php echo base_url();?>cp/login/validate" enctype="multipart/form-data" method="post" id="frm_login" name="frm_login">
                <input type="hidden" value="register" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                <input type="text" onblur="this.value=(this.value=='') ? 'Gebruikersnaam':this.value" onfocus="this.value=(this.value=='Gebruikersnaam') ? '' : this.value" value="Gebruikersnaam" class="inputbg" id="username" name="username">
                <input type="text" onfocus="change(event)" value="Wachtwoord" class="inputbg" id="password" name="password">
                <input type="image" src="<?php echo base_url();?>assets/images/submit.jpg" id="btn_register" name="btn_register">
                <input type="hidden" value="login" id="act" name="act">
              </form>
              <script type="text/javascript" language="javascript">
				 	function validate_login(){
						var user = document.getElementById('login_username').value;
						var pass = document.getElementById('login_password').value;
						if(user == "" || user == "Gebruikersnaam"){
							alert("Gelieve uw gebruikersnaam in te geven aub");
							document.getElementById('login_username').focus();
							return false;
						}
						if(pass == "" || pass == "Wachtwoord"){
							alert("Gelieve uw wachtwoord in te geven aub");
							document.getElementById('login_password').focus();
							return false;
						}
						return true;
					}
					
					function change(event){
                        var evt = (window.event) ? window.event : event;
                        var elem = (evt.srcElement) ? evt.srcElement : evt.target;
                        var inputID = $(elem).attr('id');
                        var inputValue = $(elem).attr('value');
                        var ClassName = $(elem).attr('class');
						if(inputValue == 'Wachtwoord'){
                                var passwordInput = document.createElement('input');
                                passwordInput.setAttribute("id",inputID);
								passwordInput.setAttribute("name",inputID);
                                passwordInput.type = 'password';
                                passwordInput.value = '';
								passwordInput.setAttribute("class", ClassName);
                                passwordInput.onblur = changeBack;
                                document.getElementById(inputID).parentNode.replaceChild(passwordInput, document.getElementById(inputID));
                                window.setTimeout(function(){passwordInput.focus();}, 0);
                        }
                	}

                	function changeBack(event){
                        var evt = (window.event) ? window.event : event;
                        var elem = (evt.srcElement) ? evt.srcElement : evt.target;
                        var inputID = $(elem).attr('id');
                        var inputValue = $(elem).attr('value');
						var ClassName = $(elem).attr('class');
						 if(inputValue == ''){
                                var textInput = document.createElement('input');
                                textInput.type = 'text';
                                textInput.setAttribute("id",inputID);
								textInput.setAttribute("name",inputID);
                                textInput.value = 'Wachtwoord';
								textInput.setAttribute("class", ClassName);
                                textInput.onfocus = change;
                                document.getElementById(inputID).parentNode.replaceChild(textInput, document.getElementById(inputID));
                        }
                	}
				</script>
              <div class="aanmelden"><a href="register.html"> Aanmelden</a></div>
            </div>
            <div class="menucon">              
			  <ul>
                <li><a href="<?php echo base_url();?>" <?php if($this->router->method == 'index' || $this->router->method == ''): ?>class="active"<?php endif; ?>>Home</a></li>
                <li><img alt="" src="<?php echo base_url();?>assets/images/seperator.jpg"></li>
                <li><a href="<?php echo base_url();?>features" <?php if($this->router->method == 'features'): ?>class="active"<?php endif; ?>>Features</a></li>
                <li><img alt="" src="<?php echo base_url();?>assets/images/seperator.jpg"></li>
                <li><a href="<?php echo base_url();?>register" <?php if($this->router->method == 'register'): ?>class="active"<?php endif; ?>>DEMO</a></li>
                <li><img alt="" src="<?php echo base_url();?>assets/images/seperator.jpg"></li>
                <li><a href="<?php echo base_url();?>faq" <?php if($this->router->method == 'faq'): ?>class="active"<?php endif; ?>>FAQ</a></li>
                <li><img alt="" src="<?php echo base_url();?>assets/images/seperator.jpg"></li>
                <li><a href="<?php echo base_url();?>overons" <?php if($this->router->method == 'overons'): ?>class="active"<?php endif; ?>>Over ons</a></li>
                <li><img alt="" src="<?php echo base_url();?>assets/images/seperator.jpg"></li>
                <li><a href="<?php echo base_url();?>contact" <?php if($this->router->method == 'contact'): ?>class="active"<?php endif; ?>>Contacteer</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>