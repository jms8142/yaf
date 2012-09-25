<?php 


/*
 * Run a quick check on the state of installation
 */

/*
 * look for config files
 */
if(!runInstallCheck()){ 
	getHTMLHeader();
?>
	<body>
<!-- ui-dialog -->
		<div id="dialog0" title="Welcome to Yet Another Framework" class="dialog">
			<p>It seems like this is your first time running the software.  Welcome, and if you would like to install <strong>YAF</strong>, click <strong>OK</strong> to continue.</p>
		</div>

		<div id="dialog1" title="A couple of questions about your site" class="dialog"	>
			<form>
				<label>Name</label><input type="text" name="name"/>
				<label>Company</label><input type="text" name="company"/>
				<label>Email</label><input type="text" name="name"/>

			</form>
		</div>

			
<div class="container">
  <div class="content">
   <!-- end .content --></div>
  <!-- end .container --></div>
</body>
<?php 
	getHTMLFooter();
exit();
	
} else {
	//check configuration
	if(!runConfigCheck()){
		getHTMLHeader();
		?>
		<body>
<!-- ui-dialog -->
		<div id="dialog" title="Welcome to Yet Another Framework">
			<p>There seems to be a problem accessing the software configuration files.  You may need to reinstall.</p>
		</div>
			
<div class="container">
  <div class="content">
   <!-- end .content --></div>
  <!-- end .container --></div>
</body>
		
		<?php 
		getHTMLFooter();
		exit();
	}
	
	
}

 
 
 
function runInstallCheck(){
	return false;
}

function runConfigCheck(){
	//look for util files
	if(!file_exists('com/classes/core/util/xmlToArray.php') || !file_exists('inc/config.xml') || !file_exists('inc/pages.xml'))
		return false;
	
	
	
	
	return true;
}

function getHTMLHeader(){
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Welcome to YAF (Yet Another Framework)</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript" src="/lib/js/jquery-ui/js/jquery-ui-1.8.20.custom.min.js"></script>
<link  rel="stylesheet" type="text/css" href="/lib/js/jquery-ui/css/pepper-grinder/jquery-ui-1.8.20.custom.css" /> 
<style type="text/css">
<!--
body {
	font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
	background: #42413C;
	margin: 0;
	padding: 0;
	color: #000;
}

.dialog {
	display:none;
}

-->
</style>
<script type="text/javascript">

			var step = 0;
			var dialogOptions = [
				{ "id" : "#dialog0"},
				{ "id" : "#dialog1"}
			]


			$(function(){


				refreshDialog();	


			});

			function refreshDialog(){
				console.info(step);
				console.info();
				
				$(dialogOptions[step].id).dialog({
					autoOpen: false,
					width: 600,
					buttons: {
						"Ok": function() { 
							step += 1;
							refreshDialog();
						}
					},
					resizable : false,
					closeOnEscape: false,
					open: function(event, ui) { $(".ui-dialog-titlebar-close").hide() }
				});
				//console.info(dialogOptions[step].id);
				$(dialogOptions[step].id).dialog('open');
				
			}

</script>
</head><?php
}

function getHTMLFooter(){
	?></html><?php 
}