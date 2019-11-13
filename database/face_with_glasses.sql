﻿SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS  `face_with_glasses`;
CREATE TABLE `face_with_glasses` (
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `style1` int(11) DEFAULT NULL,
  `belong` int(11) DEFAULT NULL,
  `prob1` double DEFAULT '1',
  `style2` int(11) DEFAULT NULL,
  `prob2` double DEFAULT NULL,
  `style3` int(11) DEFAULT NULL,
  `prob3` double DEFAULT NULL,
  `style4` int(11) DEFAULT NULL,
  `prob4` double DEFAULT NULL,
  PRIMARY KEY (`fid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='戴眼镜的脸评价数据';

insert into `face_with_glasses`(`fid`,`gid`,`style1`,`belong`,`prob1`,`style2`,`prob2`,`style3`,`prob3`,`style4`,`prob4`) values
('35','9','1','0','1','2','1','1','1','2','1'),
('36','12','1','0','1','2','1','1','1','2','1'),
('33','10','1','0','1','2','1','1','1','1','1'),
('32','22','1','0','1','2','1','1','1','2','1'),
('37','22','1','0','1','2','1','1','1','2','1'),
('38','16','1','0','1','2','1','1','1','2','1'),
('47','7','2','0','1','2','1','1','1','2','1'),
('52','23','1','0','1','2','1','1','1','2','1'),
('50','12','1','0','1','2','1','1','1','2','1'),
('63','7','1','1',11.909370172998,'2',12.467261596536,'1',13.470322791016,'2',12.125838489229),
('64','23','1','1',9.841700565171,'2',10.470780614324,'1',10.86371840994,'2',10.15681858404),
('64','22','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','21','1','1',9.841700565171,'2',10.470780614324,'1',10.86371840994,'2',10.15681858404),
('64','20','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','19','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','18','1','1',10.203291624702,'2',10.780476899908,'1',11.246641895039,'2',10.408005455442),
('64','17','1','1',10.261420834513,'2',10.825613925247,'1',11.31800966478,'2',10.432183800144),
('64','16','1','1',10.119309953835,'2',10.711527924463,'1',11.152379719375,'2',10.359282031783),
('64','15','1','1',9.841700565171,'2',10.470780614324,'1',10.86371840994,'2',10.15681858404),
('64','14','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','13','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','12','1','1',9.841700565171,'2',10.470780614324,'1',10.86371840994,'2',10.15681858404),
('64','11','1','1',10.203291624702,'2',10.780476899908,'1',11.246641895039,'2',10.408005455442),
('64','10','1','1',10.00955701804,'2',10.619012179261,'1',11.035458402863,'2',10.286266631937),
('64','9','1','1',9.5703567487148,'2',10.22165441239,'1',10.591969866909,'2',9.9257679050512),
('64','8','1','1',9.3160794802622,'2',9.9924173494766,'1',10.340984923129,'2',9.7139076608286),
('64','7','1','1',10.119309953835,'2',10.711527924463,'1',11.152379719375,'2',10.359282031783),
('63','23','1','1',11.95433722081,'2',12.5357136364,'1',13.497562726862,'2',12.183156041469),
('63','22','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('63','21','1','1',11.95433722081,'2',12.5357136364,'1',13.497562726862,'2',12.183156041469),
('63','20','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('63','19','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('63','18','1','1',11.911040694983,'2',12.458695801589,'1',13.48360873249,'2',12.121974925417),
('63','17','1','1',11.927381686112,'2',12.465708778449,'1',13.513243734933,'2',12.133167746348),
('63','16','1','1',11.909370172998,'2',12.467261596536,'1',13.470322791016,'2',12.125838489229),
('63','15','1','1',11.95433722081,'2',12.5357136364,'1',13.497562726862,'2',12.183156041469),
('63','14','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('63','13','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('63','12','1','1',11.95433722081,'2',12.5357136364,'1',13.497562726862,'2',12.183156041469),
('63','11','1','1',11.911040694983,'2',12.458695801589,'1',13.48360873249,'2',12.121974925417),
('63','10','1','1',11.92341998118,'2',12.492526174817,'1',13.474550206489,'2',12.145838694197),
('63','9','1','1',11.995348668201,'2',12.590135419507,'1',13.532723179493,'2',12.231054070163),
('31','19','1','0','1','2','1','1','1','2','1'),
('30','14','1','0','1','2','1','1','1','2','1'),
('29','17','1','0','1','1','1','1','1','2','1'),
('28','10','1','0','1','2','1','1','1','2','1'),
('27','13','1','0','1','2','1','1','1','2','1'),
('50','15','1','0','1','2','1','1','1','2','1'),
('52','15','2','0','1','2','1','1','1','2','1'),
('47','12','1','0','1','2','1','1','1','2','1'),
('38','10','1','0','1','2','1','1','1','1','1'),
('37','14','1','0','1','2','1','1','1','2','1'),
('36','16','1','0','1','2','1','1','1','2','1'),
('35','22','1','0','1','2','1','1','1','2','1'),
('33','20','1','0','1','2','1','1','1','2','1'),
('32','9','1','0','1','2','1','1','1','2','1'),
('31','7','2','0','1','2','1','1','1','2','1'),
('30','8','1','0','1','2','1','1','1','2','1'),
('29','23','1','0','1','2','1','1','1','1','1'),
('28','22','1','0','1','2','1','1','1','2','1'),
('27','21','1','0','1','2','1','1','1','2','1'),
('50','20','1','0','1','2','1','1','1','2','1'),
('52','19','1','0','1','2','1','1','1','2','1'),
('47','18','1','0','1','2','1','1','1','2','1'),
('38','17','1','0','1','1','1','1','1','2','1'),
('37','16','1','0','1','2','1','1','1','2','1'),
('36','15','1','0','1','2','1','1','1','2','1'),
('35','14','2','0','1','2','1','1','1','2','1'),
('33','13','1','0','1','2','1','1','1','2','1'),
('32','12','1','0','1','2','1','1','1','2','1'),
('31','11','1','0','1','2','1','1','1','2','1'),
('30','10','1','0','1','2','1','1','1','2','1'),
('29','9','2','0','1','2','1','1','1','2','1'),
('28','8','1','0','1','2','1','1','1','2','1'),
('27','7','1','0','1','2','1','1','1','2','1'),
('63','8','1','1',11.985291950119,'2',12.594722404556,'1',13.519018005856,'2',12.228410941133);
SET FOREIGN_KEY_CHECKS = 1;

