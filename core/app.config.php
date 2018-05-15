<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * phpVMS is licenced under the following license:
 *   Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)
 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/3.0/
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.phpvms.net
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

/**
 * DO NOT MODIFY THESE SETTINGS HERE!!
 * They will get over-ridden in an update. These are just defaults
 * To change, copy-paste and change the line/option/setting into your
 *  local.config.php file
 *
 * Most of these are in your local.config.php already
 *
 * View the docs for details about these settings
 */

# Debug mode is off by default
Config::Set('DEBUG_MODE', false);
Config::Set('DEBUG_LEVEL', 1); // 1 logs query errors, 2 logs all queries
Config::Set('ERROR_LEVEL', E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);

# Page encoding options
Config::Set('PAGE_ENCODING', 'UTF-8');
Config::Set('DB_CHARSET_NAME', 'utf8');

# Maintenance mode - this disables the site to non-admins
Config::Set('MAINTENANCE_MODE', false);
Config::Set('MAINTENANCE_MESSAGE', 'We are currently down for maintenance, please check back soon.');

# See more details about these in the docs
Config::Set('PAGE_EXT', '.htm');	# .htm is fine. You can still run PHP
Config::Set('PILOTID_OFFSET', 0);	# What # to start pilot ID's from
Config::Set('PILOTID_LENGTH', 4);	# Length of the Pilot ID
Config::Set('UNITS', 'nm');			# Your units: nm, mi or km
Config::Set('LOAD_FACTOR', '82');	# %age load factor
Config::Set('CARGO_UNITS', 'lbs');
Config::Set('DEFAULT_MAX_CARGO_LOAD', 10000);
Config::Set('DEFAULT_MAX_PAX_LOAD', 250);

# Number of routes to show in the route map
Config::Set('ROUTE_MAP_SHOW_NUMBER', 25);

# After how long to mark a pilot inactive, in days
Config::Set('PILOT_AUTO_RETIRE', true);
Config::Set('PILOT_INACTIVE_TIME', 90);

# Automatically confirm pilots?
Config::Set('PILOT_AUTO_CONFIRM', false);

# Automatically calculate ranks?
Config::Set('RANKS_AUTOCALCULATE', true);

# For how many hours a pilot can edit their submitted PIREP (custom fields only)
Config::Set('PIREP_CUSTOM_FIELD_EDIT', '48');

# The time to wait to be allowed to submit identical PIREPS
Config::Set('PIREP_CHECK_DUPLICATE', true);
Config::Set('PIREP_TIME_CHECK', '1'); #  Minutes, to wait in between duplicate submits

# Whether to ignore any user-inputted load, and always calculate it
Config::Set('PIREP_OVERRIDE_LOAD', false);

/* What to order schedules by. Use s.[column_name] [ASC/DESC],
	with the column name being from the schedules table */
Config::Set('SCHEDULES_ORDER_BY', 's.flightnum ASC');

/* For PIREPS_ORDER_BY use p.[column_name] [ASC/DESC] */
Config::Set('PIREPS_ORDER_BY', 'p.submitdate DESC');

# If someone places a bid, whether to disable that or not
Config::Set('DISABLE_SCHED_ON_BID', true);
Config::Set('DISABLE_BIDS_ON_BID', false);

# Whether to close any bids after a certain amount of time
Config::Set('CLOSE_BIDS_AFTER_EXPIRE', false);
Config::Set('BID_EXPIRE_TIME', '48'); # How many hours to hold bids for

# If you want to count transfer hours in rank calculations
Config::Set('TRANSFER_HOURS_IN_RANKS', false);

# Pilot pilots to only fly aircraft they're ranked to
Config::Set('RESTRICT_AIRCRAFT_RANKS', true);

# The StatsData::UserOnline() function - how many minutes to check
Config::Set('USERS_ONLINE_TIME', 20);

# Google Map Options
Config::Set('MAP_WIDTH', '800px');
Config::Set('MAP_HEIGHT', '600px');
Config::Set('MAP_TYPE', 'OpenStreetMap.Mapnik');
Config::Set('MAP_LINE_COLOR', '#ff0000');
Config::Set('MAP_CENTER_LAT', '45.484400');
Config::Set('MAP_CENTER_LNG', '-62.334821');
Config::Set('MAP_ZOOM_LEVEL', 5);

# ACARS options
#  Minutes, flights to show on the ACARS
#  Default is 720 minutes (12 hours)
Config::Set('ACARS_LIVE_TIME', 720);
Config::Set('ACARS_DEBUG', false);

/*
  This is the unit of money. For non-dollars, use :
	Dollars ($), enter "$"
	Euro (�), enter "&#8364;"
	Yen (�), enter "&yen;"
	Pounds (�), enter "&pound;"

  For example, to set EUROS:
	Config::Set('MONEY_UNIT', '&#8364;');
 */

Config::Set('MONEY_UNIT', '$');

/*
 To change the money format, look at:
  http://us3.php.net/money_format

 However, I do not recommend changing this
 */

Config::Set('MONEY_FORMAT', '%(#10n');


# Fuel info
/* Default fuel price, for airports that don't have
	And the surcharge percentage. View the docs
	for more details about these
*/
Config::Set('FUEL_GET_LIVE_PRICE', true);
Config::Set('FUEL_DEFAULT_PRICE', '5.10');
Config::Set('FUEL_SURCHARGE', '5');

# Units settings
#	These are global, also used for FSPAX
Config::Set('WeightUnit', '1');		# 0=Kg 1=lbs
Config::Set('DistanceUnit', '2');   # 0=KM 1= Miles 2=NMiles
Config::Set('SpeedUnit', '1');		# 0=Km/H 1=Kts
Config::Set('AltUnit', '1');		# 0=Meter 1=Feet
Config::Set('LiquidUnit', '3');		# 0=liter 1=gal 2=kg 3=lbs
Config::Set('WelcomeMessage', 'phpVMS/FSPAX ACARS'); # Welcome Message
Config::Set('LIQUID_UNIT_NAMES', array('liter','gal','kg', 'lbs'));

/* FSFK Settings
	Your FTP Server, and path to the lib/images folder (from where the FTP connects from), IE
	ftp://phpvms.net/phpvms/lib/fsfk or ftp://phpvms.net/public_html/phpvms/lib/fsfk

	You want the path from when you connect to the FTP down to where the /lib/fsfk folder is
*/
Config::Set('FSFK_FTP_SERVER', '');
Config::Set('FSFK_FTP_PORT', '21');
Config::Set('FSFK_FTP_USER', '');
Config::Set('FSFK_FTP_PASS', '');
Config::Set('FSFK_FTP_PASSIVE_MODE', 'TRUE');
Config::Set('FSFK_IMAGE_PATH', '/lib/fsfk'); // web path from SITE_ROOT

# Options for the signature that's generated
Config::Set('SIGNATURE_TEXT_COLOR', '#000');
Config::Set('SIGNATURE_USE_CUSTOM_FONT', true);
Config::Set('SIGNATURE_FONT_PATH', SITE_ROOT.'/lib/fonts/tahoma.ttf');
Config::Set('SIGNATURE_FONT_SIZE', '10');
Config::Set('SIGNATURE_X_OFFSET', '10');
Config::Set('SIGNATURE_Y_OFFSET', '17');
Config::Set('SIGNATURE_FONT_PADDING', 4);
Config::Set('SIGNATURE_SHOW_EARNINGS', true);
Config::Set('SIGNATURE_SHOW_RANK_IMAGE', true);
Config::Set('SIGNATURE_SHOW_COPYRIGHT', true);

# Avatar information
Config::Set('AVATAR_FILE_SIZE', 50000);	# Maximum file-size they can upload
Config::Set('AVATAR_MAX_WIDTH', 80);	# Resized width
Config::Set('AVATAR_MAX_HEIGHT', 80);	# Resized height

# Cookie information
Config::Set('SESSION_LOGIN_TIME', (60*60*24*30)); # Expire after 30 days, in seconds
Config::Set('SESSION_GUEST_EXPIRE', '30'); # Clear guest sessions 30 minutes
//Config::Set('SESSION_COOKIE_NAME', 'VMS_AUTH_COOKIE');

# Email Settings
Config::Set('EMAIL_FROM_NAME', '');
Config::Set('EMAIL_FROM_ADDRESS', '');

Config::Set('EMAIL_USE_SMTP', false);
# Add multiple SMTP servers by separating them with ;
Config::Set('EMAIL_SMTP_SERVERS', '');
Config::Set('EMAIL_SMTP_PORT', '25');
Config::Set('EMAIL_SMTP_USE_AUTH', false);
Config::Set('EMAIL_SMTP_USER', '');
Config::Set('EMAIL_SMTP_PASS', '');

# Set specific email addresses to send notifications to
Config::Set('EMAIL_NEW_REGISTRATIONS', '');
Config::Set('EMAIL_NEW_PIREP', '');

# This is email to specifically send email sent error to, such
# as failure notices with an invalid email or something
# If blank, it'll default to the "from" email that's specified
Config::Set('EMAIL_RETURN_PATH', '');


/* Can be 'geonames' or 'phpvms'.
	Geonames will use the geonames.org server to look up the airport info
	phpvms will use the phpVMS API server
*/

Config::Set('AIRPORT_LOOKUP_SERVER', 'geonames');
Config::Set('PHPVMS_API_SERVER', 'http://api.phpvms.net');
Config::Set('PHPVMS_NEWS_FEED', 'http://feeds.feedburner.com/phpvms');
Config::Set('VACENTRAL_NEWS_FEED', 'http://feeds.feedburner.com/vacentral');
Config::Set('GEONAME_API_SERVER', 'http://ws.geonames.org');

# Add to the end of local.config.php
# You need to get the keys from here: https://www.google.com/recaptcha/admin
# Put the "Site Key" in RECAPTCHA_PUBLIC_KEY
# Put the "Secret Key" in RECAPTCHA_PRIVATE_KEY
Config::Set('RECAPTCHA_PUBLIC_KEY', '');
Config::Set('RECAPTCHA_PRIVATE_KEY', '');

/*	Whether you have the /admin/maintenance.php script added into cron.
	If you do, set this to true. This saves many DB calls since phpVMS will
	have to 'fake' a cron-job
	*/
Config::Set('USE_CRON', false);

Config::Set('CHECK_RELEASE_VERSION', true);
Config::Set('CHECK_BETA_VERSION', false);
Config::Set('URL_REWRITE', false);

/* Days of the Week
	The compacted view, and the full text
 */
Config::Set('DAYS_COMPACT',  array('Su', 'M', 'T', 'W', 'Th', 'F', 'S', 'Su'));

Config::Set('DAYS_LONG',
	array('Sunday',
		  'Monday',
		  'Tuesday',
		  'Wednesday',
		  'Thursday',
		  'Friday',
		  'Saturday',
		  'Sunday'
	)
);

Config::Set('SITE_LANGUAGE', 'en');
Config::Set('ADMIN_SKIN', 'layout');


/**
 * *******************************************************
 *
 *
 *
 *
 *
 *
 * Advanced options, don't edit unless you
 * know what you're doing!!
 *
 * Actually, don't change them, at all. Please.
 * For your sake. And mine. :)
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */

$revision = file_get_contents(CORE_PATH.'/version');
define('PHPVMS_VERSION', '2.1.'.$revision);

Config::Set('TEMPLATE_USE_CACHE', false);
Config::Set('TEMPLATE_CACHE_EXPIRE', '24');
Config::Set('DBASE_USE_CACHE', false);
Config::Set('CACHE_PATH', SITE_ROOT . '/core/cache');
Config::Set('TPL_EXTENSION', '.tpl');
Config::Set('BASE_TEMPLATE_PATH', SITE_ROOT.'/core/templates');

if(defined('ADMIN_PANEL') && ADMIN_PANEL === true)
{
	Template::SetTemplatePath(SITE_ROOT.'/admin/templates');

	define('CODON_MODULES_PATH', SITE_ROOT.'/admin/modules');
	define('CODON_DEFAULT_MODULE', 'Dashboard');
}
else
{
	Template::SetTemplatePath(Config::Get('BASE_TEMPLATE_PATH'));

	define('CODON_MODULES_PATH', SITE_ROOT.'/core/modules');
	define('CODON_DEFAULT_MODULE', 'Frontpage');

	/*Config::Set('MODULES_PATH', SITE_ROOT.'/core/modules');
	Config::Set('DEFAULT_MODULE', 'Frontpage');
	Config::Set('MODULES_AUTOLOAD', true);*/
}

/* Cache settings */
$cache_settings = array(
	'active' => true,
	'engine' => 'file',					/* "file" or "apc" */
	'location' => CACHE_PATH,	/* For the "file" engine type */
	'prefix' => 'phpvms_',				/* Specify a prefix for any entries */
	'profiles' => array(
		'default' => array(
			'duration' => '+10 minutes',
		),

		'short' => array(
			'duration' => '+3 minutes',
		),

		'15minute' => array(
			'duration' => '+15 minutes',
		),

		'medium' => array(
			'duration' => '+1 hour',
		),

		'long' => array(
			'duration' => '+6 hours'
		),
	)
);

Config::Set('CACHE_KEY_LIST', array(
	'all_airline_active',
	'all_airlines',
	'start_date',
	'months_since_start',
	'years_since_start',
	'stats_aircraft_usage',
	'all_settings',
	'total_flights',
	'top_routes',
	'users_online',
	'guests_online',
	'pilot_count',
	'total_pax_carried',
	'flights_today',
	'fuel_burned',
	'miles_flown',
	'aircraft_in_fleet',
	'total_news_items',
	'total_schedules',
	'all_groups',
	'all_ranks',
	)
);


Config::Set('TABLE_LIST', array(
	'acarsdata',
	'adminlog',
	'aircraft',
	'airlines',
	'airports',
	'awards',
	'awardsgranted',
	'bids',
	'customfields',
	'downloads',
	'expenselog',
	'expenses',
	'fieldvalues',
	'financedata',
	'fuelprices',
	'groupmembers',
	'groups',
	'navdata',
	'news',
	'pages',
	'pilots',
	'pirepcomments',
	'pirepfields',
	'pireps',
	'pirepvalues',
	'ranks',
	'schedules',
	'sessions',
	'settings',
	'updates'
	)
);

/* VACentral */
Config::Set('VACENTRAL_ENABLED', false);
Config::Set('VACENTRAL_DEBUG_MODE', false);
Config::Set('VACENTRAL_DEBUG_DETAIL', 0);
Config::Set('VACENTRAL_API_SERVER', 'http://api.phpvms.net');
Config::Set('VACENTRAL_API_KEY', '');

/**
 * Constants
 *	Do not modify these! All sorts of weird shit can happen
 */
# Set the type of flights we have
Config::Set(
	'FLIGHT_TYPES', array(
		'P'=>'Passenger',
		'C'=>'Cargo',
		'H'=>'Charter'
	)
);

# Set the types of expenses we have
Config::Set(
	'EXPENSE_TYPES', array(
		'M'=>'Monthly',
		'F'=>'Per Flight',
		'P'=>'Percent (month)',
		'G'=>'Percent (per flight)'
	)
);

define('SIGNATURE_PATH', '/lib/signatures');
define('AVATAR_PATH', '/lib/avatars');

define('PIREP_PENDING', 0);
define('PIREP_ACCEPTED', 1);
define('PIREP_REJECTED', 2);
define('PIREP_INPROGRESS', 3);

define('PILOT_PENDING', 0);
define('PILOT_ACCEPTED', 1);
define('PILOT_REJECTED', 2);

define('NAV_NDB', 2);
define('NAV_VOR', 3);
define('NAV_DME', 4);
define('NAV_FIX', 5);
define('NAV_TRACK', 6);

define('LOAD_VARIATION', 5);
define('SECONDS_PER_DAY', 86400);

define('GEONAME_URL', 'http://ws.geonames.org');

define('VMS_AUTH_COOKIE', 'VMSAUTH');



/**
 * Library Includes (from 3rd Party)
 */

# Bit-masks for permission sets
$permission_set = array
	 (/*'NO_ADMIN_ACCESS'			=> 0,*/
	  'EDIT_NEWS'				=> 0x1,
	  'EDIT_PAGES'				=> 0x2,
	  'EDIT_DOWNLOADS'			=> 0x4,
	  'EMAIL_PILOTS'			=> 0x8,
	  'EDIT_AIRLINES'			=> 0x10,
	  'EDIT_FLEET'				=> 0x20,
	  'EDIT_SCHEDULES'			=> 0x80,
	  'IMPORT_SCHEDULES'		=> 0x100,
	  'MODERATE_REGISTRATIONS'	=> 0x200,
	  'EDIT_PILOTS'				=> 0x400,
	  'EDIT_GROUPS'				=> 0x800,
	  'EDIT_RANKS'				=> 0x1000,
	  'EDIT_AWARDS'				=> 0x2000,
	  'MODERATE_PIREPS'			=> 0x4000,
	  'VIEW_FINANCES'			=> 0x8000,
	  'EDIT_EXPENSES'			=> 0x10000,
	  'EDIT_SETTINGS'			=> 0x20000,
	  'EDIT_PIREPS_FIELDS'		=> 0x40000,
	  'EDIT_PROFILE_FIELDS'		=> 0x80000,
	  'EDIT_VACENTRAL'			=> 0x100000,
	  'ACCESS_ADMIN'			=> 0x2000000,
	  'FULL_ADMIN'				=> 35651519);

Config::Set('permission_set', $permission_set);
define('NO_ADMIN_ACCESS', 0);
foreach($permission_set as $key=>$value)
{
	define($key, $value);
}
