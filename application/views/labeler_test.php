<?php echo "asasuyh";?>
<script src="http://www.onlinebestelsysteem.net/obs/assets/cp/js/jquery-1.6.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.dymo.sdk.min.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/cp/new_js/print_label.js"></script> 
<script type="text/javascript">

	function print_auto_labeler(type){
		var date = new Date().getTime();
		$.ajax({
			url:'<?php echo base_url()?>test_c/print_auto_labeler/'+type+'/'+date,
			dataType: 'json',
			success:function(response){
				
					if(response.error){
						//alert(response.message);
					}else{
						var labelers = response.data;
						for(var count=0; count < labelers.length;count++){
							var content = labelers[count].items;
							var print_type = labelers[count].type;
							
							if(print_type == 'per_ordered_product'){
								var template = "﻿<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\"><PaperOrientation>Landscape</PaperOrientation><Id>WhiteNameBadge11356</Id><PaperName>11356 White Name Badge - virtual</PaperName><DrawCommands><RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" /></DrawCommands><ObjectInfo><TextObject><Name>TEKST</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Left</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>"+content.name+" ("+content.default_price+" €)\n\n</String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+( (content.company != '')?content.company:'')+"\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.c_name+"\n\n</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String></String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.remark+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String>"+content.extra_field_text+"</String><Attributes><Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"391\" Y=\"136.181823730469\" Width=\"3828.33227539063\" Height=\"5040\" /></ObjectInfo><ObjectInfo><TextObject><Name>TEKST_1</Name><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /><BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" /><LinkedObjectName></LinkedObjectName><Rotation>Rotation0</Rotation><IsMirrored>False</IsMirrored><IsVariable>False</IsVariable><HorizontalAlignment>Right</HorizontalAlignment><VerticalAlignment>Top</VerticalAlignment><TextFitMode>ShrinkToFit</TextFitMode><UseFullFontHeight>True</UseFullFontHeight><Verticalized>False</Verticalized><StyledText><Element><String>€</String><Attributes><Font Family=\"Arial\" Size=\"8\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element><Element><String> "+content.amount+"</String><Attributes><Font Family=\"Arial\" Size=\"11\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" /><ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" /></Attributes></Element></StyledText></TextObject><Bounds X=\"3255.87255859375\" Y=\"80.4545364379883\" Width=\"1472.72729492188\" Height=\"1767.27270507813\" /></ObjectInfo></DieCutLabel>";
								create_label(template);
									
							}else{
								var template = "<"+"?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<DieCutLabel Version=\"8.0\" Units=\"twips\">\r\n\t<PaperOrientation>Landscape<\/PaperOrientation>\r\n\t<Id>WhiteNameBadge11356<\/Id>\r\n\t<PaperName>11356 White Name Badge - virtual<\/PaperName>\r\n\t<DrawCommands>\r\n\t\t<RoundRectangle X=\"0\" Y=\"0\" Width=\"2340\" Height=\"5040\" Rx=\"270\" Ry=\"270\" \/>\r\n\t<\/DrawCommands>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>ID "+content.order_id+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.name+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"8\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.address+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_1<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Right<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Top<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>ShrinkToFit<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>€ "+content.amount+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"True\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>\r\n\r\n\r\n\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"12\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.date+"<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"10\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"3234.05444335938\" Y=\"287.727264404297\" Width=\"1494.54541015625\" Height=\"1560\" \/>\r\n\t<\/ObjectInfo>\r\n\t<ObjectInfo>\r\n\t\t<TextObject>\r\n\t\t\t<Name>TEKST_3<\/Name>\r\n\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t<BackColor Alpha=\"0\" Red=\"255\" Green=\"255\" Blue=\"255\" \/>\r\n\t\t\t<LinkedObjectName><\/LinkedObjectName>\r\n\t\t\t<Rotation>Rotation0<\/Rotation>\r\n\t\t\t<IsMirrored>False<\/IsMirrored>\r\n\t\t\t<IsVariable>False<\/IsVariable>\r\n\t\t\t<HorizontalAlignment>Left<\/HorizontalAlignment>\r\n\t\t\t<VerticalAlignment>Bottom<\/VerticalAlignment>\r\n\t\t\t<TextFitMode>None<\/TextFitMode>\r\n\t\t\t<UseFullFontHeight>True<\/UseFullFontHeight>\r\n\t\t\t<Verticalized>False<\/Verticalized>\r\n\t\t\t<StyledText>\r\n\t\t\t\t<Element>\r\n\t\t\t\t\t<String>"+content.remark+"\r\n<\/String>\r\n\t\t\t\t\t<Attributes>\r\n\t\t\t\t\t\t<Font Family=\"Arial\" Size=\"7\" Bold=\"False\" Italic=\"False\" Underline=\"False\" Strikeout=\"False\" \/>\r\n\t\t\t\t\t\t<ForeColor Alpha=\"255\" Red=\"0\" Green=\"0\" Blue=\"0\" \/>\r\n\t\t\t\t\t<\/Attributes>\r\n\t\t\t\t<\/Element>\r\n\t\t\t<\/StyledText>\r\n\t\t<\/TextObject>\r\n\t\t<Bounds X=\"391\" Y=\"278\" Width=\"2910.150390625\" Height=\"1600\" \/>\r\n\t<\/ObjectInfo>\r\n<\/DieCutLabel>";
								create_label(template);
							}
						}
					}
				}
		});
	}
	
	print_auto_labeler("all");
	
</script>