-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2012 at 06:54 PM
-- Server version: 5.0.51
-- PHP Version: 5.4.6-1~dotdeb.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `awwnime`
--

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` varchar(12) collate utf8_unicode_ci NOT NULL,
  `post_date` int(11) NOT NULL,
  `post_title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `post_link` varchar(255) collate utf8_unicode_ci NOT NULL,
  `post_poster` varchar(50) collate utf8_unicode_ci NOT NULL,
  `post_flair` varchar(50) collate utf8_unicode_ci NOT NULL,
  `post_keywords` varchar(255) collate utf8_unicode_ci default NULL,
  `post_hist_r1` double default NULL,
  `post_hist_r2` double default NULL,
  `post_hist_r3` double default NULL,
  `post_hist_r4` double default NULL,
  `post_hist_g1` double default NULL,
  `post_hist_g2` double default NULL,
  `post_hist_g3` double default NULL,
  `post_hist_g4` double default NULL,
  `post_hist_b1` double default NULL,
  `post_hist_b2` double default NULL,
  `post_hist_b3` double default NULL,
  `post_hist_b4` double default NULL,
  `post_score` int(11) default NULL,
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
