DROP TABLE IF EXISTS `sysparam`;[EOL]
CREATE TABLE `sysparam` (
  `param` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(20) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

INSERT INTO `sysparam` (`param`, `value`) VALUES
('setversion', '07070002');[EOL]

DROP TABLE IF EXISTS `auslosungeinzel`;[EOL]
CREATE TABLE `auslosungeinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `pos` int(3) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '1',
  `del` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `auslosungteam`;[EOL]
CREATE TABLE `auslosungteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `pos` int(3) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '1',
  `del` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `clientmonitor`;[EOL]
CREATE TABLE `clientmonitor` (
  `ip` varchar(30) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` text,
  `verid` int(11) NOT NULL,
  `type` int(3) NOT NULL DEFAULT '0',
  `ms` int(3) DEFAULT '0',
  `matchid` varchar(50) DEFAULT NULL,
  `nameid1` int(11) DEFAULT NULL,
  `nameid2` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `coach`;[EOL]
CREATE TABLE `coach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(50) DEFAULT '',
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `geburt` date NOT NULL DEFAULT '0000-00-00',
  `sichtbar` int(1) NOT NULL DEFAULT '1',
  `kyu` int(1) DEFAULT '0',
  `dan` int(1) DEFAULT '0',
  `sonstiges` text,
  `geschlecht` char(1) NOT NULL,
  `vereinnr` int(11) NOT NULL,
  `wkfid` varchar(15) DEFAULT NULL,
  `passportid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `doubleeliminationeinzel`;[EOL]
CREATE TABLE `doubleeliminationeinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `doubleeliminationteam`;[EOL]
CREATE TABLE `doubleeliminationteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `dtmdefaults`;[EOL]
CREATE TABLE `dtmdefaults` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vernr` int(11) NOT NULL DEFAULT '0',
  `type` char(1) DEFAULT '',
  `sex` char(1) DEFAULT '',
  `catname` varchar(255) DEFAULT '',
  `fighttime` int(5) NOT NULL DEFAULT '0',
  `color` varchar(7) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `entryfeemodel`;[EOL]
CREATE TABLE `entryfeemodel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` int(2) NOT NULL DEFAULT '1',
  `verid` int(11) NOT NULL DEFAULT '0',
  `discountentry` int(3) NOT NULL DEFAULT '0',
  `discount` float NOT NULL DEFAULT '0',
  `maxclub` float NOT NULL DEFAULT '0',
  `enablediscount` int(1) NOT NULL DEFAULT '0',
  `enablemaxclub` int(1) NOT NULL DEFAULT '0',
  `indentrydiscounttype` int(1) NOT NULL DEFAULT '1',
  `discountflat` float NOT NULL DEFAULT '0',
  `discountcatflat` text,
  `coachfee` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `ergebniseinzel`;[EOL]
CREATE TABLE `ergebniseinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `erg` int(2) NOT NULL DEFAULT '0',
  `done` int(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `ergebnisteam`;[EOL]
CREATE TABLE `ergebnisteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `erg` int(2) NOT NULL DEFAULT '0',
  `done` int(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]
DROP VIEW IF EXISTS `garecount`;[EOL]
CREATE TABLE `garecount` (
`katnr` int(11)
,`numatl` bigint(21)
);[EOL]
DROP TABLE IF EXISTS `kata`;[EOL]
CREATE TABLE `kata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `stilnr` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;[EOL]

INSERT INTO `kata` (`id`, `bezeichnung`, `stilnr`) VALUES
(1, 'Taikyoku Shodan', 1),
(10, 'Tekki Nidan', 1),
(100, 'Pinan Shodan', 4),
(101, 'Pinan Nidan', 4),
(102, 'Pinan Sandan', 4),
(103, 'Pinan Yondan', 4),
(104, 'Pinan Godan', 4),
(105, 'Naihanchi', 4),
(106, 'Kushanku', 4),
(107, 'Chinto', 4),
(108, 'Seishan', 4),
(109, 'Bassai', 4),
(11, 'Tekki Sandan', 1),
(110, 'Jion', 4),
(111, 'Jitte', 4),
(112, 'Niseishi', 4),
(113, 'Wanshu', 4),
(114, 'Rohai', 4),
(12, 'Jion', 1),
(13, 'Bassai Dai', 1),
(14, 'Bassai Sho', 1),
(15, 'Kanku Dai ', 1),
(16, 'Kanku Sho', 1),
(17, 'Empi', 1),
(18, 'Jitte', 1),
(19, 'Jiin', 1),
(2, 'Taikyoku Nidan', 1),
(20, 'Hangetsu', 1),
(21, 'Gankaku', 1),
(22, 'Nijushiho Sho', 1),
(23, 'Chinte', 1),
(24, 'Sochin', 1),
(25, 'Wankan', 1),
(26, 'Meikyo', 1),
(27, 'Goju Shiho Dai', 1),
(28, 'Goju Shiho Sho', 1),
(29, 'Unsu', 1),
(3, 'Taikyoku Sandan', 1),
(30, 'Ten No Kata', 1),
(31, 'Gekisai-Dai-Ichi', 2),
(32, 'Gekisai-Dai-Ni', 2),
(33, 'Sanchin', 2),
(34, 'Tensho', 2),
(35, 'Saifa', 2),
(36, 'Sepai', 2),
(37, 'Seyunchin', 2),
(38, 'Sanseru', 2),
(39, 'Shisochin', 2),
(4, 'Heian Shodan', 1),
(40, 'Seisan', 2),
(41, 'Kururunfa', 2),
(42, 'Suparinpai', 2),
(43, 'Heian Shodan', 3),
(44, 'Heian Nidan', 3),
(45, 'Heian Sandan', 3),
(46, 'Heian Yondan', 3),
(47, 'Heian Godan', 3),
(48, 'Naifanchin Shodan', 3),
(49, 'Naifanchin Nidan', 3),
(5, 'Heian Nidan', 1),
(50, 'Naifanchin Sandan', 3),
(51, 'Rohai Shodan', 3),
(52, 'Rohai Nidan', 3),
(53, 'Rohai Sandan', 3),
(54, 'Matsumura No Rohai', 3),
(55, 'Bassai Dai', 3),
(56, 'Annan', 3),
(57, 'Pachu', 3),
(58, 'Bassai Sho', 3),
(59, 'Matsumura No Bassai', 3),
(6, 'Heian Sandan', 1),
(60, 'Tomari No Bassai', 3),
(61, 'Jion', 3),
(62, 'Jiin', 3),
(63, 'Jitte', 3),
(64, 'Wanshu', 3),
(65, 'Tomari No Wanshu', 3),
(66, 'Chatanyara Kushanku', 3),
(67, 'Kosukun Dai', 3),
(68, 'Kosukun Sho', 3),
(69, 'Kosukun Shiho', 3),
(7, 'Heian Yondan', 1),
(70, 'Chinte', 3),
(71, 'Annanko', 3),
(72, 'Gojushiho', 3),
(73, 'Chinto', 3),
(74, 'Kururunfa', 3),
(75, 'Saifa', 3),
(76, 'Sanchin', 3),
(77, 'Tensho', 3),
(78, 'Sanseru', 3),
(79, 'Sepai', 3),
(8, 'Heian Godan', 1),
(80, 'Seiyunchin', 3),
(81, 'Seisan', 3),
(82, 'Matsumura No Sanchin', 3),
(83, 'Shisoching', 3),
(84, 'Suparinpai', 3),
(85, 'Heiku', 3),
(86, 'Hakucho', 3),
(87, 'Nipaipo', 3),
(88, 'Papporen', 3),
(89, 'Aoyagi', 3),
(9, 'Tekki Shodan', 1),
(90, 'Joruku', 3),
(91, 'Miyojo', 3),
(92, 'Shinpa', 3),
(93, 'Matsukaze', 3),
(94, 'Shinsei', 3),
(95, 'Shinsei Ni', 3),
(96, 'Niseishi', 3),
(97, 'Sochin', 3),
(98, 'Unshu', 3),
(99, 'Paiku', 3);[EOL]

DROP TABLE IF EXISTS `kategorie`;[EOL]
CREATE TABLE `kategorie` (
  `knr` int(11) NOT NULL AUTO_INCREMENT,
  `katbez` varchar(255) NOT NULL DEFAULT '',
  `alterVon` int(10) NOT NULL DEFAULT '0',
  `alternichtmehr` int(10) NOT NULL DEFAULT '0',
  `geschlecht` char(1) DEFAULT NULL,
  `team` char(1) DEFAULT NULL,
  `sportart` int(11) DEFAULT NULL,
  `typ` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`knr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `landesverband`;[EOL]
CREATE TABLE `landesverband` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nationnr` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '',
  `kurz` varchar(20) DEFAULT NULL,
  `natid` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `mitschrifteinzel`;[EOL]
CREATE TABLE `mitschrifteinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `mitschriftteam`;[EOL]
CREATE TABLE `mitschriftteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `names`;[EOL]
CREATE TABLE `names` (
  `nnr` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `geburt` date NOT NULL DEFAULT '0000-00-00',
  `vereinnr` int(11) NOT NULL DEFAULT '3',
  `geschlecht` char(1) DEFAULT NULL,
  `gewicht` int(5) DEFAULT NULL,
  `groesse` int(11) DEFAULT NULL,
  `sichtbar` int(1) unsigned NOT NULL DEFAULT '1',
  `kyu` int(1) DEFAULT '0',
  `dan` int(1) DEFAULT '0',
  `nationnr` int(11) DEFAULT '0',
  `stpktnr` int(11) DEFAULT '0',
  `nationalid` varchar(30) DEFAULT NULL,
  `sonstiges` text,
  `wkfid` varchar(15) DEFAULT NULL,
  `passportid` varchar(50) DEFAULT NULL,
  `extid` int(11) DEFAULT NULL,
  PRIMARY KEY (`nnr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `nation`;[EOL]
CREATE TABLE `nation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '',
  `iso` varchar(5) NOT NULL DEFAULT '',
  `kurz` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;[EOL]

INSERT INTO `nation` (`id`, `bezeichnung`, `iso`, `kurz`) VALUES
(106, 'ITALIA', 'IT', 'ITA');[EOL]

DROP TABLE IF EXISTS `nennungencoach`;[EOL]
CREATE TABLE `nennungencoach` (
  `id` int(11) NOT NULL,
  `vernr` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `nennungeneinzel`;[EOL]
CREATE TABLE `nennungeneinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `katnr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `gesetzt` int(1) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `checkok` int(1) DEFAULT NULL,
  `checkcom` varchar(50) DEFAULT NULL,
  `checkokentry` int(1) DEFAULT NULL,
  `checkcomentry` varchar(50) DEFAULT NULL,
  `measurement` float DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `nennungenofficial`;[EOL]
CREATE TABLE `nennungenofficial` (
  `id` int(11) NOT NULL,
  `vernr` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `daysinfo` varchar(50) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `nennungenreferee`;[EOL]
CREATE TABLE `nennungenreferee` (
  `id` int(11) NOT NULL,
  `vernr` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `daysinfo` varchar(50) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `nennungenteam`;[EOL]
CREATE TABLE `nennungenteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `gesetzt` int(1) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `teamid` int(11) NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `teamid` (`teamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `official`;[EOL]
CREATE TABLE `official` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(50) DEFAULT '',
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `geburt` date NOT NULL DEFAULT '0000-00-00',
  `sichtbar` int(1) NOT NULL DEFAULT '1',
  `geschlecht` char(1) NOT NULL,
  `vereinnr` int(11) NOT NULL,
  `roleid` int(11) NOT NULL,
  `sonstiges` varchar(255) DEFAULT '',
  `wkfid` varchar(15) DEFAULT NULL,
  `passportid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `poolsiegereinzel`;[EOL]
CREATE TABLE `poolsiegereinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `poolsiegerteam`;[EOL]
CREATE TABLE `poolsiegerteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `xpos` int(11) NOT NULL DEFAULT '0',
  `ypos` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `tabletype` int(1) NOT NULL DEFAULT '0',
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `punktelisteeinzel`;[EOL]
CREATE TABLE `punktelisteeinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `s1` float DEFAULT '0',
  `s2` float DEFAULT '0',
  `s3` float DEFAULT '0',
  `s4` float DEFAULT '0',
  `s5` float DEFAULT '0',
  `s6` float DEFAULT '0',
  `s7` float DEFAULT '0',
  `kata` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `punktelisteteam`;[EOL]
CREATE TABLE `punktelisteteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `fieldpos` int(11) NOT NULL DEFAULT '0',
  `pool` char(1) NOT NULL DEFAULT '',
  `s1` float DEFAULT '0',
  `s2` float DEFAULT '0',
  `s3` float DEFAULT '0',
  `s4` float DEFAULT '0',
  `s5` float DEFAULT '0',
  `s6` float DEFAULT '0',
  `s7` float DEFAULT '0',
  `kata` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `referee`;[EOL]
CREATE TABLE `referee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(50) DEFAULT '',
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `geburt` date NOT NULL DEFAULT '0000-00-00',
  `sichtbar` int(1) NOT NULL DEFAULT '1',
  `kyu` int(1) DEFAULT '0',
  `dan` int(1) DEFAULT '0',
  `lizenznat` text,
  `geschlecht` char(1) NOT NULL,
  `vereinnr` int(11) NOT NULL,
  `nationnr` int(11) DEFAULT NULL,
  `lizenzint` text,
  `lizenznr` varchar(255) NOT NULL,
  `wkfid` varchar(15) DEFAULT NULL,
  `passportid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `role_typ`;[EOL]
CREATE TABLE `role_typ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;[EOL]

INSERT INTO `role_typ` (`id`, `bezeichnung`) VALUES
(1, 'official_type_a_medical'),
(2, 'official_type_b_representative'),
(3, 'official_type_c_press'),
(4, 'official_type_d_physio'),
(5, 'official_type_e_others');[EOL]

DROP TABLE IF EXISTS `siegerehrung_erledigt`;[EOL]
CREATE TABLE `siegerehrung_erledigt` (
  `vernr` int(11) NOT NULL,
  `knr` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `sportart`;[EOL]
CREATE TABLE `sportart` (
  `sportartnr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sportartnr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `stilrichtung`;[EOL]
CREATE TABLE `stilrichtung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;[EOL]

INSERT INTO `stilrichtung` (`id`, `bezeichnung`) VALUES
(1, 'Shotokan'),
(2, 'Gojuryu'),
(3, 'Shitoryu'),
(4, 'Wadoryu');[EOL]

DROP TABLE IF EXISTS `team`;[EOL]
CREATE TABLE `team` (
  `teamid` int(11) NOT NULL,
  `nnr` int(11) NOT NULL,
  `checkokentry` int(1) DEFAULT NULL,
  `checkcomentry` varchar(50) DEFAULT NULL,
  `measurement` float DEFAULT '0',
  `checkok` int(1) DEFAULT NULL,
  `checkcom` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `team_warteliste`;[EOL]
CREATE TABLE `team_warteliste` (
  `teamid` int(11) NOT NULL,
  `nnr` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `timetable`;[EOL]
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vernr` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `tatamis` int(3) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `catname` varchar(255) NOT NULL DEFAULT '',
  `sex` varchar(1) NOT NULL DEFAULT '',
  `type` int(1) NOT NULL DEFAULT '1',
  `tatami` int(3) NOT NULL DEFAULT '0',
  `pool` int(2) NOT NULL DEFAULT '1',
  `pools` int(3) NOT NULL DEFAULT '1',
  `entries` int(5) NOT NULL DEFAULT '0',
  `fighttime` int(5) NOT NULL DEFAULT '0',
  `edited` int(1) NOT NULL DEFAULT '0',
  `color` varchar(7) NOT NULL DEFAULT '',
  `starttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `trostrundeeinzel`;[EOL]
CREATE TABLE `trostrundeeinzel` (
  `id` bigint(20) NOT NULL,
  `nnr` int(11) NOT NULL,
  `fieldpos` int(5) NOT NULL,
  `trostrunde` int(3) NOT NULL,
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `trostrundeteam`;[EOL]
CREATE TABLE `trostrundeteam` (
  `id` bigint(20) NOT NULL,
  `vereinnr` int(11) NOT NULL,
  `mannschaft` varchar(255) NOT NULL,
  `fieldpos` int(5) NOT NULL,
  `trostrunde` int(3) NOT NULL,
  `points` int(3) DEFAULT NULL,
  `kata` int(11) DEFAULT NULL,
  `matchid` varchar(50) DEFAULT NULL,
  `matchtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `trostrunde_pool`;[EOL]
CREATE TABLE `trostrunde_pool` (
  `id` bigint(20) NOT NULL,
  `vernr` int(11) NOT NULL,
  `knr` int(11) NOT NULL,
  `pool` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `user`;[EOL]
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL DEFAULT '',
  `passwort` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `gesperrt` int(1) NOT NULL DEFAULT '0',
  `titel` varchar(50) DEFAULT '',
  `vorname` varchar(100) DEFAULT '',
  `nachname` varchar(100) DEFAULT '',
  `geburtstag` date DEFAULT '0000-00-00',
  `adresse` varchar(255) DEFAULT NULL,
  `plz` varchar(50) DEFAULT NULL,
  `ort` varchar(50) DEFAULT NULL,
  `nation` int(11) DEFAULT '0',
  `telefon` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `mobiltelefon` varchar(50) DEFAULT NULL,
  `create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aktiv` int(1) NOT NULL DEFAULT '0',
  `billingaddress` text,
  `mandant` int(3) NOT NULL DEFAULT '2',
  `autopayment` int(3) NOT NULL DEFAULT '1',
  `paypalaccount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;[EOL]

INSERT INTO `user` (`id`, `user`, `passwort`, `email`, `gesperrt`, `titel`, `vorname`, `nachname`, `geburtstag`, `adresse`, `plz`, `ort`, `nation`, `telefon`, `fax`, `mobiltelefon`, `create`, `aktiv`, `billingaddress`, `mandant`, `autopayment`, `paypalaccount`) VALUES
(1, 'administrator', 'cdaeeeba9b4a4c5ebf042c0215a7bb0e', 'admin@sportdata.org', 0, '', '', '', '0000-00-00', '', '', '', 15, '', '', '', '0000-00-00 00:00:00', 1, '', 0, 0, '');[EOL]

DROP TABLE IF EXISTS `veranstaltung`;[EOL]
CREATE TABLE `veranstaltung` (
  `vernr` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '',
  `verdatum` varchar(20) NOT NULL DEFAULT '',
  `nennstart` varchar(20) NOT NULL DEFAULT '',
  `nennende` varchar(20) NOT NULL DEFAULT '',
  `user` int(11) DEFAULT NULL,
  `gesperrt` int(1) unsigned NOT NULL DEFAULT '0',
  `info` text,
  `passwd` varchar(50) DEFAULT NULL,
  `offen` int(1) DEFAULT NULL,
  `showstarter` int(1) DEFAULT NULL,
  `regmode` int(1) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `auslosungen` int(1) DEFAULT NULL,
  `land` int(10) unsigned NOT NULL DEFAULT '15',
  `lastchange` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `limitedto` int(11) DEFAULT NULL,
  `deletetodraw` int(1) DEFAULT '0',
  `waehrung` int(11) NOT NULL DEFAULT '45',
  `typ` int(11) DEFAULT '1',
  `lat` varchar(50) DEFAULT NULL,
  `lon` varchar(50) DEFAULT NULL,
  `liveblog` int(1) NOT NULL DEFAULT '1',
  `indlimitclub` int(5) NOT NULL DEFAULT '0',
  `teamlimitclub` int(5) NOT NULL DEFAULT '0',
  `usepaypal` int(1) DEFAULT '0',
  `paypalaccount` varchar(255) DEFAULT NULL,
  `paypalnoamount` int(1) DEFAULT '0',
  `systemtype` varchar(15) NOT NULL DEFAULT 'prod',
  `bisdatum` varchar(20) DEFAULT NULL,
  `entrylimit` int(10) NOT NULL DEFAULT '0',
  `premiumevent` int(1) DEFAULT '0',
  `usebothcutoffdates` int(1) DEFAULT '0',
  `othercutoffday` varchar(20) DEFAULT NULL,
  `useothercutoffday` int(1) DEFAULT NULL,
  `livedtm` int(1) DEFAULT NULL,
  PRIMARY KEY (`vernr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `veranstaltungkat`;[EOL]
CREATE TABLE `veranstaltungkat` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `alterVon` int(10) NOT NULL DEFAULT '0',
  `alternichtmehr` int(10) NOT NULL DEFAULT '0',
  `startgeld` float DEFAULT NULL,
  `teammin` int(11) DEFAULT NULL,
  `teammax` int(11) DEFAULT NULL,
  `othercutoffday` varchar(20) DEFAULT NULL,
  `resulttype` int(1) DEFAULT NULL,
  `roundrobin` int(1) DEFAULT NULL,
  `pools` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `veranstaltung_club_entryfee`;[EOL]
CREATE TABLE `veranstaltung_club_entryfee` (
  `vernr` int(11) NOT NULL,
  `vereinnr` int(11) NOT NULL,
  `paidammount` float DEFAULT '0',
  `comment` varchar(255) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `veranstaltung_compcount`;[EOL]
CREATE TABLE `veranstaltung_compcount` (
  `vernr` int(11) NOT NULL,
  `nnr` int(11) NOT NULL,
  `eventcount` int(7) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `veranstaltung_typ`;[EOL]
CREATE TABLE `veranstaltung_typ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;[EOL]

INSERT INTO `veranstaltung_typ` (`id`, `bezeichnung`) VALUES
(1, 'db_var_event_typ_tournament'),
(2, 'db_var_event_typ_seminar'),
(3, 'db_var_event_typ_other');[EOL]

DROP TABLE IF EXISTS `veranstaltung_user`;[EOL]
CREATE TABLE `veranstaltung_user` (
  `verid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `verein`;[EOL]
CREATE TABLE `verein` (
  `vereinnr` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '',
  `nation` int(11) NOT NULL DEFAULT '15',
  `lvnr` int(11) DEFAULT '0',
  `sektionnr` int(11) DEFAULT '0',
  `stpktnr` int(11) DEFAULT '0',
  `createdbymanager` int(11) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `nationalid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`vereinnr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]

DROP TABLE IF EXISTS `waehrung`;[EOL]
CREATE TABLE `waehrung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;[EOL]

INSERT INTO `waehrung` (`id`, `bezeichnung`) VALUES
(45, 'EUR');[EOL]

DROP TABLE IF EXISTS `wartelisteeinzel`;[EOL]
CREATE TABLE `wartelisteeinzel` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `katnr` int(11) NOT NULL DEFAULT '0',
  `nnr` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `approved` int(1) DEFAULT NULL,
  `comment` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `wartelisteteam`;[EOL]
CREATE TABLE `wartelisteteam` (
  `vernr` int(11) NOT NULL DEFAULT '0',
  `knr` int(11) NOT NULL DEFAULT '0',
  `vereinnr` int(11) NOT NULL DEFAULT '0',
  `mannschaft` varchar(255) NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrator` int(11) DEFAULT NULL,
  `teamid` int(11) NOT NULL AUTO_INCREMENT,
  `approved` int(1) DEFAULT NULL,
  `comment` varchar(50) DEFAULT NULL,
  UNIQUE KEY `teamid` (`teamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;[EOL]



DROP TABLE IF EXISTS `stili`;[EOL]
CREATE TABLE `stili` (
  `idpart` int(11) NOT NULL,
  `tipo` tinyint(1) NOT NULL,
  `idstile` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

DROP TABLE IF EXISTS `garecount`;[EOL]
CREATE VIEW `garecount` AS select `nennungeneinzel`.`katnr` AS `katnr`,count(`nennungeneinzel`.`nnr`) AS `numatl` from `nennungeneinzel` group by `nennungeneinzel`.`katnr` order by count(`nennungeneinzel`.`nnr`) desc;[EOL]

DROP TABLE IF EXISTS `puntisoc`;[EOL]
CREATE TABLE `puntisoc` (
  `partecipanti` int(11) NOT NULL,
  `classifica` int(11) NOT NULL,
  `punti` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;[EOL]

INSERT INTO `puntisoc` (`partecipanti`, `classifica`, `punti`) VALUES
(1, 1, 2),
(2, 1, 2),
(2, 2, 1),
(3, 1, 4),
(3, 2, 2),
(3, 3, 1),
(4, 1, 4),
(4, 2, 2),
(4, 3, 1),
(4, 4, 1),
(5, 1, 6),
(5, 2, 4),
(5, 3, 2),
(5, 4, 2),
(5, 5, 1),
(6, 1, 6),
(6, 2, 4),
(6, 3, 2),
(6, 4, 2),
(6, 5, 1),
(6, 6, 1),
(7, 1, 8),
(7, 2, 6),
(7, 3, 4),
(7, 4, 4),
(7, 5, 2),
(7, 6, 2),
(7, 7, 1),
(8, 1, 8),
(8, 2, 6),
(8, 3, 4),
(8, 4, 4),
(8, 5, 2),
(8, 6, 2),
(8, 7, 1),
(8, 8, 1),
(9, 1, 12),
(9, 2, 8),
(9, 3, 6),
(9, 4, 6),
(9, 5, 4),
(9, 6, 4),
(9, 7, 2),
(9, 8, 2),
(9, 9, 1);[EOL]
