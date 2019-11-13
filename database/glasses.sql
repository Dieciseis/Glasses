SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS  `glasses`;
CREATE TABLE `glasses` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `figName` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `left_eye_x` double DEFAULT NULL,
  `right_eye_x` double DEFAULT NULL,
  `left_ear_x` double DEFAULT NULL,
  `right_ear_x` double DEFAULT NULL,
  `left_eye_y` double DEFAULT NULL,
  `right_eye_y` double DEFAULT NULL,
  `left_ear_y` double DEFAULT NULL,
  `right_ear_y` double DEFAULT NULL,
  `frame_shape` int(11) DEFAULT NULL,
  `frame_thickness` int(11) DEFAULT NULL,
  `frame_type` int(11) DEFAULT NULL,
  `frame_width` int(11) DEFAULT NULL,
  `materials` int(11) DEFAULT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='眼镜数据';

insert into `glasses`(`gid`,`figName`,`left_eye_x`,`right_eye_x`,`left_ear_x`,`right_ear_x`,`left_eye_y`,`right_eye_y`,`left_ear_y`,`right_ear_y`,`frame_shape`,`frame_thickness`,`frame_type`,`frame_width`,`materials`) values
('7','1.png',0.252,0.758,0.004,0.992,0.469,0.468,0.339,0.62,'0','0','1','0','2'),
('8','2.png',0.24,0.754,0.006,0.998,0.467,0.463,0.341,0.61,'0','0','0','0','1'),
('10','4.png',0.226,0.748,0.006,0.986,0.471,0.462,0.275,0.66,'1','0','1','1','1'),
('9','3.png',0.232,0.77,0.008,0.992,0.473,0.459,0.361,0.59,'0','1','1','0','0'),
('11','5.png',0.254,0.75,0.007,0.991,0.481,0.484,0.327,0.671,'1','0','0','1','2'),
('12','6.png',0.248,0.768,0.007,0.999,0.466,0.468,0.291,0.655,'1','1','0','1','0'),
('13','7.png',0.25,0.756,0.011,0.993,0.462,0.465,0.333,0.612,'1','0','0','0','1'),
('14','8.png',0.248,0.76,0.012,0.993,0.473,0.461,0.333,0.62,'1','1','0','0','0'),
('15','9.png',0.246,0.764,0.009,0.997,0.485,0.467,0.323,0.599,'1','0','1','0','1'),
('16','10.png',0.24,0.776,0.006,0.996,0.464,0.463,0.355,0.576,'0','1','2','0','0'),
('17','11.png',0.218,0.786,0.013,0.996,0.466,0.46,0.279,0.657,'2','1','0','1','1'),
('18','12.png',0.21,0.776,0.006,0.998,0.46,0.465,0.296,0.656,'2','0','0','1','1'),
('19','13.png',0.218,0.76,0.007,0.996,0.468,0.46,0.337,0.619,'0','1','0','0','1'),
('20','14.png',0.242,0.758,0.002,0.998,0.46,0.46,0.313,0.638,'0','0','0','1','1'),
('21','15.png',0.218,0.754,0.006,0.991,0.458,0.459,0.318,0.649,'0','1','0','1','1'),
('22','16.png',0.236,0.768,0.008,0.999,0.454,0.456,0.293,0.64,'0','0','0','1','1'),
('23','17.png',0.254,0.746,0.009,0.993,0.45,0.46,0.289,0.65,'1','0','0','1','1');
SET FOREIGN_KEY_CHECKS = 1;

