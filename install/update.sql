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

CREATE TABLE `phpvms_expenselog` (
	`dateadded` INT NOT NULL ,
	`name` VARCHAR( 25 ) NOT NULL ,
	`type` VARCHAR( 2 ) NOT NULL ,
	`cost` FLOAT NOT NULL ,
	INDEX ( `dateadded` )
) ENGINE = MYISAM ;

ALTER TABLE `phpvms_pilots` ADD `lastip` VARCHAR( 25 ) NULL DEFAULT '';
ALTER TABLE `phpvms_pilots` ADD `rankid` INT NOT NULL DEFAULT '0' AFTER `transferhours` ;
ALTER TABLE `phpvms_pilots` ADD `ranklevel` INT NOT NULL DEFAULT '0' AFTER `rank` ;
UPDATE `phpvms_pilots` p SET `rankid` =  ( SELECT `rankid` FROM `phpvms_ranks` WHERE rank = p.rank ) ;

ALTER TABLE `phpvms_pireps` ADD `gross` FLOAT NOT NULL AFTER `flighttype`;
ALTER TABLE `phpvms_pireps` ADD `route` TEXT NOT NULL AFTER `arricao`, ADD `route_details` TEXT NOT NULL AFTER `route`;

ALTER TABLE `phpvms_acarsdata` ADD `route` TEXT NOT NULL AFTER `arrtime`, ADD `route_details` TEXT NOT NULL AFTER `route`;

ALTER TABLE `phpvms_schedules` DROP `maxload` ;
ALTER TABLE `phpvms_schedules` ADD `route_details` TEXT NOT NULL AFTER `route`;

-- Aircraft account for ranks;
ALTER TABLE `phpvms_aircraft` ADD `minrank` INT NOT NULL DEFAULT '0' AFTER `maxcargo`;
ALTER TABLE `phpvms_aircraft` ADD `ranklevel` INT NOT NULL DEFAULT '0' AFTER `minrank` ;

-- It's sometimes missing
INSERT INTO `phpvms_settings` VALUES(NULL , 'Total VA Hours', 'TOTAL_HOURS', '0', 'Your total hours', 0);

-- Remove deprecated settings;
DELETE FROM `phpvms_settings` WHERE `name`='PHPVMS_VERSION';
DELETE FROM `phpvms_settings` WHERE `name`='NOTIFY_UPDATE';
DELETE FROM `phpvms_settings` WHERE `name`='GOOGLE_KEY';

-- Create language table
CREATE TABLE IF NOT EXISTS `phpvms_languages` (
	`id` int(11) NOT NULL auto_increment,
	`language` varchar(5) NOT NULL,
	`name` varchar(25) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `language` (`language`)
)ENGINE=INNODB;

-- Insert default languages
INSERT INTO `phpvms_languages` VALUES (NULL, 'en', 'English');
INSERT INTO `phpvms_languages` VALUES (NULL, 'es', 'Spanish');

-- Add language reference
ALTER TABLE `phpvms_pilots` ADD CONSTRAINT `phpvms_pilots_ibfk_2` FOREIGN KEY (`language`) REFERENCES `phpvms_languages` (`language`) ON UPDATE CASCADE;
