-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 16, 2018 at 04:47 PM
-- Server version: 10.2.14-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xpress_health`
--
CREATE DATABASE IF NOT EXISTS `xpress_health` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `xpress_health`;

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `acc_type_id` int(11) UNSIGNED NOT NULL,
  `acc_type_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int(11) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(13) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_surname` varchar(255) NOT NULL,
  `client_address` varchar(255) NOT NULL,
  `client_postalcode` varchar(4) NOT NULL,
  `client_tel_home` varchar(20) NOT NULL,
  `client_tel_work` varchar(20) NOT NULL,
  `client_tel_cell` varchar(20) NOT NULL,
  `client_email` varchar(50) NOT NULL,
  `ref_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `client_references`
--

CREATE TABLE `client_references` (
  `ref_id` int(11) UNSIGNED NOT NULL,
  `ref_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `inv_num` varchar(20) NOT NULL,
  `inv_date` datetime NOT NULL,
  `client_id` varchar(13) DEFAULT NULL,
  `consultation` decimal(13,2) NOT NULL,
  `total_supplement` decimal(13,2) NOT NULL,
  `grand_total` decimal(13,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_lines`
--

CREATE TABLE `invoice_lines` (
  `line_id` int(11) UNSIGNED NOT NULL,
  `inv_num` varchar(20) DEFAULT NULL,
  `supplement_id` varchar(50) DEFAULT NULL,
  `price_charged` decimal(13,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(13,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `supplements`
--

CREATE TABLE `supplements` (
  `supplement_id` varchar(50) NOT NULL,
  `description_id` int(11) UNSIGNED DEFAULT NULL,
  `cost_excl` decimal(13,2) NOT NULL,
  `cost_incl` decimal(13,2) NOT NULL,
  `perc_inc` decimal(13,2) NOT NULL,
  `cost_client` decimal(13,2) NOT NULL,
  `supplier_id` int(11) UNSIGNED DEFAULT NULL,
  `min_levels` int(11) NOT NULL,
  `stock_levels` int(11) NOT NULL,
  `nappi_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `supplement_descriptions`
--

CREATE TABLE `supplement_descriptions` (
  `description_id` int(11) UNSIGNED NOT NULL,
  `supplement_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `bank_id` int(11) UNSIGNED DEFAULT NULL,
  `acc_type_id` int(11) UNSIGNED DEFAULT NULL,
  `supplier_branch_code` varchar(15) NOT NULL,
  `supplier_accnum` varchar(50) NOT NULL,
  `supplier_person` varchar(255) NOT NULL,
  `supplier_tel` varchar(20) NOT NULL,
  `supplier_tel_cell` varchar(20) NOT NULL,
  `supplier_fax` varchar(20) NOT NULL,
  `supplier_email` varchar(50) NOT NULL,
  `supplier_comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`acc_type_id`),
  ADD KEY `acc_type_id` (`acc_type_id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `ref_id` (`ref_id`);

--
-- Indexes for table `client_references`
--
ALTER TABLE `client_references`
  ADD PRIMARY KEY (`ref_id`),
  ADD KEY `ref_id` (`ref_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`inv_num`),
  ADD KEY `inv_num` (`inv_num`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD PRIMARY KEY (`line_id`),
  ADD KEY `line_id` (`line_id`),
  ADD KEY `inv_num` (`inv_num`),
  ADD KEY `supplement_id` (`supplement_id`);

--
-- Indexes for table `supplements`
--
ALTER TABLE `supplements`
  ADD PRIMARY KEY (`supplement_id`),
  ADD KEY `supplement_id` (`supplement_id`),
  ADD KEY `description_id` (`description_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplement_descriptions`
--
ALTER TABLE `supplement_descriptions`
  ADD PRIMARY KEY (`description_id`),
  ADD KEY `description_id` (`description_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `acc_type_id` (`acc_type_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `acc_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `bank_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_references`
--
ALTER TABLE `client_references`
  MODIFY `ref_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  MODIFY `line_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplement_descriptions`
--
ALTER TABLE `supplement_descriptions`
  MODIFY `description_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `ref_id_const` FOREIGN KEY (`ref_id`) REFERENCES `client_references` (`ref_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `client_id_const` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD CONSTRAINT `inv_num_const` FOREIGN KEY (`inv_num`) REFERENCES `invoices` (`inv_num`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `supplement_id_const` FOREIGN KEY (`supplement_id`) REFERENCES `supplements` (`supplement_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `supplements`
--
ALTER TABLE `supplements`
  ADD CONSTRAINT `description_id_const` FOREIGN KEY (`description_id`) REFERENCES `supplement_descriptions` (`description_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `supplier_id_const` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `acc_type_const` FOREIGN KEY (`acc_type_id`) REFERENCES `account_types` (`acc_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `bank_const` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
