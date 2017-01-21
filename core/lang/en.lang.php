<?php
/**
 * English Language Strings for phpVMS
 * 
 * @author Nabeel Shahzad <http://www.phpvms.net>
 * 
 * You can use this file to create your own translations
 * Format is
 * 
 * key=>string
 * 
 * Key must be lower case, no underscores.  These are mainly for the
 * admin panel.
 */
 
$trans = array(
	
	/* Define some core language stuff */
	'invalid.php.version'				=> 'You are not running PHP 5.0+',
	'database.connection.failed'		=> 'Database connection failed',
	'error'								=> 'An error has occured (%s)', /* %s is the error string */
	
	/*
	 * Module language replacements
	 */
	
	/* Email stuff */
	'email.inuse'						=> 'This email address is already in use',
	'email.register.accepted.subject'	=> 'Your registration was accepted!',
	'email.register.rejected.subject'	=> 'Your registration was denied!',
	'email.pilot.retired.subject'		=> SITE_NAME.': You\'ve been marked as retired',
	
	/* Expenses */
	
	
	
	/* Registration Errors */
	'password.wrong.length'				=> 'Password is less than 5 characters',
	'password.no.match'					=> 'The passwords do not match',
	'password.changed'					=> 'Password has been successfully changed',
	
	/* Pilots Info */
	'pilots.title'						=> 'Pilots',
	'pilot.deleted'						=> 'Pilot had been deleted',
	
	/* ACARS */
	'acars.title'						=> 'ACARS',
	
	/* Awards */
	'award.exists'						=> 'The pilot already has this award!',
	'award.deleted'						=> 'Award deleted!',
	
	/* Contact */
	'contact.title'						=> 'Contact',
	
	/* Downloads */
	'downloads.title'					=> 'Downloads',
	
	/* Finances */
	'finances.title'					=> 'Finances',
	
	/* Front Bids */
	'frontbids.title'					=> 'Front Bids',
	
	/* Front Page */
	'frontpage.title'					=> 'Front Page',
	
	/* Groups */
	'group.added'						=> 'The group %s has been added', /* %s is group name */
	'group.saved'						=> 'The group %s has been saved', /* %s is group name */
	'group.no.name'						=> 'You must enter a name for the group',
	'group.pilot.already.in'			=> 'This user is already in this group!',
	'group.add.error'					=> 'There was an error adding this user',
	'group.user.added'					=> 'User has been added to the group!',
	
	/* Login */
	'login.title'						=> 'Login',
	
	/* Logout */
	'logout.title'						=> 'Logout',
	
	/* Pages */
	'pages.title'						=> 'Pages',
	'page.add.title'					=> 'Add Page',
	'page.edit.title'					=> 'Edit Page', 
	'page.exists'						=> 'This page already exists!',
	'page.create.error'					=> 'There was an error creating the file',
	'page.edit.error'					=> 'There was an error saving content',
	'page.error.delete'					=> 'There was an error deleting the page!',
	'page.deleted'						=> 'The page was deleted',
	
	/* Profile */
	'profile.title'						=> 'Profile',
	
	/* Registration */
	'registration.title'					=> 'Registration',
	
	/* Route Map */
	'routemap.title'					=> 'Route Map',
	
	/* Schedules */
	'schedules.title'					=> 'Schedules',
	
	/* News */
	'news.title'						=> 'News',
	'news.add.title'					=> 'Add News',
	'news.edit.title'					=> 'Edit News',
	'news.updated.success'				=> 'News edited successfully!',
	'news.updated.error'				=> 'There was an error editing the news item',
	'news.delete.error'					=> 'There was an error deleting the item',
	'news.item.deleted'					=> 'News item deleted',
	
	/* Settings */
	'settings.add.field'				=> 'Add Field',
	'settings.edit.field'				=> 'Edit Field',
	'pirep.field.add'					=> 'Add PIREP Field',
	'pirep.field.edit'					=> 'Edit PIREP Field',
	
	/* PIREPS */
	'pireps.title'					=> 'PIREPs',
	'pireps.view.recent'				=> 'Recent Reports',
	
	/* XML */
	'xml.title'					=> 'XML',
	
	/* kACARS Free */
	'kacars_free.title'				=> 'kACARS Free',
	
	/*
	 * Template language replacements
	 */
	 
	
);