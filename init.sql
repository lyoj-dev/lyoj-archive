CREATE TABLE `contest` (
  `id` int(255) DEFAULT NULL,
  `title` longtext DEFAULT NULL,
  `starttime` int(20) DEFAULT NULL,
  `duration` int(20) DEFAULT NULL,
  `type` int(11) DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `contest_ranking` (
  `id` int(255) DEFAULT NULL,
  `uid` int(255) DEFAULT NULL,
  `score` int(255) DEFAULT NULL,
  `time` int(255) DEFAULT NULL,
  `info` longtext DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `contest_signup` (
  `id` int(255) DEFAULT NULL,
  `uid` int(255) DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `crontab` (
  `id` int(255) DEFAULT NULL,
  `duration` int(20) DEFAULT NULL,
  `lasttime` int(20) DEFAULT NULL,
  `command` longtext DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `judger` (
  `id` varchar(128) DEFAULT NULL,
  `config` longtext DEFAULT NULL,
  `name` longtext DEFAULT NULL,
  `heartbeat` int(20) DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `logindata` (
  `uid` int(255) DEFAULT NULL,
  `csrf` longtext DEFAULT NULL,
  `sessdata` longtext DEFAULT NULL,
  `time` int(20) DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `problem` (
  `id` int(255) DEFAULT NULL,
  `name` longtext CHARACTER SET utf8 DEFAULT NULL,
  `bg` longtext CHARACTER SET utf8 DEFAULT NULL,
  `descrip` longtext CHARACTER SET utf8 DEFAULT NULL,
  `input` longtext CHARACTER SET utf8 DEFAULT NULL,
  `output` longtext CHARACTER SET utf8 DEFAULT NULL,
  `cases` longtext CHARACTER SET utf8 DEFAULT NULL,
  `hint` longtext CHARACTER SET utf8 DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT 0,
  `banned` tinyint(1) DEFAULT 0,
  `difficult` int(11) DEFAULT 0,
  `contest` int(255) DEFAULT 0
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `status` (
  `id` int(255) DEFAULT NULL,
  `uid` int(255) DEFAULT NULL,
  `pid` int(255) DEFAULT NULL,
  `lang` int(11) DEFAULT NULL,
  `code` longtext DEFAULT NULL,
  `result` longtext DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `status` longtext DEFAULT NULL,
  `ideinfo` longtext DEFAULT NULL,
  `judged` tinyint(1) DEFAULT NULL,
  `contest` int(255) DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `tags` (
  `tagname` longtext CHARACTER SET utf8 DEFAULT NULL,
  `id` int(255) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;
CREATE TABLE `user` (
  `id` int(255) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `passwd` longtext CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `permission` int(10) DEFAULT NULL,
  `email` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `salttime` int(20) DEFAULT NULL,
  `verify` tinyint(1) DEFAULT NULL,
  `verify_code` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL
) DEFAULT CHARSET=utf8mb4;