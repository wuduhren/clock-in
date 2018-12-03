-- phpMyAdmin SQL Dump
-- version 4.7.5
-- https://www.phpmyadmin.net/
--
-- 主機: localhost:3306
-- 產生時間： 2018 年 03 月 26 日 10:17
-- 伺服器版本: 5.6.38
-- PHP 版本： 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 資料庫： `clock-in`
--

-- --------------------------------------------------------

--
-- 資料表結構 `clock`
--

CREATE TABLE `clock` (
  `date_work` date NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `time_start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `dayoff`
--

CREATE TABLE `dayoff` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `holiday`
--

CREATE TABLE `holiday` (
  `date_holiday` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `holiday_category` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `php_session`
--

CREATE TABLE `php_session` (
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `expiry` int(11) UNSIGNED NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `schedule`
--

CREATE TABLE `schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `time_start` time NOT NULL DEFAULT '08:00:00',
  `time_end` time NOT NULL DEFAULT '17:00:00',
  `time_range` int(10) UNSIGNED NOT NULL DEFAULT '15'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `schedule`
--

INSERT INTO `schedule` (`id`, `time_start`, `time_end`, `time_range`) VALUES
(1, '08:00:00', '17:00:00', 15);

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `schedule` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `auth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `time_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `user`
--

INSERT INTO `user` (`id`, `account`, `password`, `email`, `schedule`, `status`, `auth`, `time_create`) VALUES
(1, 'root', '1084c29da0ccd38cfcf3d9c92c148026', '', 0, 1, 1, '2018-03-26 16:16:17');

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `clock`
--
ALTER TABLE `clock`
  ADD PRIMARY KEY (`date_work`,`uid`);

--
-- 資料表索引 `dayoff`
--
ALTER TABLE `dayoff`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`date_holiday`);

--
-- 資料表索引 `php_session`
--
ALTER TABLE `php_session`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `dayoff`
--
ALTER TABLE `dayoff`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表 AUTO_INCREMENT `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表 AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;