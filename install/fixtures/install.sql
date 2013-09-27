

INSERT INTO `phpvms_news` (`subject`,`body`,`postdate`,`postedby`)
VALUES ('Welcome to phpVMS!',  'Thanks for installing and using phpVMS!<br /> Check out the <a href="http://docs.phpvms.net/" target="_new">docs</a> for help and information on getting started on your VA.<br /><br />This is just a starter skin - customize yours completely. This is just a basic, barebones example of what a skin is and how it works. Check out the crystal folder in the lib/skins directory. Make your own copy and fiddle around. For help, check out the forum, and skinning docs. Also, be sure to check out the <a href="http://www.phpvms.net/tutorials" target="_blank">skinning tutorials</a> for a quick primer. The <a href="http://forum.phpvms.net" target="_blank">forums</a> are also filled with plenty of helpful people for any questions you may have.<br /><br />Good luck!', NOW(), 'phpVMS Installer');

INSERT INTO `phpvms_airports` (`id`, `icao`, `name`, `country`, `lat`, `lng`, `hub`, `fuelprice`, `chartlink`) VALUES
(1, 'KJFK', 'Kennedy International', 'USA', 40.6398, -73.7787, 1, 0, '');

INSERT INTO `phpvms_ranks` VALUES(1, 'New Hire', '', 0, 18.0); 

INSERT INTO `phpvms_groups` (`name`, `permissions`, `core`) VALUES ('Administrators', '536870911', 1);
INSERT INTO `phpvms_groups` (`name`, `permissions`, `core`) VALUES ('Active Pilots', '0', 1);
INSERT INTO `phpvms_groups` (`name`, `permissions`, `core`) VALUES ('Inactive Pilots', '0', 1);

INSERT INTO `phpvms_settings` VALUES(NULL , 'phpVMS Version', 'PHPVMS_VERSION', '0', 'phpVMS Version', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Virtual Airline Name', 'SITE_NAME', 'PHPVMS', 'The name of your site. This will show up in the browser title bar.', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Webmaster Email Address', 'ADMIN_EMAIL', '', 'This is the email address that email will get sent to/from', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Date Format', 'DATE_FORMAT', 'm/d/Y', 'This is the date format to be used around the site.', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Current Skin', 'CURRENT_SKIN', 'crystal', 'Available skins', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Default User Group', 'DEFAULT_GROUP', 'Active Pilots', 'This is the default group if they are not explicitly denied', 1);
INSERT INTO `phpvms_settings` VALUES(NULL , 'Total VA Hours', 'TOTAL_HOURS', '0', 'Your total hours', 1);