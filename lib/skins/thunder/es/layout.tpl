<?php
/**
 * Template for Thunder Airways
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" media="all" type="text/css" href="<?php echo SITE_URL?>/lib/skins/thunder/styles.css" />

<?php 
/* This is required, so phpVMS can output the necessary libraries it needs */
echo $page_htmlhead; 
?>

<?php /*Any custom Javascript should be placed below this line, after the above call */ ?>

<script type="text/javascript" src="<?php echo fileurl('lib/skins/thunder/js/jssor.slider.min.js');?>"></script>

</head>
<body>
<?php
/* This should be the first thing you place after a <body> tag
	This is also required by phpVMS */
echo $page_htmlreq;
?>
<div id="body">
<div id="innerwrapper">
	<div id="topBanner">
		<div id="topLogin">
		<?php 
		/* 
		Quick example of how to see if they're logged in or not
		Only show this login form if they're logged in */
		if(Auth::LoggedIn() == false)
		{ ?>
			<form name="loginform" action="<?php echo url('/login'); ?>" method="post">
				Ingrese con su ID de piloto, email, o <a href="<?php echo url('/registration'); ?>">regístrese</a><br />
			<input type="text" name="email" value="" onClick="this.value=''" />
			<input type="password" name="password" value="" />
			<input type="hidden" name="remember" value="on" />
			<input type="hidden" name="redir" value="index.php/profile" />
			<input type="hidden" name="action" value="login" />
			<input type="submit" name="submit" value="Log In" />
			</form>
			<?php
		}	
		/* End the Auth::LoggedIn() if */
		else /* else - they're logged in, so show some info about the pilot, and a few links */
		{
		
		/*	Auth::$userinfo has the information about the user currently logged in
			We will use this next line - this gets their full pilot id, formatted properly */
		$pilotid = PilotData::GetPilotCode(Auth::$userinfo->code, Auth::$userinfo->pilotid);
		?>
		
		<img align="left" height="50px" width="50px" style="margin-right: 10px;"
			src="<?php echo PilotData::getPilotAvatar($pilotid);?>" />

		<strong>ID de Piloto: </strong> <?php echo $pilotid ; ?>
		<strong>Rango: </strong><?php echo Auth::$userinfo->rank;?><br />
		<strong>Total de Vuelos: </strong><?php echo Auth::$userinfo->totalflights?>, <strong>Total de Horas: </strong><?php echo Auth::$userinfo->totalhours;?>
		<br />
		<a href="<?php echo url('/pireps/new');?>">Enviar un nuevo PIREP</a> | 
		<a href="<?php echo url('/profile/');?>">Ver Sala de Pilotos</a>
		<?php
		} /* End the else */
		?>
		</div>
	</div>
	<div id="topNav">
		<ul class="nav">
			<?php
			/*	You can modify this template into a table or something, by default
				it's list elements inside of a UL. Here's a link with some info:
				
				http://articles.sitepoint.com/article/css-anthology-tips-tricks-4/2
			 */
			Template::Show('core_navigation.tpl');
			?>
		</ul>
	</div>
	<div id="bodytext">
	
	<?php
	/*	This will insert all of the "meat" of the page in there - the template
		which is generated, depending on which page you're on. To change these
		templates, check out the docs on the site. They're under the /core/templates
		folder, and to change them, copy them into the folder of your skin (the
		folder this file is in right now.
	 */
	 
	echo $page_content;
	
	?>
	
	</div>
	</div>
	<div id="footer">
	<p>copyright &copy; <?php echo date('Y') ?> - <?php echo SITE_NAME; ?><br />
	<!-- Please retain this!! It's part of the phpVMS license. You must display a
			"powered by phpVMS" somewhere on your page. Thanks! -->
	<a href="http://www.phpvms.net" target="_blank">powered by phpVMS</a></p>
	</div>	
</div>
</body>
</html>