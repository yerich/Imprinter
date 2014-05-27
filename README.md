Imprinter, version 1
====================

Imprinter is a PHP-based CMS written for Imprint, the official student newspaper
of the University of Waterloo. It's features include:

- **Usability and Performance Speed:** Features like drag-and-drop image upload and auto-complete on many form fields make tedious tasks faster and easier, and a framworkless architecture means that pages load quickly, even in high traffic
- **Flexible Templating:** Content generation and presentation are seperated with a simple but fexible templating engine that adapts Imprinter to a newspaper's specific needs, and a unique "frontpage" template automatically fills the homepage and section pages with dynamic content.
- **Tagging and Filtering:** Sophisticated filtering lets the most important stories occupy the most important spots on the homepage, while several different types of tags let articles be sorted quickly and effectively.

Please contact me by email at yerich AT gmail DOT com for information about incorporating Imprinter into your website.

The ckeditor plugin is required by this project; however, it is not included to save space. Simply download the library
from http://ckeditor.com/, and copy it into /scripts/ckeditor/.';

#Setup

Requires PHP 5.x and mySQL 5. Copy this repository into your web root folder and set up the database by running the schema provided below. Then, enter your database connection inforation into `include/config.php`. If set up correctly, you should be able to see the homepage correctly, albeit with no content. To access the administration interface, go to `/admin/`. By default, the username is "admin" and the password is "password".


#Database schema

`

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(300) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `config` (
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  `caption` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `view` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `content` mediumtext NOT NULL,
  `tags` text NOT NULL,
  `media` text NOT NULL,
  `data` mediumtext NOT NULL,
  `created` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `SEARCH` (`title`,`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT 'tag',
  `name` varchar(250) NOT NULL,
  `num_articles` int(11) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`name`(100),`type`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `salt` varchar(10) NOT NULL DEFAULT 'e4d908c3b0',
  `sessid` varchar(3400) NOT NULL,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `email` varchar(75) DEFAULT NULL,
  `userlevel` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `users` (salt, username, password, userlevel) VALUES ("f4d6e4ab08", "admin", "bd0c34c40317cfd6aefa68ca5564e3bb", 5) ;

`
