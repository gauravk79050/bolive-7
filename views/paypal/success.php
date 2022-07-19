<h2><?php echo _('Payment succesfully completed')?></h2>

<p><?php echo $payment_message; ?></p>

<?php if($redirect): ?>
<script type="text/javascript">setTimeout(function(){ window.location = '<?php echo $redirect; ?>' },3000);</script>
<?php endif; ?>
