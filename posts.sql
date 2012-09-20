-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 20, 2012 at 05:51 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.6-1~dotdeb.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `awwnime`
--

-- --------------------------------------------------------

--
-- Table structure for table `dictionary`
--

CREATE TABLE IF NOT EXISTS `dictionary` (
  `word` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `image_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_hist_r1` double NOT NULL,
  `image_hist_r2` double NOT NULL,
  `image_hist_r3` double NOT NULL,
  `image_hist_r4` double NOT NULL,
  `image_hist_g1` double NOT NULL,
  `image_hist_g2` double NOT NULL,
  `image_hist_g3` double NOT NULL,
  `image_hist_g4` double NOT NULL,
  `image_hist_b1` double NOT NULL,
  `image_hist_b2` double NOT NULL,
  `image_hist_b3` double NOT NULL,
  `image_hist_b4` double NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `image_parent` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3593 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `post_date` int(11) NOT NULL,
  `post_updated` int(11) NOT NULL,
  `post_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_poster` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `post_flair` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `post_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_score` int(11) DEFAULT NULL,
  `post_processed` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
