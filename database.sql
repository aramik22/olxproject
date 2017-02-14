-- Esquema de Project para OLX
-------------------------------------------------------------------------------
CREATE SCHEMA `PROJECT_OLX` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `picture` varchar(200) DEFAULT NULL COMMENT 'nombre de la foto subida',
  `address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
);
INSERT INTO users VALUES(1,'Aram','img.png','Pringles 1112');

--Tabla no especificada en requerimientos, es para en caso de necesitar que los administradores
--del sistema no esten harcodeados
CREATE TABLE `admins` (
  `admin_id` INT NOT NULL AUTO_INCREMENT,
  `admin_nick` VARCHAR(45) NOT NULL,
  `admin_pass` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE INDEX `admin_nick_UNIQUE` (`admin_nick` ASC));

INSERT INTO admins VALUES(1,'Aram',md5('OlxPass'));