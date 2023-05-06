-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql200.byetcluster.com
-- Generation Time: May 05, 2023 at 01:50 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `epiz_33199223_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `img`, `link`) VALUES
(1, '1.1.png', 'search.php?brand=Acer'),
(2, '1.2.png', 'search.php?brand=Apple'),
(3, '1.3.png', 'search.php?brand=Hp');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `name`) VALUES
(8, 'Acer'),
(3, 'Apple'),
(9, 'ASUS'),
(5, 'Dell'),
(7, 'Hp'),
(4, 'Huawei'),
(10, 'INFINIX'),
(6, 'Lenovo');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_user` varchar(255) NOT NULL,
  `id_product` varchar(255) NOT NULL,
  `com` varchar(255) NOT NULL,
  `time_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `id_user`, `id_product`, `com`, `time_at`, `status`) VALUES
(28, '584406', '796461', 'Good Laptop', '2023-05-05 17:23:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `graphics`
--

CREATE TABLE `graphics` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `graphics`
--

INSERT INTO `graphics` (`id`, `name`) VALUES
(2, 'AMD Radeon RX 7900 XTX'),
(5, 'NVIDIA GeForce RTX 4070 Ti\r\n'),
(8, 'Other'),
(9, 'RTX 3050'),
(7, 'RTX 3090 Ti'),
(3, 'RTX 4080'),
(1, 'RTX 4090'),
(6, 'RX 6950 XT'),
(4, 'RX 7900 XT');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `time_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `username`, `message`, `time_at`) VALUES
(7, '514654', 'Add New Product', '2023-05-05 16:20:00'),
(8, '514654', 'Edit in Product', '2023-05-05 16:20:28'),
(9, '514654', 'Add New Product', '2023-05-05 16:24:40'),
(10, '514654', 'Add New Product', '2023-05-05 16:27:09'),
(11, '514654', 'Add New Product', '2023-05-05 16:29:31'),
(12, '514654', 'Add New Product', '2023-05-05 16:32:45'),
(13, '514654', 'Edit in Product', '2023-05-05 16:33:09'),
(14, '514654', 'Edit in Product', '2023-05-05 16:33:22'),
(15, '514654', 'Add New Product', '2023-05-05 16:37:38'),
(16, '514654', 'Add New Product', '2023-05-05 16:40:42'),
(17, '514654', 'Add New Product', '2023-05-05 16:43:11'),
(18, '514654', 'Add New Product', '2023-05-05 16:45:53'),
(19, '514654', 'Edit in Product', '2023-05-05 16:46:38'),
(20, '514654', 'Edit in Product', '2023-05-05 16:47:02'),
(21, '514654', 'Edit in Product', '2023-05-05 17:14:32'),
(22, '514654', 'Someone Add Comment In product', '2023-05-05 17:23:40');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `id_product` varchar(255) NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `status` enum('0','1','2','3','4') NOT NULL,
  `time_ordering` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_expected` timestamp NULL DEFAULT NULL,
  `time_arrived` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `id_product`, `order_number`, `price`, `count`, `username`, `status`, `time_ordering`, `time_expected`, `time_arrived`) VALUES
(42, '796461', '9324', 1000, 1, '584406', '4', '2023-05-05 17:28:13', '2023-05-07 04:00:00', NULL),
(43, '866086', '9324', 334, 1, '584406', '4', '2023-05-05 17:28:13', '2023-05-07 04:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `processor`
--

CREATE TABLE `processor` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `processor`
--

INSERT INTO `processor` (`id`, `name`) VALUES
(6, 'i10'),
(5, 'i11'),
(1, 'i3'),
(2, 'i5'),
(3, 'i7'),
(4, 'i9'),
(7, 'M1'),
(8, 'M2'),
(9, 'Other'),
(10, 'Ryzen 7');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `random_product` varchar(255) NOT NULL,
  `username_add` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `img_bg` varchar(255) NOT NULL,
  `imgs_product` varchar(255) NOT NULL,
  `ram` varchar(255) NOT NULL,
  `processor` varchar(255) NOT NULL,
  `graphics` varchar(255) NOT NULL,
  `storage` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `des` varchar(1000) NOT NULL,
  `price` int(11) NOT NULL,
  `time_add` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(1) NOT NULL DEFAULT 0,
  `count_allow` int(11) NOT NULL DEFAULT 1,
  `count_pay` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `random_product`, `username_add`, `brand`, `img_bg`, `imgs_product`, `ram`, `processor`, `graphics`, `storage`, `color`, `des`, `price`, `time_add`, `status`, `count_allow`, `count_pay`) VALUES
(16, 'أبل ماك بوك', '997665', '514654', 'Apple', '997665917.jpeg', '997665562.jpeg,997665592.jpeg,997665223.jpeg,', '8GB', 'M1', 'Other', '256SSD', 'رمادى', 'أبل ماك بوك شريحة Air 13 بوصة M1 مع وحدة معالجة مركزية 8-Core و 7-Core GPU و 8 جيجا بايت رام و 256 جيجا بايت اس اس دى - رمادى\r\n', 1400, '2023-05-05 16:20:00', 0, 10, 0),
(17, 'لينوفو ليجون 5', '143944', '514654', 'Lenovo', '143944454.jpeg', '14394430.jpeg,14394464.jpeg,143944992.jpeg,', '16GB', 'i5', 'Other', '1TB', 'أزرق', 'لينوفو ليجون 5 15ITH6H انتل كور i7-11800H ، 16 جيجا رام ، 1 تيرابايت اس اس دي - RTX 3070 ، 15.6 انش فل اتش دي - أزرق\r\n', 1733, '2023-05-05 16:24:40', 0, 100, 0),
(18, 'لينوفو ايديا باد 3', '866086', '514654', 'Lenovo', '866086668.jpeg', '866086721.jpeg,866086652.jpeg,866086146.jpeg,', '8GB', 'i10', 'Other', '1TB HDD', 'أزرق', 'لينوفو ايديا باد 3 15ITL6 82H801ALED انتل كور i3-1115G4 ، 4 جيجا رام ، 1 تيرابايت اتش دي دي ، 15.6 انش فل اتش دي - ازرق\r\n', 334, '2023-05-05 16:27:09', 0, 4, 1),
(19, 'لاب توب ديل G15', '337044', '514654', 'Dell', '337044109.jpeg', '337044448.jpeg,337044672.jpeg,337044315.jpeg,', '16GB', 'i10', 'RTX 3090 Ti', '512SSD', 'رمادي', 'لاب توب ديل G15 5511 انتل كور i7-11800H ، 16 جيجا رام ، 512 جيجا اس اس دي ، نفيديا جي فورس ار تي اكس 3050 ، 15.6 انش - رمادي\r\n', 1133, '2023-05-05 16:29:31', 0, 7, 0),
(20, 'انفينيكس لابتوب X2', '83189', '514654', 'INFINIX', '83189445.jpeg', '83189279.jpeg,83189756.jpeg,', '16GB', 'i7', 'Other', '512HDD', 'رمادى', 'انفينيكس لابتوب X2 كور اي 7 ، 8 جيجا رام ، 512 جيجا اس اس دي ، 14 انش ، ويندوز 11 - رمادي\r\n', 533, '2023-05-05 16:32:45', 0, 5, 0),
(21, 'أسوس تاف جيمينج اف 15', '452670', '514654', 'ASUS', '45267092.jpeg', '452670913.jpeg,452670677.jpeg,452670104.jpeg,', '16GB', 'i10', 'AMD Radeon RX 7900 XTX', '512SSD', 'رمادى', 'أسوس تاف جيمينج اف 15 FA507RC-HN007W ايه ام دي رايزن R7 6800H ، 16 جيجا رام ، 512 جيجا اس اس دي ، انفيديا ار تي اكس 3050 ، 15.6 انش فل اتش دي ، ويندوز 11 - رمادي\r\n', 1116, '2023-05-05 16:37:38', 0, 1, 0),
(22, 'اتش بي 15s-fq5009ne', '187521', '514654', 'Hp', '187521922.jpeg', '187521452.jpeg,187521208.jpeg,187521148.jpeg,', '4GB', 'i3', 'AMD Radeon RX 7900 XTX', '512SSD', 'أسود', 'اتش بي 15s-fq5009ne انتل كور i3-1215U ، 4 جيجا رام ، 512 جيجا اس اس دي ، 15.6 انش اتش دي - اسود\r\n', 466, '2023-05-05 16:40:42', 0, 15, 0),
(23, ' اتش بي برو بوك 450 G9', '141727', '514654', 'Hp', '141727998.jpeg', '141727645.jpeg,141727706.jpeg,141727973.jpeg,', '8GB', 'i7', 'Other', '512HDD', 'فضى', 'اتش بي برو بوك 450 G9 انتل كور i7-1255U ، 8 جيجا رام ، 512 جيجا ، نفيديا جي فورس MX570 ، شاشة 15.6 انش اتش دي - فضي\r\n', 1000, '2023-05-05 16:43:11', 0, 1, 0),
(24, 'لينوفو ايديا باد جيمينج 3', '796461', '514654', 'Lenovo', '79646147.jpeg', '796461528.jpeg,796461723.jpeg,796461955.jpeg,796461965.jpeg,796461115.jpeg,796461366.jpeg,', '16GB', 'i7', 'Other', 'أسود', 'أسود', 'لينوفو ايديا باد جيمينج 3 15IMH05 انتل كور i7-10750H ، 16 جيجا رام ، 1 تيرابايت + 256 اس اس دي ، نفيديا جي فورس جي تي اكس 1650 تي اي ، 15.6 انش فل اتش دي 81Y4010BED - اسود\r\n', 1000, '2023-05-05 16:45:53', 0, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ram`
--

CREATE TABLE `ram` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ram`
--

INSERT INTO `ram` (`id`, `name`) VALUES
(4, '16GB'),
(1, '2GB'),
(5, '32GB'),
(2, '4GB'),
(6, '64GB'),
(3, '8GB');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `img_profile` varchar(255) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `create_at` timestamp NULL DEFAULT current_timestamp(),
  `active` int(11) NOT NULL DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `cart` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `img_profile`, `FullName`, `location`, `create_at`, `active`, `admin`, `status`, `cart`) VALUES
(1, '514654', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'admin@admin.com', '514654.jpeg', 'Admin', 'One Tow Three', '2023-04-07 15:32:33', 1, 2, 0, ',,'),
(17, '584406', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'tomtom22006@gmail.com', '584406.png', 'Thomas Emad', 'One Two Three', '2023-05-05 17:22:03', 1, 3, 0, 'Mackinley Hamner,4756670690085038,564');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `name_2` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_ibfk_1` (`id_product`),
  ADD KEY `comments_ibfk_2` (`id_user`);

--
-- Indexes for table `graphics`
--
ALTER TABLE `graphics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `processor`
--
ALTER TABLE `processor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_add` (`username_add`),
  ADD KEY `graphics` (`graphics`),
  ADD KEY `ram` (`ram`),
  ADD KEY `product_ibfk_3` (`processor`),
  ADD KEY `brand` (`brand`);

--
-- Indexes for table `ram`
--
ALTER TABLE `ram`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `graphics`
--
ALTER TABLE `graphics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `processor`
--
ALTER TABLE `processor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ram`
--
ALTER TABLE `ram`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`username_add`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
