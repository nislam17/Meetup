
--
-- Database: `meetup`
--
CREATE DATABASE IF NOT EXISTS `meetup` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `meetup`;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE IF NOT EXISTS `member` (
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) NOT NULL DEFAULT '',
  `firstname` varchar(20) NOT NULL DEFAULT '',
  `lastname` varchar(20) NOT NULL DEFAULT '',
  `zipcode` int(5) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `group_id` int(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`group_id`),
  KEY `group_ibfk_1` (`username`),
  CONSTRAINT `group_ibfk_1` FOREIGN KEY (`username`) REFERENCES `member` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `interest`
--

DROP TABLE IF EXISTS `interest`;
CREATE TABLE IF NOT EXISTS `interest` (
  `interest_name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groupinterest`
--

DROP TABLE IF EXISTS `groupinterest`;
CREATE TABLE IF NOT EXISTS `groupinterest` (
  `interest_name` varchar(20) NOT NULL DEFAULT '',
  `group_id` int(20) NOT NULL,
  PRIMARY KEY (`group_id`,`interest_name`),
  KEY `groupinterest_ibfk_2` (`interest_name`),
  CONSTRAINT `groupinterest_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`),
  CONSTRAINT `groupinterest_ibfk_2` FOREIGN KEY (`interest_name`) REFERENCES `interest` (`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `memberinterest`
--

DROP TABLE IF EXISTS `memberinterest`;
CREATE TABLE IF NOT EXISTS `memberinterest` (
  `username` varchar(20) NOT NULL DEFAULT '',
  `interest_name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`username`,`interest_name`),
  KEY `userinterest_ibfk_2` (`interest_name`),
  CONSTRAINT `userinterest_ibfk_1` FOREIGN KEY (`username`) REFERENCES `member` (`username`),
  CONSTRAINT `userinterest_ibfk_2` FOREIGN KEY (`interest_name`) REFERENCES `interest` (`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `groupuser`
--

DROP TABLE IF EXISTS `groupuser`;
CREATE TABLE IF NOT EXISTS `groupuser` (
  `group_id` int(20) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `authorized` tinyint(1) NOT NULL,
  PRIMARY KEY (`group_id`,`username`),
  KEY `groupuser_ibfk_2` (`username`),
  CONSTRAINT `groupuser_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`),
  CONSTRAINT `groupuser_ibfk_2` FOREIGN KEY (`username`) REFERENCES `member` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `lname` varchar(20) NOT NULL DEFAULT '',
  `zip` int(5) NOT NULL,
  `street` varchar(50) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `latitude` bigint(50) NOT NULL,
  `longitude` bigint(50) NOT NULL,
  PRIMARY KEY (`lname`,`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `group_id` int(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `zip` int(5) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `group_id` (`group_id`),
  KEY `event_ibfk_2` (`lname`,`zip`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`),
  CONSTRAINT `event_ibfk_2` FOREIGN KEY (`lname`, `zip`) REFERENCES `location` (`lname`, `zip`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eventuser`
--

DROP TABLE IF EXISTS `eventuser`;
CREATE TABLE IF NOT EXISTS `eventuser` (
  `event_id` int(20) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `rsvp` tinyint(1) NOT NULL,
  `rating` int(1) NOT NULL,
  PRIMARY KEY (`event_id`,`username`),
  KEY `username` (`username`),
  CONSTRAINT `eventuser_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  CONSTRAINT `eventuser_ibfk_2` FOREIGN KEY (`username`) REFERENCES `member` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
