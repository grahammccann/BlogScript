-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2022 at 11:41 AM
-- Server version: 5.7.39-0ubuntu0.18.04.2
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_the-fish-guys.com`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` bigint(20) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` bigint(20) NOT NULL,
  `image_name` varchar(50) NOT NULL,
  `image_alt_text` varchar(50) NOT NULL,
  `image_is_header` enum('no','yes') NOT NULL,
  `image_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` bigint(20) NOT NULL,
  `member_username` varchar(50) NOT NULL,
  `member_password` varchar(50) NOT NULL,
  `member_password_md5` varchar(32) NOT NULL,
  `member_email` varchar(50) NOT NULL,
  `member_is_admin` enum('no','yes') NOT NULL,
  `member_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `member_username`, `member_password`, `member_password_md5`, `member_email`, `member_is_admin`, `member_date`) VALUES
(2, 'admin', '...', '0717ee60b139f9fee9210f454b48935c', 'contact@site.com', 'yes', '2022-09-20 18:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `option_id` bigint(20) NOT NULL,
  `option_name` longtext NOT NULL,
  `option_value` longtext NOT NULL,
  `option_description` longtext NOT NULL,
  `option_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`option_id`, `option_name`, `option_value`, `option_description`, `option_date`) VALUES
(1, 'homepage_title', '...', 'This sets the <title></title> tag value on the homepage.', '2021-05-16 15:47:56'),
(2, 'homepage_description', '...', 'This sets the meta description on the homepage.', '2021-05-16 17:38:44'),
(3, 'homepage_pagination', '10', 'This sets the number of articles to show on the homepage.', '2021-10-18 21:55:15'),
(4, 'homepage_show_categories', '1', 'This sets if the categories section will show. (true or false)', '2021-10-23 20:40:35'),
(5, 'homepage_about', 'We are dedicated to helping fellow fish enthusiasts care for their pet fish indoor and outdoor ...', 'This sets the description on what your blog is about.', '2021-10-23 20:55:27'),
(6, 'homepage_hide_login_link', '1', 'This sets if the admin login link on the main page footer shows to everyone or not. (true or false)', '2021-10-29 20:22:40'),
(7, 'footer_amazon_disclosure_text', '<strong>site.com</strong> is a participant in the Amazon Services LLC Associates Program, an affiliate advertising program designed to provide a means for sites to earn advertising fees by advertising and linking to Amazon.com.', 'This sets the text to be displayed to comply with Amazon Associates.', '2021-10-31 09:00:19'),
(8, 'footer_twitter', 'https://twitter.com', 'This sets your twitter account on the footer.', '2021-10-31 09:04:15'),
(9, 'footer_meta', 'https://www.facebook.com/', 'This sets your meta (aka facebook) account on the footer.', '2021-10-31 09:06:06'),
(10, 'footer_instagram', 'https://www.instagram.com/', 'This sets your instagram account on the footer.', '2021-10-31 09:23:11'),
(11, 'site_admin_email', 'contact@site.com', 'This sets the main email associated with your site.', '2022-09-20 20:54:07'),
(12, 'ads_post_top', '', 'This sets the ad code at the top of the post page.', '2022-10-09 15:24:42'),
(13, 'category_style_icon', '<i class=\"fa-solid fa-...\" style=\"color: orange;\"></i>', 'This sets a custom icon next to each category name.', '2022-12-18 09:59:54'),
(14, 'google_adsense', '', 'This sets the AdSense code needed by Google in the <head></head> tags.', '2023-01-19 10:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` bigint(20) NOT NULL,
  `page_slug` varchar(50) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `page_body` longtext NOT NULL,
  `page_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` bigint(20) NOT NULL,
  `post_category_id` bigint(20) NOT NULL,
  `post_member_id` bigint(20) NOT NULL,
  `post_title` longtext NOT NULL,
  `post_body` longtext NOT NULL,
  `post_seo_title` longtext NOT NULL,
  `post_seo_description` longtext NOT NULL,
  `post_image` varchar(100) NOT NULL,
  `post_image_alt_text` varchar(100) NOT NULL,
  `post_status` enum('published','draft','archived') NOT NULL,
  `post_date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_views` bigint(20) NOT NULL DEFAULT '0',
  `post_sticky` enum('0','1') NOT NULL,
  `post_source_url` longtext NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `option_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
