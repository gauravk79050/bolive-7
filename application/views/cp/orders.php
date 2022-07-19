<!-- <meta http-equiv="refresh" content="5"> -->
<script type="text/javascript">

	var labeler_activated = false;
	<?php if($settings['0']->activate_labeler){?>
	labeler_activated = true;
	<?php } ?>
</script>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.dymo.sdk.min.js?version=<?php echo version;?>" type="text/javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>assets/cp/new_js/print_label.js" type="text/javascript"></script>
<script>
	var messages1="<?php echo _('Please select a record')?>"
	var messages2="<?php echo _('Do u really want to delete it')?>";
	var messages3="<?php echo _('Selected orders has been deleted successfully')?>";
	var messages4="<?php echo _('Some error occured')?>";
	var messages5="<?php echo $company_role;?>";
	var messages6="<?php echo _('Please select atleast one record')?>";
	var messages7="<?php echo _('Do you want to print all records')?>";
	var messages8="<?php echo $orders;?>";
	var messages9="<?php echo _('Date')?>";
	var messages10="<?php echo _('Name')?>";
	var messages11="<?php echo _("Total")?>";
	var messages12="<?php echo _("Take Away")?>";
	var messages13="<?php echo _("Delivery")?>";
	var messages14="<?php echo _("Send")?>";
	var messages15="<?php echo _("Order Status")?>";
	var messages16="<?php echo _("Shop")?>";
	var messages17="<?php echo _("Action")?>";
	var messages18="<?php echo _('New Client Ordered'); ?>";
	var messages19="<?php echo _("Download Label");?>";
	var messages20="<?php echo _("Download Label");?>";
	var messages21="<?php echo _("Delete")?>";
	var messages22="<?php echo _("Print All")?>";
	var messages23="<?php echo _('Please enter start date')?>";
	var messages24="<?php echo _('Please enter end date')?>";
	var messages25="<?php echo _('Please enter start date')?>";
	var messages26="<?php echo _('Please enter end date')?>";
	var messages27="<?php echo _('no products available')?>";
	var messages28="<?php echo _('did not get any category.please select a category')?>";
	var messages29="<?php echo _('Are you sure you want to delete?')?>";
	var messages30="<?php echo _('Please use only numbers')?>";
	var messages31="<?php echo _("Image can not be downloaded. Please try again");?>";
</script>

	<script>
	<?php if($settings['0']->activate_labeler){?>
		function print_auto_labeler(type){
		var date = new Date().getTime();
		$.ajax({
			url:base_url+'cp/orders/print_auto_labeler/'+type+'/'+date,
			dataType: 'json',
			success:function(response){

					if(response.error){
						//alert(response.messages);
					}else{
						var labelers = response.data;
						for(var count=0; count < labelers.length;count++){
							var content = labelers[count].items;
							var print_type = labelers[count].type;

							if(print_type == 'per_ordered_product'){
								//var template = "﻿<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\"><PaperOrientation>Landscape</PaperOrientation><Id>WhiteNameBadge11356</Id><PaperName>11356 White Name Badge - virtual</PaperName><DrawCommands><RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" /></DrawCommands><ObjectInfo><TextObject><Name>TEKST</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Left</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>"+content.name+" ("+content.default_price+" €)\n\n</String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+( (content.company != '')?content.company:'')+"\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.c_name+"\n\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.remark+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra_field_text+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"391\" Y=\"136.181823730469\" Width=\"3828.33227539063\" Height=\"5040\" /></ObjectInfo><ObjectInfo><TextObject><Name>TEKST_1</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Right</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>€</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String> "+content.amount+"</String><Attributes><Font Family=\"Arial\" Size=\"11\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"3255.87255859375\" Y=\"80.4545364379883\" Width=\"1472.72729492188\" Height=\"1767.27270507813\" /></ObjectInfo></DieCutLabel>";
								//var template = "﻿<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\"><PaperOrientation>Landscape</PaperOrientation><Id>WhiteNameBadge11356</Id><PaperName>11356 White Name Badge - virtual</PaperName><DrawCommands><RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" /></DrawCommands><ObjectInfo><TextObject><Name>TEKST</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Left</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>"+content.name+" ("+content.default_price+" €)\n\n</String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+( (content.company != '')?content.company:'')+"\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.c_name+"\n\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra_field_text+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.remark+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"391\" Y=\"100\" Width=\"3828.33227539063\" Height=\"5040\" /></ObjectInfo><ObjectInfo><TextObject><Name>TEKST_1</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Right</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>€</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String> "+content.amount+"</String><Attributes><Font Family=\"Arial\" Size=\"11\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"3255.87255859375\" Y=\"80.4545364379883\" Width=\"1472.72729492188\" Height=\"1767.27270507813\" /></ObjectInfo></DieCutLabel>";
								var template = "﻿<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\"><PaperOrientation>Landscape</PaperOrientation><Id>WhiteNameBadge11356</Id><PaperName>11356 White Name Badge - virtual</PaperName><DrawCommands><RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" /></DrawCommands><ObjectInfo><TextObject><Name>TEKST</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Left</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>"+content.name+" ("+content.default_price+" €)\n\n</String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+( (content.company != '')?content.company:'')+"\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.c_name+"\n\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra_field_text+"\n</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.remark+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"391\" Y=\"100\" Width=\"3828.33227539063\" Height=\"5040\" /></ObjectInfo><ObjectInfo><TextObject><Name>TEKST_1</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Right</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>€</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String> "+content.amount+"</String><Attributes><Font Family=\"Arial\" Size=\"11\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"3255.87255859375\" Y=\"80.4545364379883\" Width=\"1472.72729492188\" Height=\"1767.27270507813\" /></ObjectInfo></DieCutLabel>";
								create_label(template);

							}else{
								//var template = "<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\">\r\n\t<PaperOrientation>Landscape<\/PaperOrientation>\r\n\t<Id>WhiteNameBadge11356<\/Id>\r\n\t<PaperName>11356 White Name Badge - virtual<\/PaperName>\r\n\t<DrawCommands>\r\n\t\t<RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" \/>\r\n\t<\/DrawCommands>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>ID "+content.order_id+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.name+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.address+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_1<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Right<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>ShrinkToFit<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>€ "+content.amount+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n\r\n\r\n\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"12\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.date+"<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"3234.05444335938\" Y=\"287.727264404297\" Width=\"1494.54541015625\" Height=\"1560\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_3<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Bottom<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.remark+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n<\/DieCutLabel>";
								//var template = "<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\">\r\n\t<PaperOrientation>Landscape<\/PaperOrientation>\r\n\t<Id>WhiteNameBadge11356<\/Id>\r\n\t<PaperName>11356 White Name Badge - virtual<\/PaperName>\r\n\t<DrawCommands>\r\n\t\t<RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" \/>\r\n\t<\/DrawCommands>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>ID "+content.order_id+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String> "+( (content.company_c != '')?content.company_c:'')+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"12\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.name+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.address+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"100\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_1<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Right<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>ShrinkToFit<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>€ "+content.amount+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n\r\n\r\n\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"12\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.date+"<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"3234.05444335938\" Y=\"287.727264404297\" Width=\"1494.54541015625\" Height=\"1560\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_3<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Bottom<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.remark+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n<\/DieCutLabel>";
								var template = "<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\">\r\n\t<PaperOrientation>Landscape<\/PaperOrientation>\r\n\t<Id>WhiteNameBadge11356<\/Id>\r\n\t<PaperName>11356 White Name Badge - virtual<\/PaperName>\r\n\t<DrawCommands>\r\n\t\t<RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" \/>\r\n\t<\/DrawCommands>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>ID "+content.order_id+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String> "+( (content.company_c != '')?content.company_c:'')+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t</Element><Element>\r\n\t\t\t\t\t<String>\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.name+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.address+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"100\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_1<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Right<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>ShrinkToFit<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>€ "+content.amount+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n\r\n\r\n\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"12\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.date+"<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"3234.05444335938\" Y=\"287.727264404297\" Width=\"1494.54541015625\" Height=\"1560\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_3<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Bottom<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.remark+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n<\/DieCutLabel>";
								create_label(template);
							}
						}
					}
				}
		});
	}


	<?php } ?>
	var labeler_activated = false;
	<?php if($settings['0']->activate_labeler){?>
	labeler_activated = true;
	<?php } ?>
	<?php if($this->session->userdata('login_via') == 'cp' && $settings['0']->activate_labeler && $settings['0']->labeler_print_type == 'automatic'){ ?>
	print_auto_labeler('<?php echo $settings['0']->labeler_type;?>');
	<?php }?>
	var company_role = "<?php echo $company_role;?>";

	</script>

<!-------function will print labeler for orders -------->

<!-- -----this section is for jquery pagination ------------------------------------- -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_js/pagination/pagination.css"/>
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/orders_new.css"/>

<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/pagination/jquery.pagination.js?version=<?php echo version;?>"></script>
<script>
<?php if($this->company->login_first_time == 1 ):?>
		tb_show('','#TB_inline?height=290&width=400&inlineId=mail_accept_que&modal=true','');
	<?php endif;?>

	var dataLength = <?php echo $orders;?>;

	</script>



<div id="mail_accept_que" style="display: none;">
<p><?php echo _("Agree future mails from Bestelonline ??");?></p>
<a href="javascript:;" id="accept"><?php echo _("Yes");?></a>   ||  <a href="javascript:;" id="no_accept"><?php echo _("No");?></a>
</div>

<div id="main">
	<div id="main-header">
 		<h2><?php echo _('Orders'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Orders'); ?></span>	<?php $messages = $this->messages->get();?>
		<?php if($messages != array()): foreach($messages as $type => $messages):?>

			<?php if($type == 'success' && $messages != array()):?>
				<div id="succeed"><?php echo $messages[0];?></div>
			<?php elseif($type == 'error' && $messages != array()):?>
				<div id="error"><strong><?php echo _('Error')?></strong>:<?php echo $messages[0];?></div>
			<?php endif;?>
		<?php endforeach; endif;?>
	</div>

	<div id="content">
    	<div id="content-container">

			<div class="orders_tab">
			<!-- -----------------------Code for showing notifications -->
		      	<?php $a_type = $this->company->ac_type_id;?>
		      	<?php if(isset($notifications)) {
		      	foreach ($notifications as $noti ){
		      		$ac_type_arr = json_decode($noti['company_type']);
		      		$show_flag = FALSE;
		      		foreach ($ac_type_arr as $ac_type){
						if($ac_type == $a_type){
							$show_flag = TRUE;
							BREAK;
						}
					}
				foreach($closed_noti as $c_noti){
						if($c_noti->notification_id == $noti['id']){
							$show_flag = FALSE;
							BREAK;
						}
					}
		      		if($show_flag){ ?>

					<div style="background:#EBF7C5; padding:10px 8px;width:96%; margin-bottom:20px; text-align:left; border:1px solid #ddd; margin-right: 245px; margin-left:0px;">
						<a id="noti_<?php echo $noti['id'];?>" href="javascript:;" data-title="close" onclick="close_this_noti(this.id)" style="float:right"><img alt="close" width="15" src="<?php echo base_url('')."assets/cp/images/Delete.gif" ?>" ></a>
						<h4><?php echo $noti['subject'];?></h4>
						<?php echo $noti['notification']; ?>
					</div>
				<?php }
		      	 	}
		      	 }?>
		      	<!-- ------------------------------------------------------- -->




				<ul>
					<li class="select">
						<a href="<?php echo base_url();?>cp/orders"><?php echo _("Order via website");?> (<?php echo $orders;?>)</a>
					</li>
					<?php //if($this->company->obsdesk_status) { ?>
					<li>
						<a href="<?php echo base_url();?>cp/desk/orders"><?php echo _("Order via OBSdesk");?> (<?php echo count($desk_orders);?> )</a>
					</li>
					<?php //}?>
					<?php if(isset($pending_orders)){?>
					<li>
						<a href="<?php echo base_url();?>cp/orders/lijst/pending"><?php echo _("Pending Orders");?> (<?php echo $pending_orders;?> )</a>
					</li>
					<?php }?>
					<?php if(isset($cancelled_orders)){?>
					<li>
						<a href="<?php echo base_url();?>cp/orders/lijst/cancelled"><?php echo _("Cancelled Orders");?> (<?php echo $cancelled_orders;?> )</a>
					</li>
					<?php }?>
				</ul>
			</div>
			<?php if($this->session->userdata('login_via') == 'mcp'){?>
			<input type="button" style="float: right;width: 100px; height: 30px;" class="submit" id="btn_auto_load" value="<?php echo _('Stop Auto Load')?>"  name="btn_autoload"/>
			<?php }?>
			<div class="clear"></div>


        	<div class="box">
          		<h3>&nbsp;</h3>
				<div class="table">
					<input type="hidden" id="filtered_start_date" name="filtered_start_date" value="<?php if(isset($start_date)){ echo $start_date;}?>" />
					<input type="hidden" id="filtered_end_date" name="filtered_end_date" value="<?php if(isset($end_date)){ echo $end_date;}?>" />

            		<form name="frm_search" id="frm_search" action="<?php echo base_url()?>cp/orders" method="post">
            			<table cellspacing="0" border="0" width="90%" cellpadding="0">
	            			<tbody>
	                			<tr>
	                  				<td colspan="2" width="22%"><strong><?php echo _('Display all orders between')?></strong></td>
	                  				<td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="start_date" id="start_date"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'dd/mm/yyyy',this)" name="button1" id="button1"></div>
									</td>
	                   				<td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="end_date" id="end_date"/></div>
										<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'dd/mm/yyyy',this)" name="button2" id="button2"/></div>
	                  				</td>
	                 				 <td valign="middle" width="20%"><input type="submit" class="submit" value="<?php echo _('Search')?>" name="btn_search" id="btn_search"/>
		                    			<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
		                    			<input type="hidden" value="do_filter" name="act" id="act"/>
		                    			<input type="hidden" value="orders" name="view" id="view"/>
									</td>
	                  				<td width="0%">&nbsp;</td>
	                			</tr>
	              			</tbody>
	              		</table>
            		</form>
            		<script language="JavaScript" type="text/javascript">
						var frmvalidator = new Validator("frm_search");
						frmvalidator.EnableMsgsTogether();
						frmvalidator.addValidation("start_date","req","<?php echo _('Please enter start date')?>");
						frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
					</script>
          		</div><!------END OF TABLE DIV------>
        	</div><!-- ------END OF BOX DIV-------- -->

			<div class="box">
				<h3><?php echo _("Orders Information")?></h3>
				<div class="table">
					<form name="frm_delete_all" id="frm_delete_all">
						<table cellspacing="0" border="0" id="order_content">
						<!-- jquery pagination will add content in this table-->
							<tbody>
							</tbody>
						</table>

					<!------------------this div will show the order details in a thickbox--------------->
								<div id="show_order_details" style="display:none"></div>
					<!---------------------------------------------------------------------------------->
					<input type="hidden" value="multiple_data" name="act" id="act"/>
					</form>
					<div id="Pagination">
					</div>
					<div style="background: none repeat scroll 0 0 #FAFAFA;border-top: 1px solid #E3E3E3;padding: 5px 10px;">
					  <a href="<?php echo base_url()?>cp/orders/ordered_products"><?php echo _('Print Report'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
					  <a href="<?php echo base_url()?>cp/seperate_orders_report"><?php echo _('Seperate Orders Report'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
					  <a href="<?php echo base_url()?>cp/client_orders_report"><?php echo _('Client Orders Report'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
					  <a href="<?php echo base_url()?>cp/orders/ordered_products_per_supp"><?php echo _('Orders Report Per Supplier'); ?></a>
					  <br />
					  <br />
					  <br />
					  <br />

                  	</div>
			    	<div style= "clear:both">
				  	</div>
				  	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="textkleiner">
			  		  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/image-x-photo-cd.png" width="15" height="15" /></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _('To download image for ordered products'); ?></div></td>
			          </tr>
				  	  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/per_order.png" width="15" height="15" /></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _('Means print labeler per order'); ?></div></td>
			          </tr>
				  	  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/per_product.png" width="15" height="15" /></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _('Means print labeler per product'); ?></div></td>
			          </tr>

				      <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/red_dot.gif" width="8" height="8" /></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _('means a new client'); ?></div></td>
			          </tr>
				      <tr>
					    <td>&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/via-paypal.PNG" width="16" height="16" /></td>
				        <td><?php echo _('means paid by Paypal'); ?></td>
			          </tr>
				      <tr>
					    <td>&nbsp;</td>
				        <td width="1%"><img src="<?php echo base_url();?>assets/cp/images/checked_invoice_64.gif" width="16" height="16" /></td>
				        <td><?php echo _('means that this client has marked - I want an invoice'); ?></td>
			          </tr>
			        </table>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript"> var a="fdf";</script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/order_all.js?version=<?php echo version;?>"></script>