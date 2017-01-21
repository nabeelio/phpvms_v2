--
-- phpVMS Installer;
-- 
-- DO NOT MANUALLY RUN THIS FILE!!!
-- To install, goto /install/install.php
--
-- Manually running this will screw up your install!! ;


CREATE TABLE `phpvms_adminlog` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`pilotid` INT NOT NULL ,
	`datestamp` DATETIME NOT NULL ,
	`message` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `phpvms_awards` (
	`awardid` INT NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR( 25 ) NOT NULL ,
	`descrip` VARCHAR( 100 ) NOT NULL ,
	`image` TEXT NOT NULL,
	PRIMARY KEY ( `awardid` )
) ENGINE = MYISAM;

CREATE TABLE `phpvms_awardsgranted` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`awardid` INT NOT NULL ,
	`pilotid` INT NOT NULL ,
	`dateissued` DATETIME NOT NULL ,
	PRIMARY KEY ( `id` )
) ENGINE = MYISAM;

CREATE TABLE IF NOT EXISTS `phpvms_fuelprices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icao` varchar(4) NOT NULL,
  `lowlead` float NOT NULL,
  `jeta` float NOT NULL,
  `dateupdated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `phpvms_updates` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 25 ) NOT NULL ,
	`lastupdate` DATETIME NOT NULL ,
	INDEX ( `name` )
) ENGINE = MYISAM;

CREATE TABLE `phpvms_downloads` (
   `id` INT NOT NULL AUTO_INCREMENT,
   `pid` INT,
   `name` VARCHAR(50),
   `description` TEXT ASCII,
   `link` TEXT ASCII,
   `image` TEXT ASCII,
   `hits` INT,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

CREATE TABLE `phpvms_expenses` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR( 25 ) NOT NULL ,
	`cost` FLOAT NOT NULL ,
	`fixed` INT NOT NULL DEFAULT 0,
	`type` VARCHAR( 1 ) NOT NULL default 'M',
	PRIMARY KEY (`id`)
) ENGINE = MYISAM;

CREATE TABLE `phpvms_expenselog` (
	`dateadded` INT NOT NULL ,
	`name` VARCHAR( 25 ) NOT NULL ,
	`type` VARCHAR( 2 ) NOT NULL ,
	`cost` FLOAT NOT NULL ,
	INDEX ( `dateadded` )
) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `phpvms_financedata` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`month` int(11) NOT NULL,
	`year` int(11) NOT NULL,
	`data` text NOT NULL,
	`total` float NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `phpvms_acarsdata` (
  `id` int(11) NOT NULL auto_increment,
  `pilotid` varchar(11) NOT NULL default '0',
  `flightnum` varchar(11) NOT NULL default '0',
  `pilotname` varchar(100) NOT NULL default '',
  `aircraft` varchar(12) NOT NULL default '',
  `lat` varchar(15) NOT NULL default '',
  `lng` varchar(15) NOT NULL default '',
  `heading` smallint(6) NOT NULL default '0',
  `alt` varchar(6) NOT NULL default '',
  `gs` int(11) NOT NULL default '0',
  `depicao` varchar(4) NOT NULL default '',
  `depapt` varchar(255) NOT NULL default '',
  `arricao` varchar(4) NOT NULL default '',
  `arrapt` text NOT NULL,
  `deptime` time NOT NULL default '00:00:00',
  `timeremaining` varchar(6) NOT NULL default '',
  `arrtime` time NOT NULL default '00:00:00',
  `route` TEXT NOT NULL,
  `route_details` TEXT NOT NULL,
  `distremain` varchar(6) NOT NULL default '',
  `phasedetail` varchar(255) NOT NULL default '',
  `online` varchar(10) NOT NULL default '',
  `messagelog` text NOT NULL,
  `lastupdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `client` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pilotid` (`pilotid`)
) ENGINE=INNODB; 

CREATE TABLE `phpvms_airlines` (
  `id` int(11) NOT NULL auto_increment,
  `code` char(3) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  `enabled` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB;

CREATE TABLE `phpvms_aircraft` (
  `id` int(11) NOT NULL auto_increment,
  `icao` varchar(4) NOT NULL default '',
  `name` varchar(12) NOT NULL default '',
  `fullname` varchar(50) NOT NULL default '',
  `registration` varchar(30) NOT NULL,
  `downloadlink` text NOT NULL,
  `imagelink` text NOT NULL,
  `range` varchar(15) NOT NULL default '0',
  `weight` varchar(15) NOT NULL default '0',
  `cruise` varchar(15) NOT NULL default '0',
  `maxpax` FLOAT NOT NULL default '0',
  `maxcargo` FLOAT NOT NULL default '0',
  `minrank` INT NOT NULL DEFAULT '0',
  `ranklevel` INT NOT NULL DEFAULT '0',
  `enabled` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `phpvms_navdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(7) NOT NULL,
  `title` varchar(25) NOT NULL,
  `airway` varchar(7) DEFAULT NULL,
  `airway_type` varchar(1) DEFAULT NULL,
  `seq` int(11) NOT NULL,
  `loc` varchar(4) NOT NULL,
  `lat` float(8,6) NOT NULL,
  `lng` float(9,6) NOT NULL,
  `freq` varchar(7) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `airway` (`airway`)
) ENGINE=MyISAM;

CREATE TABLE `phpvms_airports` (
  `id` int(11) NOT NULL auto_increment,
  `icao` varchar(5) NOT NULL default '',
  `name` text NOT NULL,
  `country` varchar(50) NOT NULL default '',
  `lat` float NOT NULL default '0',
  `lng` float NOT NULL default '0',
  `hub` smallint(6) NOT NULL default '0',
  `fuelprice` FLOAT NOT NULL default '0',
  `chartlink` TEXT NOT NULL, 
  PRIMARY KEY  (`id`),
  UNIQUE KEY `icao` (`icao`)
) ENGINE=InnoDB;


CREATE TABLE `phpvms_schedules` (
  `id` int(11) NOT NULL auto_increment,
  `code` char(3) NOT NULL default '',
  `flightnum` varchar(10) NOT NULL default '0',
  `depicao` varchar(4) NOT NULL default '',
  `arricao` varchar(4) NOT NULL default '',
  `route` text NOT NULL,
  `route_details` TEXT NOT NULL,
  `aircraft` text NOT NULL,
  `flightlevel` VARCHAR( 6 ) NOT NULL,
  `distance` float NOT NULL default '0',
  `deptime` varchar(15) NOT NULL default '',
  `arrtime` varchar(15) NOT NULL default '',
  `flighttime` FLOAT NOT NULL default '0',
  `daysofweek` VARCHAR( 7 ) NOT NULL DEFAULT '0123456',
  `price` FLOAT NOT NULL,
  `flighttype` VARCHAR( 1 ) NOT NULL DEFAULT 'P',
  `timesflown` int(11) NOT NULL default '0',
  `notes` text NOT NULL,
  `enabled` int(11) NOT NULL default '1',
  `bidid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `depicao` (`depicao`),
  KEY `flightnum` (`flightnum`),
  KEY `depicao_arricao` (`depicao`,`arricao`),
  KEY `code` (`code`)
) ENGINE=InnoDB ;

CREATE TABLE `phpvms_news` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(30) NOT NULL default '',
  `body` text NOT NULL,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `postedby` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB ;

INSERT INTO `phpvms_news` (`subject`,`body`,`postdate`,`postedby`)
VALUES ('Welcome to phpVMS!',  'Thanks for installing and using phpVMS!<br /> Check out the <a href="http://docs.phpvms.net/" target="_new">docs</a> for help and information on getting started on your VA.<br /><br />This is just a starter skin - customize yours completely. This is just a basic, barebones example of what a skin is and how it works. Check out the crystal folder in the lib/skins directory. Make your own copy and fiddle around. For help, check out the forum, and skinning docs. Also, be sure to check out the <a href="http://www.phpvms.net/tutorials" target="_blank">skinning tutorials</a> for a quick primer. The <a href="http://forum.phpvms.net" target="_blank">forums</a> are also filled with plenty of helpful people for any questions you may have.<br /><br />Good luck!', NOW(), 'phpVMS Installer');

CREATE TABLE `phpvms_pages` (
  `pageid` int(11) NOT NULL auto_increment,
  `pagename` varchar(30) NOT NULL default '',
  `filename` varchar(30) NOT NULL default '',
  `order` smallint(6) NOT NULL default '0',
  `postedby` varchar(50) NOT NULL default '',
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `public` smallint(6) NOT NULL default '0',
  `enabled` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`pageid`),
  UNIQUE KEY `pagename` (`pagename`)
) ENGINE=InnoDB ;

CREATE TABLE `phpvms_ranks` (
  `rankid` int(11) NOT NULL auto_increment,
  `rank` varchar(32) NOT NULL default '',
  `rankimage` text NOT NULL,
  `minhours` smallint(6) NOT NULL default '0',
  `payrate` float NOT NULL default '0',
  PRIMARY KEY  (`rankid`),
  UNIQUE KEY `rank` (`rank`)
) ENGINE=InnoDB ;

INSERT INTO `phpvms_ranks` VALUES(1, 'New Hire','', 0, 18.0); 

CREATE TABLE `phpvms_sessions` (
   `id` INT NOT NULL auto_increment ,
   `pilotid` INT NOT NULL ,
   `ipaddress` VARCHAR( 25 ) NOT NULL ,
   `logintime` DATETIME NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE = MYISAM ;

CREATE TABLE `phpvms_pilots` (
  `pilotid` int(11) NOT NULL auto_increment,
  `firstname` varchar(25) NOT NULL default '',
  `lastname` varchar(25) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `code` char(3) NOT NULL default '',
  `location` varchar(32) NOT NULL default '',
  `hub` varchar(4) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `salt` varchar(32) NOT NULL default '',
  `bgimage` varchar(30) NOT NULL default '',
  `lastlogin` date NOT NULL default '0000-00-00',
  `totalflights` int(11) NOT NULL default '0',
  `totalhours` float NOT NULL default '0',
  `totalpay` float NOT NULL default '0',
  `transferhours` float NOT NULL default '0',
  `rankid` INT NOT NULL DEFAULT '0',
  `rank` varchar(32) NOT NULL default 'New Hire',
  `ranklevel` INT NOT NULL DEFAULT '0',
  `confirmed` smallint(5) unsigned NOT NULL default '0',
  `retired` smallint(6) NOT NULL default '0',
  `joindate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastpirep` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastip` VARCHAR( 25 ) NULL DEFAULT '',
  `language` VARCHAR( 5 ) NOT NULL DEFAULT 'en',
  PRIMARY KEY  (`pilotid`),
  KEY `code` (`code`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB;

CREATE TABLE `phpvms_pireps` (
  `pirepid` int(11) NOT NULL auto_increment,
  `pilotid` int(11) NOT NULL default '0',
  `code` char(3) NOT NULL default '',
  `flightnum` varchar(10) NOT NULL default '0',
  `depicao` varchar(4) NOT NULL default '',
  `arricao` varchar(4) NOT NULL default '',
  `route` TEXT NOT NULL,
  `route_details` TEXT NOT NULL,
  `aircraft` varchar(12) NOT NULL default '',
  `flighttime` varchar(10) NOT NULL default '',
  `flighttime_stamp` TIME NOT NULL,
  `distance` smallint(6) NOT NULL default '0',
  `landingrate` FLOAT NOT NULL DEFAULT 0.0,
  `submitdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `accepted` smallint(6) NOT NULL default '0',
  `log` text NOT NULL,
  `load` INT(11) NOT NULL,
  `fuelused` FLOAT NOT NULL DEFAULT 0.0,
  `fuelunitcost` FLOAT NOT NULL DEFAULT 0.0,
  `fuelprice` FLOAT NOT NULL DEFAULT 5.10,
  `price` FLOAT NOT NULL,
  `flighttype` VARCHAR( 1 ) NOT NULL DEFAULT 'P',
  `gross` FLOAT NOT NULL,
  `pilotpay` FLOAT NOT NULL,
  `expenses` FLOAT NOT NULL,
  `expenselist` BLOB NOT NULL,
  `revenue` FLOAT NOT NULL,
  `source` VARCHAR(25) NOT NULL,
  `exported` TINYINT NOT NULL,
  `rawdata` TEXT NOT NULL,
  PRIMARY KEY  (`pirepid`)
) ENGINE=InnoDB;

CREATE TABLE `phpvms_pirepcomments` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`pirepid` INT NOT NULL ,
	`pilotid` INT NOT NULL ,
	`comment` TEXT NOT NULL ,
	`postdate` DATETIME NOT NULL ,
	PRIMARY KEY ( `id` )
)ENGINE=INNODB;

CREATE TABLE `phpvms_customfields` (
	`fieldid` INT NOT NULL AUTO_INCREMENT ,
	`title` VARCHAR( 75 ) NOT NULL ,
	`fieldname` VARCHAR( 75 ) NOT NULL ,
	`value` TEXT NOT NULL ,
	`type` VARCHAR( 25 ) NOT NULL DEFAULT 'text',
	`public` smallint(6) NOT NULL default '0',
	`showonregister` smallint(6) NOT NULL default '0',
	PRIMARY KEY ( `fieldid` ),
	UNIQUE KEY `fieldname` (`fieldname`)
)ENGINE=INNODB;

CREATE TABLE `phpvms_fieldvalues` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`fieldid` INT NOT NULL ,
	`pilotid` INT NOT NULL ,
	`value` TEXT NOT NULL ,
	PRIMARY KEY ( `id` )
)ENGINE=INNODB;

CREATE TABLE `phpvms_groups` (
  `groupid` int(11) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL default '',
  `permissions` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB ;

INSERT INTO `phpvms_groups` (`name`, `permissions`) VALUES ('Administrators', '35651519');
INSERT INTO `phpvms_groups` (`name`) VALUES ('Active Pilots');

CREATE TABLE `phpvms_groupmembers` (
	`id` int(11) NOT NULL auto_increment,
	`groupid` int(11) NOT NULL default '0',
	`pilotid` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
)ENGINE=INNODB;

CREATE TABLE `phpvms_pirepfields` (
	`fieldid` INT NOT NULL AUTO_INCREMENT ,
	`title` VARCHAR( 25 ) NOT NULL ,
	`name` VARCHAR( 25 ) NOT NULL ,
	`type` VARCHAR (25) NOT NULL,
	`options` TEXT NOT NULL,
	PRIMARY KEY ( `fieldid` ),
	UNIQUE KEY `name` (`name`)
) ENGINE = INNODB; 

CREATE TABLE `phpvms_pirepvalues` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`fieldid` INT NOT NULL ,
	`pirepid` INT NOT NULL ,
	`value` TEXT NOT NULL ,
	PRIMARY KEY ( `id` )
)ENGINE=INNODB;

CREATE TABLE `phpvms_bids` (
	`bidid` INT NOT NULL AUTO_INCREMENT ,
	`pilotid` int(11) NOT NULL default '0',
	`routeid` int(11) NOT NULL default '0',
	`dateadded` DATE NOT NULL,
	PRIMARY KEY ( `bidid` )
) ENGINE = INNODB; 

CREATE TABLE `phpvms_settings` (
	`id` int(11) NOT NULL auto_increment,
	`friendlyname` varchar(25) NOT NULL default '',
	`name` varchar(25) NOT NULL default '',
	`value` varchar(150) NOT NULL default '',
	`descrip` varchar(150) NOT NULL default '',
	`core` smallint(6) NOT NULL default '1',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `name` (`name`)
)ENGINE=INNODB;

CREATE TABLE `phpvms_languages` (
	`id` int(11) NOT NULL auto_increment,
	`language` varchar(5) NOT NULL,
	`name` varchar(25) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `language` (`language`)
)ENGINE=INNODB;

ALTER TABLE `phpvms_fieldvalues`
  ADD CONSTRAINT `phpvms_fieldvalues_ibfk_1` FOREIGN KEY (`fieldid`) REFERENCES `phpvms_customfields` (`fieldid`) ON DELETE CASCADE,
  ADD CONSTRAINT `phpvms_fieldvalues_ibfk_2` FOREIGN KEY (`pilotid`) REFERENCES `phpvms_pilots` (`pilotid`) ON DELETE CASCADE;

ALTER TABLE `phpvms_groupmembers`
  ADD CONSTRAINT `phpvms_groupmembers_ibfk_1` FOREIGN KEY (`groupid`) REFERENCES `phpvms_groups` (`groupid`) ON DELETE CASCADE,
  ADD CONSTRAINT `phpvms_groupmembers_ibfk_2` FOREIGN KEY (`pilotid`) REFERENCES `phpvms_pilots` (`pilotid`) ON DELETE CASCADE;

ALTER TABLE `phpvms_pilots`
  ADD CONSTRAINT `phpvms_pilots_ibfk_1` FOREIGN KEY (`code`) REFERENCES `phpvms_airlines` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `phpvms_pilots_ibfk_2` FOREIGN KEY (`language`) REFERENCES `phpvms_languages` (`language`) ON UPDATE CASCADE;

ALTER TABLE `phpvms_pirepcomments`
  ADD CONSTRAINT `phpvms_pirepcomments_ibfk_1` FOREIGN KEY (`pirepid`) REFERENCES `phpvms_pireps` (`pirepid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `phpvms_schedules`
  ADD CONSTRAINT `phpvms_schedules_ibfk_1` FOREIGN KEY (`code`) REFERENCES `phpvms_airlines` (`code`) ON UPDATE CASCADE;

INSERT INTO `phpvms_settings` VALUES(NULL, 'Virtual Airline Name', 'SITE_NAME', 'PHPVMS', 'The name of your site. This will show up in the browser title bar.', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Webmaster Email Address', 'ADMIN_EMAIL', '', 'This is the email address that email will get sent to/from', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Date Format', 'DATE_FORMAT', 'm/d/Y', 'This is the date format to be used around the site.', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Current Skin', 'CURRENT_SKIN', 'crystal', 'Available skins', 1);
INSERT INTO `phpvms_settings` VALUES(NULL, 'Default User Group', 'DEFAULT_GROUP', 'Active Pilots', 'This is the default group if they are not explicitly denied', 1);
INSERT INTO `phpvms_settings` VALUES(NULL , 'Total VA Hours', 'TOTAL_HOURS', '0', 'Your total hours', 0);

INSERT INTO `phpvms_languages` VALUES (NULL, 'en', 'English');
INSERT INTO `phpvms_languages` VALUES (NULL, 'es', 'Spanish');
