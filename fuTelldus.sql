-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Vert: localhost
-- Generert den: 13. Apr, 2013 00:46 AM
-- Tjenerversjon: 5.5.30-cll
-- PHP-Versjon: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `futelldus`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_config`
--

CREATE TABLE IF NOT EXISTS `futelldus_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(256) NOT NULL,
  `config_value` varchar(256) NOT NULL,
  `comment` varchar(256) NOT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dataark for tabell `futelldus_config`
--

INSERT INTO `futelldus_config` (`config_id`, `config_name`, `config_value`, `comment`) VALUES
(1, 'pagetitle', 'fuTelldus', ''),
(10, 'default_language', 'en', ''),
(11, 'mail_from', 'mail@mydomain.com', ''),
(13, 'chart_max_days', '365', ''),
(14, 'public_page_language', 'en', '');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_devices`
--

CREATE TABLE IF NOT EXISTS `futelldus_devices` (
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `statevalue` tinyint(4) NOT NULL,
  `methods` tinyint(4) NOT NULL,
  `type` varchar(64) NOT NULL,
  `client` mediumint(9) NOT NULL,
  `clientname` varchar(128) NOT NULL,
  `online` tinyint(4) NOT NULL,
  `editable` tinyint(4) NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_schedule`
--

CREATE TABLE IF NOT EXISTS `futelldus_schedule` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `direction` varchar(16) NOT NULL,
  `warning_value` double NOT NULL,
  `type` varchar(32) NOT NULL,
  `repeat_alert` smallint(6) NOT NULL,
  `device` int(11) NOT NULL,
  `device_set_state` tinyint(4) NOT NULL,
  `send_to_mail` tinyint(4) NOT NULL,
  `last_warning` int(11) NOT NULL,
  `notification_mail_primary` varchar(256) NOT NULL,
  `notification_mail_secondary` varchar(256) NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_sensors`
--

CREATE TABLE IF NOT EXISTS `futelldus_sensors` (
  `user_id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `last_update` int(11) NOT NULL,
  `ignored` tinyint(4) NOT NULL,
  `client` int(11) NOT NULL,
  `clientname` varchar(256) NOT NULL,
  `online` tinyint(4) NOT NULL,
  `editable` tinyint(4) NOT NULL,
  `monitoring` tinyint(4) NOT NULL,
  `public` tinyint(4) NOT NULL,
  PRIMARY KEY (`sensor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_sensors_log`
--

CREATE TABLE IF NOT EXISTS `futelldus_sensors_log` (
  `sensor_id` int(11) NOT NULL,
  `time_updated` int(11) NOT NULL,
  `temp_value` double NOT NULL,
  `humidity_value` double NOT NULL,
  PRIMARY KEY (`sensor_id`,`time_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_sensors_shared`
--

CREATE TABLE IF NOT EXISTS `futelldus_sensors_shared` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `url` varchar(256) NOT NULL,
  `show_in_main` tinyint(4) NOT NULL,
  `disable` tinyint(4) NOT NULL COMMENT '0=view, 1=disabled',
  PRIMARY KEY (`share_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dataark for tabell `futelldus_sensors_shared`
--

INSERT INTO `futelldus_sensors_shared` (`share_id`, `user_id`, `description`, `url`, `show_in_main`, `disable`) VALUES
(1, 1, 'Developers sensor', 'http://robertan.com/app/telldus/fuTelldus/public/xml_sensor.php?sensorID=871223', 1, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_users`
--

CREATE TABLE IF NOT EXISTS `futelldus_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `language` varchar(64) NOT NULL,
  `admin` tinyint(4) NOT NULL,
  `chart_type` varchar(64) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dataark for tabell `futelldus_users`
--

INSERT INTO `futelldus_users` (`user_id`, `mail`, `password`, `language`, `admin`, `chart_type`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1, '');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `futelldus_users_telldus_config`
--

CREATE TABLE IF NOT EXISTS `futelldus_users_telldus_config` (
  `user_id` int(11) NOT NULL,
  `sync_from_telldus` tinyint(4) NOT NULL COMMENT '0=off, 1=on',
  `public_key` varchar(256) NOT NULL,
  `private_key` varchar(256) NOT NULL,
  `token` varchar(256) NOT NULL,
  `token_secret` varchar(256) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dataark for tabell `futelldus_users_telldus_config`
--

INSERT INTO `futelldus_users_telldus_config` (`user_id`, `sync_from_telldus`, `public_key`, `private_key`, `token`, `token_secret`) VALUES
(1, 1, 'FEHUVEW84RAFR5SP22RABURUPHAFRUNU', 'ZUXEVEGA9USTAZEWRETHAQUBUR69U6EF', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
