DROP DATABASE IF EXISTS `test_schema`;
-- DROP USER IF EXISTS 'testuser'@'localhost';

CREATE DATABASE `test_schema`;
USE `test_schema`;

CREATE USER 'testuser'@'localhost' IDENTIFIED BY 'TestUser';
GRANT SELECT,EXECUTE ON `test_schema`.* TO 'testuser'@'localhost';

DROP TABLE IF EXISTS `main_table`;
CREATE TABLE `main_table` (
  `id` int(11) NOT NULL,
  `value` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `main_table` VALUES (10,'ten'),(20,'twenty'),(30,'thirty');

DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f1`() RETURNS varchar(10) CHARSET latin1
BEGIN
  RETURN '100';
END ;;

CREATE DEFINER=`root`@`localhost` FUNCTION `f2`(_i integer) RETURNS int(11)
BEGIN
  RETURN _i+1;
END ;;

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp`(_i integer, _s varchar(20))
BEGIN
  SELECT `id`,CONCAT(`value`,_s) as `value` FROM main_table WHERE `id`>_i;
END ;;
