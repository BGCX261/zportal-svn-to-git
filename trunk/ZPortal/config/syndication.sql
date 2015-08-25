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

#
# Structure for the `channels` table :
#
DROP TABLE IF EXISTS channels;
CREATE TABLE `channels` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`pub_date` DATE NOT NULL,
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
	KEY (managing_editor)
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
	`description` TEXT,
	`link` TEXT,
	`category` TEXT,
	`comments` TEXT,
	PRIMARY KEY  (`id`),
	FOREIGN KEY (`channel_id`) REFERENCES channels (id),
	KEY (author)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

