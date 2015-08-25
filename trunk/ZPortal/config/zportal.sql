# 
#  Zend Portal
# 
#  LICENSE
# 
#  This source file is subject to the new BSD license that is bundled
#  with this package in the file LICENSE.txt.
#  It is also available through the world-wide-web at this URL:
#  http://framework.zend.com/license/new-bsd
#  If you did not receive a copy of the license and are unable to
#  obtain it through the world-wide-web, please send an email
#  to license@zend.com so we can send you a copy immediately.
# 
#  @category   Zend
#  @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
#  @license    http://framework.zend.com/license/new-bsd     New BSD License
#  @author 

# SQL Manager 2007 for MySQL 4.1.2.1 (work find on 5.x)
# ---------------------------------------
# Host     : 10.1.2.176
# Port     : 3306
# Database : zportal

SET FOREIGN_KEY_CHECKS=0;

#
# Structure for the `users` table :
#
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','member') NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB';

INSERT INTO `users` SET `id`=1, `email`='yuval@zend.com', role='admin';

#
# Structure for the `feeds` table :
#
DROP TABLE IF EXISTS `feeds`;
CREATE TABLE `feeds` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` int(11) unsigned,
  `owner_id` int(11) unsigned NOT NULL,
  `type` enum('feed','group') NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255),
  `description` text NOT NULL,
  `url` varchar(255),
  `updated` varchar(64),
  `xml` varchar(255),
  `inter_channel`  BOOLEAN NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `url` (`url`),
  KEY `owner_id` (`owner_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `feed_group` FOREIGN KEY (`group_id`) REFERENCES `feeds` (`id`),
  CONSTRAINT `feed_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; (`group_id`) REFER `zportal/feeds`(`id';
INSERT INTO `feeds` SET `id`=1, `owner_id`=1,  `type`='group', `name` = 'ROOT', `description` = 'Root group', path='', xml='';  

#
# Structure for the `portal` table :
#
DROP TABLE IF EXISTS `portal`;
CREATE TABLE `portal` (
  `user_id` int(11) unsigned NOT NULL,
  `feed_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`feed_id`),
  KEY `user_id` (`user_id`),
  KEY `feed_id` (`feed_id`),
  CONSTRAINT `portal_feed` FOREIGN KEY (`feed_id`) REFERENCES `feeds` (`id`),
  CONSTRAINT `portal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; (`user_id`) REFER `zportal/users`(`id`';

#
# Structure for the `channels` table :
#
DROP TABLE IF EXISTS channels;
CREATE TABLE `channels` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`pub_date` TIMESTAMP NOT NULL,
	`managing_editor` int(11) unsigned NOT NULL,
	`copyright` varchar(255),
	`link` TEXT,
	`description` TEXT,
	`category` TEXT,
	`last_build_date` DATE,
	`time_to_live` int(11) UNSIGNED,
	`skip_hours` TIME,
	`skip_days` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
	PRIMARY KEY  (id),
	FOREIGN KEY (`managing_editor`) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for the `items` table :
#
DROP TABLE IF EXISTS items;
CREATE TABLE `items` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`channel_id` int(11) UNSIGNED NOT NULL,
	`title` varchar(255) NOT NULL,
	`pub_date` TIMESTAMP NOT NULL,
	`author` int(11) unsigned NOT NULL ,
	`description` TEXT,
	`link` TEXT,
	`category` TEXT,
	`comments` TEXT,
	PRIMARY KEY  (`id`),
	FOREIGN KEY (`channel_id`) REFERENCES channels (id),
	FOREIGN KEY (`author`) REFERENCES users (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

