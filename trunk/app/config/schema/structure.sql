/*
SQLyog Ultimate - MySQL GUI v8.21 
MySQL - 5.0.67-community-nt : Database - tricks_cake
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tricks_cake` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `tricks_cake`;

/*Table structure for table `events` */

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `location_id` int(10) unsigned default NULL,
  `exp_date` datetime default NULL,
  `title` varchar(255) default '',
  `description` text,
  `url` varchar(255) default '',
  `complete` enum('yes','no') default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `locations` */

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `title` varchar(255) default '',
  `city` varchar(50) default '',
  `state` varchar(2) default '',
  `zip` int(10) unsigned default NULL,
  `address1` varchar(255) default '',
  `address2` varchar(255) default '',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `tags` */

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(50) default '',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `tags_to_events` */

DROP TABLE IF EXISTS `tags_to_events`;

CREATE TABLE `tags_to_events` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `event_id` int(10) unsigned default NULL,
  `user_id` int(10) unsigned default NULL,
  `tag_id` int(10) unsigned default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(50) default '',
  `pass` varchar(50) default '',
  `enabled` enum('yes','no') default 'yes',
  `activated` enum('yes','no') default 'no',
  `ac_code` varchar(32) default '',
  `city` varchar(50) default '',
  `state` varchar(2) default '',
  `zip` int(10) unsigned default NULL,
  `address1` varchar(255) default '',
  `address2` varchar(255) default '',
  `role` enum('admin','user') default 'user',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
