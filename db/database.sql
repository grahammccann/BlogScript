-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 27, 2023 at 11:29 AM
-- Server version: 5.7.39-0ubuntu0.18.04.2
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_we-review-stuff.com`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` bigint(20) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clicks`
--

CREATE TABLE `clicks` (
  `click_id` bigint(11) NOT NULL,
  `click_page` varchar(250) NOT NULL,
  `click_ip` varchar(39) NOT NULL,
  `click_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` bigint(20) NOT NULL,
  `image_name` longtext NOT NULL,
  `image_alt_text` varchar(100) NOT NULL,
  `image_is_header` enum('no','yes') NOT NULL,
  `image_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE `ips` (
  `ip_id` bigint(11) NOT NULL,
  `ip_range` varchar(15) NOT NULL,
  `ip_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `member_username`, `member_password`, `member_password_md5`, `member_email`, `member_is_admin`, `member_date`) VALUES
(2, 'Jessica', 'Milkybar12022', 'Milkybar12022', 'graham23s@hotmail.com', 'yes', '2023-02-26 09:08:49');

-- --------------------------------------------------------

--
-- Table structure for table `newsletters`
--

CREATE TABLE `newsletters` (
  `newsletter_id` bigint(11) NOT NULL,
  `newsletter_email` varchar(150) NOT NULL,
  `newsletter_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options`
--

(1, 'site_admin_email', 'admin@demo.com', 'Specifies the primary administrative email for the site.', '2022-09-20 20:54:07'),
(2, 'ip_edit', '123.45.67.89', 'Specifies the IP addresses (separated by "|") authorized to edit posts.', '2023-02-23 08:21:55'),
(3, 'homepage_title', 'Demo Title', 'Defines the title tag value for the homepage.', '2021-05-16 15:47:56'),
(4, 'homepage_description', 'Demo Description', 'Sets the meta description tag for the homepage.', '2021-05-16 17:38:44'),
(5, 'homepage_about', 'About our Demo Site', 'Defines the "About" section content for the homepage.', '2021-10-23 20:55:27'),
(6, 'homepage_pagination', '5', 'Sets the number of articles displayed on the homepage.', '2021-10-18 21:55:15'),
(7, 'homepage_show_categories', '0', 'Controls the display of the categories section on the homepage (1=show, 0=hide).', '2021-10-23 20:40:35'),
(8, 'homepage_hide_login_link', '1', 'Controls the display of the admin login link on the homepage footer (1=hide, 0=show).', '2021-10-29 20:22:40'),
(9, 'homepage_introduction_header', 'Demo Header', 'Sets the header for the homepage introduction card.', '2023-01-29 12:05:06'),
(10, 'homepage_introduction_header_text', 'Demo Intro Text', 'Defines the content for the homepage introduction card.', '2023-01-29 12:20:16'),
(11, 'sidebar_cta_1_header', 'CTA1', 'Sets the header for the first sidebar Call To Action (CTA) section.', '2023-01-29 10:59:06'),
(12, 'sidebar_cta_1_text', 'CTA1 Text', 'Defines the content for the first sidebar CTA section.', '2023-01-29 11:17:34'),
(13, 'sidebar_cta_2_header', 'CTA2', 'Sets the header for the second sidebar CTA section.', '2023-01-29 11:29:50'),
(14, 'sidebar_cta_2_text', 'CTA2 Text', 'Defines the content for the second sidebar CTA section.', '2023-01-29 11:31:13'),
(15, 'hide_all_sidebars', '0', 'Controls the display of sidebars on the site (1=hide, 0=show).', '2023-06-27 11:15:10'),
(16, 'footer_amazon_disclosure_text', 'Demo Amazon Disclosure', 'Sets the Amazon Associates disclosure text for the site footer.', '2021-10-31 09:00:19'),
(17, 'footer_twitter', 'https://twitter.com/DemoTwitter', 'Sets the Twitter link for the site footer.', '2021-10-31 09:04:15'),
(18, 'footer_meta', 'https://www.facebook.com/DemoFacebook/', 'Sets the Facebook link for the site footer.', '2021-10-31 09:06:06'),
(19, 'footer_instagram', 'https://www.instagram.com/DemoInstagram/', 'Sets the Instagram link for the site footer.', '2021-10-31 09:23:11'),
(20, 'bottom_link_1', 'https://demo.com/', 'Specifies an additional link to display below the footer.', '2023-02-28 19:09:35'),
(21, 'google_adsense', 'Demo Adsense Code', 'Sets the Google AdSense code for the <head> tags.', '2023-01-19 10:58:58'),
(22, 'google_analytics_property_id', 'G-DemoPropertyId', 'Sets the Google Analytics property ID.', '2023-01-21 21:46:26'),
(23, 'ads_post_top', 'Demo Ad Code', 'Sets the ad code to be displayed at the top of each post page.', '2022-10-09 15:24:42'),
(24, 'recaptcha2_site_key', '6DemoSiteKey', 'Sets the site key for ReCaptcha2.', '2023-03-12 14:35:56'),
(25, 'recaptcha2_secret_key', '6DemoSecretKey', 'Sets the secret key for ReCaptcha2.', '2023-03-12 14:36:32'),
(26, 'about_us_header', 'About Us', 'Sets the header for the "About Us" section.', '2023-02-25 21:02:18'),
(27, 'about_us_text', 'Demo About Us Text', 'Defines the content for the "About Us" section.', '2023-02-25 21:03:41'),
(28, 'category_style_icon', 'demo-icon', 'Sets a custom icon for each category name.', '2022-12-18 09:59:54'),
(29, 'twitter_username', 'DemoTwitterUser', 'Sets the Twitter username for the Twitter cards feature.', '2023-02-09 21:38:34');


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `post_image` longtext NOT NULL,
  `post_image_alt_text` varchar(100) NOT NULL,
  `post_status` enum('published','draft','archived') NOT NULL,
  `post_date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_views` bigint(20) NOT NULL DEFAULT '0',
  `post_sticky` enum('0','1') NOT NULL,
  `post_source_url` longtext NOT NULL,
  `post_affiliate_url` longtext NOT NULL,
  `post_will_show_ads` enum('no','yes') NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shorteners`
--

CREATE TABLE `shorteners` (
  `shortener_id` bigint(20) NOT NULL,
  `shortener_short` varchar(50) NOT NULL,
  `shortener_original_url` longtext NOT NULL,
  `shortener_clicks_count` int(11) NOT NULL,
  `shortener_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `clicks`
--
ALTER TABLE `clicks`
  ADD PRIMARY KEY (`click_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `ips`
--
ALTER TABLE `ips`
  ADD PRIMARY KEY (`ip_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `newsletters`
--
ALTER TABLE `newsletters`
  ADD PRIMARY KEY (`newsletter_id`);

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
-- Indexes for table `shorteners`
--
ALTER TABLE `shorteners`
  ADD PRIMARY KEY (`shortener_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `clicks`
  MODIFY `click_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `images`
  MODIFY `image_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `ips`
  MODIFY `ip_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `members`
  MODIFY `member_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `newsletters`
  MODIFY `newsletter_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `options`
  MODIFY `option_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `pages`
  MODIFY `page_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `posts`
  MODIFY `post_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `shorteners`
  MODIFY `shortener_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;