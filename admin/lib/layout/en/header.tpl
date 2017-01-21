<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>phpVMS Admin Panel</title>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo Config::Get('PAGE_ENCODING');?>">

<link rel="alternate" href="<?php echo SITE_URL?>/lib/rss/latestpireps.rss" title="latest pilot reports" type="application/rss+xml" />
<link rel="alternate" href="<?php echo SITE_URL?>/lib/rss/latestpilots.rss" title="latest pilot registrations" type="application/rss+xml" />

<?php
Template::Show('core_htmlhead.tpl');
?>

<link href="<?php echo SITE_URL?>/admin/lib/css/flick/jquery-ui-1.8.custom.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SITE_URL?>/admin/lib/layout/styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<style type="text/css"> 
#sidebar1 { padding-top: 30px; }
#mainContent { zoom: 1; padding-top: 15px; }
</style>
<![endif]-->
</head>

<body>
<?php
Template::Show('core_htmlreq.tpl');
?>
<div id="container">
 	<div id="header">
    	<img src="<?php echo SITE_URL?>/admin/lib/layout/images/admin_logo.png" />
	</div>
	
  <div id="sidebar">
	<?php
	Template::Show('core_sidebar.tpl');
	?>
	
	<h3 >Options</h3>
	<ul class="menu" style="padding: 0px">
		<li><a href="<?php echo SITE_URL?>/admin/index.php/dashboard" class="menu">
				<img src="<?php echo SITE_URL?>/admin/lib/layout/images/dashboard_icon.png" />Dashboard
			</a>
		</li>
	</ul>
	<ul id="slidermenu" class="menu">
	<?php
	Template::Show('core_navigation.tpl');
	?>
	</ul>
	<ul class="menu">
		<li><a style="border-top: none" href="<?php echo SITE_URL?>/index.php">View Your Site</a></li>
		<li><a href="<?php echo SITE_URL?>/index.php/Login/logout">Log Out</a></li>
	</ul>
		
  </div>
  <div id="mainContent">
	<div id="results"></div>
	<div id="bodytext">