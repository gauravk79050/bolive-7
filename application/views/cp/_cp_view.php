<?php echo $this->load->view('cp/header'); ?>

<?php echo $this->load->view($content); ?>

<?php if(!$cont){?>
<?php echo $this->load->view('cp/sidebar')?>
<?php }?>
<?php echo $this->load->view('cp/footer'); ?>