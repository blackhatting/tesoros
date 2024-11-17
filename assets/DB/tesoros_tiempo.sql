/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - tesoros_tiempo
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tesoros_tiempo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `tesoros_tiempo`;

/*Table structure for table `bloqueos` */

DROP TABLE IF EXISTS `bloqueos`;

CREATE TABLE `bloqueos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_bloqueado` int(11) NOT NULL,
  `fecha_bloqueo` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_bloqueado` (`id_bloqueado`),
  CONSTRAINT `bloqueos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bloqueos_ibfk_2` FOREIGN KEY (`id_bloqueado`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `bloqueos` */

/*Table structure for table `empleos` */

DROP TABLE IF EXISTS `empleos`;

CREATE TABLE `empleos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabajo` varchar(255) DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  `privacidad_id` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `usuarios_id` (`usuarios_id`),
  KEY `privacidad_id` (`privacidad_id`),
  CONSTRAINT `empleos_ibfk_1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `empleos_ibfk_2` FOREIGN KEY (`privacidad_id`) REFERENCES `privacidad` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `empleos` */

insert  into `empleos`(`id`,`trabajo`,`usuarios_id`,`privacidad_id`) values 
(1,'Cruz Roja',18,1),
(2,'Presentador Televisión',18,3);

/*Table structure for table `estado_civil` */

DROP TABLE IF EXISTS `estado_civil`;

CREATE TABLE `estado_civil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(200) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `estado_civil` */

insert  into `estado_civil`(`id`,`estado`) values 
(1,'Soltero(a)'),
(2,'En una relación'),
(3,'Comprometido(a)'),
(4,'Casado(a)'),
(5,'En una unión civil'),
(6,'En una pareja de hecho'),
(7,'En una relación abierta'),
(8,'Es complicado'),
(9,'Separado(a)'),
(10,'Divorciado(a)'),
(11,'Viudo(a)');

/*Table structure for table `formacion` */

DROP TABLE IF EXISTS `formacion`;

CREATE TABLE `formacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudios` varchar(255) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `año_inicial` date DEFAULT NULL,
  `año_final` date DEFAULT NULL,
  `privacidad_id` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `privacidad_id` (`privacidad_id`),
  KEY `user_id` (`usuarios_id`),
  CONSTRAINT `formacion_ibfk_2` FOREIGN KEY (`privacidad_id`) REFERENCES `privacidad` (`id`),
  CONSTRAINT `formacion_ibfk_3` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `formacion` */

insert  into `formacion`(`id`,`estudios`,`usuarios_id`,`año_inicial`,`año_final`,`privacidad_id`) values 
(1,'Ciencias ocultas',18,NULL,NULL,1);

/*Table structure for table `privacidad` */

DROP TABLE IF EXISTS `privacidad`;

CREATE TABLE `privacidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `privacidad` varchar(100) NOT NULL,
  `private_img` varchar(255) DEFAULT 'assets/img/iconos/privacidad',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `privacidad` */

insert  into `privacidad`(`id`,`privacidad`,`private_img`) values 
(1,'Público','assets/img/iconos/privacidad/publico.png'),
(2,'Amigos','assets/img/iconos/privacidad/amigos.png'),
(3,'Solo Yo','assets/img/iconos/privacidad/privado.png');

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`nombre`) values 
(1,'Familiar'),
(2,'Voluntario'),
(3,'Muy Honorable');

/*Table structure for table `ubicacion` */

DROP TABLE IF EXISTS `ubicacion`;

CREATE TABLE `ubicacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(200) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `privacidad_id` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `usuarios_id` (`usuarios_id`),
  KEY `privacidad_id` (`privacidad_id`),
  CONSTRAINT `ubicacion_ibfk_1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `ubicacion_ibfk_2` FOREIGN KEY (`privacidad_id`) REFERENCES `privacidad` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `ubicacion` */

insert  into `ubicacion`(`id`,`pais`,`usuarios_id`,`privacidad_id`) values 
(2,'España',18,1);

/*Table structure for table `usuario_estado_civil` */

DROP TABLE IF EXISTS `usuario_estado_civil`;

CREATE TABLE `usuario_estado_civil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) DEFAULT NULL,
  `estado_civil_id` int(11) DEFAULT NULL,
  `privacidad_id` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `usuarios_id` (`usuarios_id`),
  KEY `estado_civil_id` (`estado_civil_id`),
  KEY `privacidad_id` (`privacidad_id`),
  CONSTRAINT `usuario_estado_civil_ibfk_1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `usuario_estado_civil_ibfk_2` FOREIGN KEY (`estado_civil_id`) REFERENCES `estado_civil` (`id`),
  CONSTRAINT `usuario_estado_civil_ibfk_3` FOREIGN KEY (`privacidad_id`) REFERENCES `privacidad` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `usuario_estado_civil` */

insert  into `usuario_estado_civil`(`id`,`usuarios_id`,`estado_civil_id`,`privacidad_id`) values 
(1,18,1,1);

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `codigo_verificacion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `es_publico` tinyint(1) DEFAULT 1,
  `nombre_real` varchar(50) DEFAULT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `telefono` varchar(200) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `ubicacion_id` int(100) DEFAULT NULL,
  `formacion_id` int(11) DEFAULT NULL,
  `empleo_id` int(11) DEFAULT NULL,
  `estadocivil_id` int(11) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `intentos_fallidos` int(11) DEFAULT 0,
  `cuenta_bloqueada` tinyint(4) DEFAULT 0,
  `privacidad_id` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `rol_id` (`rol_id`),
  KEY `empleo_id` (`empleo_id`),
  KEY `estadocivil_id` (`estadocivil_id`),
  KEY `formacion_id` (`formacion_id`),
  KEY `privacidad_id` (`privacidad_id`),
  KEY `ubicacion_id` (`ubicacion_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`empleo_id`) REFERENCES `empleos` (`id`),
  CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`estadocivil_id`) REFERENCES `estado_civil` (`id`),
  CONSTRAINT `usuarios_ibfk_4` FOREIGN KEY (`formacion_id`) REFERENCES `formacion` (`id`),
  CONSTRAINT `usuarios_ibfk_5` FOREIGN KEY (`privacidad_id`) REFERENCES `privacidad` (`id`),
  CONSTRAINT `usuarios_ibfk_6` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nombre`,`correo`,`contrasena`,`rol_id`,`codigo_verificacion`,`activo`,`creado_en`,`actualizado_en`,`es_publico`,`nombre_real`,`apellidos`,`telefono`,`direccion`,`ubicacion_id`,`formacion_id`,`empleo_id`,`estadocivil_id`,`foto_perfil`,`intentos_fallidos`,`cuenta_bloqueada`,`privacidad_id`) values 
(18,'borja','borjamaiques@hotmail.com','$2y$10$c9y6Gzrg6hWrpSezFWaBfeKHOXbZ6amuedEz76Z4zxUZQGRuXGFou',2,'8471b227d864b90008ba5ef657866ed9',0,'2024-10-14 10:08:02','2024-10-24 12:03:32',1,NULL,NULL,NULL,NULL,2,1,2,9,NULL,1,0,3),
(22,'blimblim','acaracrouted@hotmail.com','$argon2id$v=19$m=65536,t=4,p=1$UTJtcFBSYmFkbnRlZ2E4Mg$OJLKUxJ9w7O063xOpbOcxgtjGx2vrCN7cmswxJtnUiI',2,'d06f4988e259f0047598a9cc1f0f972f',0,'2024-10-24 13:05:50','2024-10-24 13:20:36',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1),
(23,'blimblom','hfghgfhgf@gdgdf','$argon2id$v=19$m=65536,t=4,p=1$RkFrUUFhYkxlUTljVDdtMg$ogah8OH8fZdxomraN65quuIh7bW6em5GQZX1CFi216E',1,'301532551a61f69620f91263a33447e3',0,'2024-10-24 13:21:45','2024-10-24 13:21:45',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
