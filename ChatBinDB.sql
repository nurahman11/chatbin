SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE `chatbox_old` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chatbox_old`;

CREATE TABLE IF NOT EXISTS `chat` (
  `chatid` int(10) NOT NULL AUTO_INCREMENT,
  `roomid` int(10) NOT NULL,
  `userid` int(10) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chatid`),
  KEY `roomid` (`roomid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `room` (
  `roomid` int(10) NOT NULL AUTO_INCREMENT,
  `roomowner` int(10) NOT NULL,
  `roomname` varchar(50) NOT NULL,
  `roompassword` varchar(100) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roomid`),
  KEY `roomowner` (`roomowner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `room` (`roomid`, `roomowner`, `roomname`, `roompassword`, `hidden`) VALUES
(1, 1, 'Public Chat', NULL, 0),
(2, 1, 'Admin Chat', 'a2b21c166976123dd24b3666ae946e80', 1);

CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `screenname` varchar(50) NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastactive` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `screenname` (`screenname`),
  UNIQUE KEY `password` (`password`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `user` (`userid`, `email`, `password`, `screenname`, `registerdate`, `lastactive`) VALUES
(0, 'bot@bot.bot', 'no one knows...', '[BOT]', '2013-03-01 09:41:23', '2013-03-24 09:05:37'),
(1, 'mail@wibisaja.com', 'c8c700700ef98da8db6a01b797b6c710', 'Wibisaja', '2013-03-20 11:23:41', '2013-04-05 08:28:36');


ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`roomid`) REFERENCES `room` (`roomid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`roomowner`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;
CREATE DATABASE `chatbox_pro` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chatbox_pro`;

CREATE TABLE IF NOT EXISTS `chat` (
  `chatid` int(10) NOT NULL AUTO_INCREMENT,
  `roomid` int(10) NOT NULL,
  `userid` int(10) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chatid`),
  KEY `roomid` (`roomid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `room` (
  `roomid` int(10) NOT NULL AUTO_INCREMENT,
  `roomowner` int(10) NOT NULL,
  `roomname` varchar(50) NOT NULL,
  `roompassword` varchar(100) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roomid`),
  KEY `roomowner` (`roomowner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `room` (`roomid`, `roomowner`, `roomname`, `roompassword`, `hidden`) VALUES
(1, 1, 'Public Chat', NULL, 0),
(2, 1, 'Admin Chat', 'a2b21c166976123dd24b3666ae946e80', 1);

CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `screenname` varchar(50) NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastactive` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `screenname` (`screenname`),
  UNIQUE KEY `password` (`password`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `user` (`userid`, `email`, `password`, `screenname`, `registerdate`, `lastactive`) VALUES
(0, 'bot@bot.bot', 'no one knows...', '[BOT]', '2013-03-01 09:41:23', '2013-03-24 09:05:37'),
(1, 'mail@wibisaja.com', '26baed2ce6b1e1278661ecc3b37f2498', 'Wibisaja', '2013-03-20 11:23:41', '2013-03-29 13:11:49'),
(2, 'w1bi@live.com', 'd4b8a43eed574b5e0b773313a9f120a6', 'AsuLo', '2013-03-21 19:37:36', '2013-03-29 13:15:03');


ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`roomid`) REFERENCES `room` (`roomid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`roomowner`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
