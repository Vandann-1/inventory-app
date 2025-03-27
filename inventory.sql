-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 12:32 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `authtoken_token`
--

CREATE TABLE `authtoken_token` (
  `key` varchar(40) NOT NULL,
  `created` datetime(6) NOT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_group`
--

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_group_permissions`
--

CREATE TABLE `auth_group_permissions` (
  `id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_permission`
--

CREATE TABLE `auth_permission` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  `codename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_permission`
--

INSERT INTO `auth_permission` (`id`, `name`, `content_type_id`, `codename`) VALUES
(1, 'Can add log entry', 1, 'add_logentry'),
(2, 'Can change log entry', 1, 'change_logentry'),
(3, 'Can delete log entry', 1, 'delete_logentry'),
(4, 'Can view log entry', 1, 'view_logentry'),
(5, 'Can add permission', 2, 'add_permission'),
(6, 'Can change permission', 2, 'change_permission'),
(7, 'Can delete permission', 2, 'delete_permission'),
(8, 'Can view permission', 2, 'view_permission'),
(9, 'Can add group', 3, 'add_group'),
(10, 'Can change group', 3, 'change_group'),
(11, 'Can delete group', 3, 'delete_group'),
(12, 'Can view group', 3, 'view_group'),
(13, 'Can add content type', 4, 'add_contenttype'),
(14, 'Can change content type', 4, 'change_contenttype'),
(15, 'Can delete content type', 4, 'delete_contenttype'),
(16, 'Can view content type', 4, 'view_contenttype'),
(17, 'Can add session', 5, 'add_session'),
(18, 'Can change session', 5, 'change_session'),
(19, 'Can delete session', 5, 'delete_session'),
(20, 'Can view session', 5, 'view_session'),
(21, 'Can add Token', 6, 'add_token'),
(22, 'Can change Token', 6, 'change_token'),
(23, 'Can delete Token', 6, 'delete_token'),
(24, 'Can view Token', 6, 'view_token'),
(25, 'Can add Token', 7, 'add_tokenproxy'),
(26, 'Can change Token', 7, 'change_tokenproxy'),
(27, 'Can delete Token', 7, 'delete_tokenproxy'),
(28, 'Can view Token', 7, 'view_tokenproxy'),
(29, 'Can add categories', 8, 'add_categories'),
(30, 'Can change categories', 8, 'change_categories'),
(31, 'Can delete categories', 8, 'delete_categories'),
(32, 'Can view categories', 8, 'view_categories'),
(33, 'Can add user', 9, 'add_customuser'),
(34, 'Can change user', 9, 'change_customuser'),
(35, 'Can delete user', 9, 'delete_customuser'),
(36, 'Can view user', 9, 'view_customuser');

-- --------------------------------------------------------

--
-- Table structure for table `django_admin_log`
--

CREATE TABLE `django_admin_log` (
  `id` int(11) NOT NULL,
  `action_time` datetime(6) NOT NULL,
  `object_id` longtext DEFAULT NULL,
  `object_repr` varchar(200) NOT NULL,
  `action_flag` smallint(5) UNSIGNED NOT NULL CHECK (`action_flag` >= 0),
  `change_message` longtext NOT NULL,
  `content_type_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `django_content_type`
--

CREATE TABLE `django_content_type` (
  `id` int(11) NOT NULL,
  `app_label` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `django_content_type`
--

INSERT INTO `django_content_type` (`id`, `app_label`, `model`) VALUES
(1, 'admin', 'logentry'),
(3, 'auth', 'group'),
(2, 'auth', 'permission'),
(6, 'authtoken', 'token'),
(7, 'authtoken', 'tokenproxy'),
(4, 'contenttypes', 'contenttype'),
(8, 'invapp', 'categories'),
(9, 'invapp', 'customuser'),
(5, 'sessions', 'session');

-- --------------------------------------------------------

--
-- Table structure for table `django_migrations`
--

CREATE TABLE `django_migrations` (
  `id` bigint(20) NOT NULL,
  `app` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `applied` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `django_migrations`
--

INSERT INTO `django_migrations` (`id`, `app`, `name`, `applied`) VALUES
(1, 'contenttypes', '0001_initial', '2025-03-26 09:45:48.010001'),
(2, 'contenttypes', '0002_remove_content_type_name', '2025-03-26 09:45:48.061758'),
(3, 'auth', '0001_initial', '2025-03-26 09:45:48.263175'),
(4, 'auth', '0002_alter_permission_name_max_length', '2025-03-26 09:45:48.312999'),
(5, 'auth', '0003_alter_user_email_max_length', '2025-03-26 09:45:48.335344'),
(6, 'auth', '0004_alter_user_username_opts', '2025-03-26 09:45:48.351031'),
(7, 'auth', '0005_alter_user_last_login_null', '2025-03-26 09:45:48.367715'),
(8, 'auth', '0006_require_contenttypes_0002', '2025-03-26 09:45:48.367715'),
(9, 'auth', '0007_alter_validators_add_error_messages', '2025-03-26 09:45:48.381712'),
(10, 'auth', '0008_alter_user_username_max_length', '2025-03-26 09:45:48.397266'),
(11, 'auth', '0009_alter_user_last_name_max_length', '2025-03-26 09:45:48.414672'),
(12, 'auth', '0010_alter_group_name_max_length', '2025-03-26 09:45:48.441971'),
(13, 'auth', '0011_update_proxy_permissions', '2025-03-26 09:45:48.452601'),
(14, 'auth', '0012_alter_user_first_name_max_length', '2025-03-26 09:45:48.461973'),
(15, 'invapp', '0001_initial', '2025-03-26 09:45:48.700089'),
(16, 'admin', '0001_initial', '2025-03-26 09:45:48.793533'),
(17, 'admin', '0002_logentry_remove_auto_add', '2025-03-26 09:45:48.810056'),
(18, 'admin', '0003_logentry_add_action_flag_choices', '2025-03-26 09:45:48.831387'),
(19, 'authtoken', '0001_initial', '2025-03-26 09:45:48.886415'),
(20, 'authtoken', '0002_auto_20160226_1747', '2025-03-26 09:45:48.926454'),
(21, 'authtoken', '0003_tokenproxy', '2025-03-26 09:45:48.926454'),
(22, 'authtoken', '0004_alter_tokenproxy_options', '2025-03-26 09:45:48.939576'),
(23, 'invapp', '0002_alter_customuser_mobile_no', '2025-03-26 09:45:49.025160'),
(24, 'sessions', '0001_initial', '2025-03-26 09:45:49.059733'),
(25, 'invapp', '0003_customuser_user_code', '2025-03-26 13:17:21.506825'),
(26, 'invapp', '0004_customuser_role', '2025-03-27 08:14:04.150164');

-- --------------------------------------------------------

--
-- Table structure for table `django_session`
--

CREATE TABLE `django_session` (
  `session_key` varchar(40) NOT NULL,
  `session_data` longtext NOT NULL,
  `expire_date` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invapp_categories`
--

CREATE TABLE `invapp_categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` longtext DEFAULT NULL,
  `created_at` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invapp_categories`
--

INSERT INTO `invapp_categories` (`id`, `name`, `desc`, `created_at`) VALUES
(1, 'Phone', 'Smart Phone', '2025-03-26 13:11:21.822759');

-- --------------------------------------------------------

--
-- Table structure for table `invapp_customuser`
--

CREATE TABLE `invapp_customuser` (
  `id` bigint(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `last_login` datetime(6) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `username` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(254) NOT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `date_joined` datetime(6) NOT NULL,
  `user_code` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invapp_customuser`
--

INSERT INTO `invapp_customuser` (`id`, `password`, `last_login`, `is_superuser`, `username`, `first_name`, `last_name`, `email`, `mobile_no`, `role`, `is_staff`, `is_active`, `date_joined`, `user_code`) VALUES
(1, 'pbkdf2_sha256$870000$ljPcJSC3hlc8pgevEKFx1X$MMIE0Itq3EtAOZxMdVHU9+ySo90Rqw7tlc7GGFE3JpA=', NULL, 0, 'Jevin Kalathiya', 'Jevin', 'Kalathiya', 'kalathiyajevin77@gmail.com', '8426555555', 'Admin', 0, 1, '2025-03-27 10:02:44.906583', 'vq9CKAB4mJi5Yx9JwUONVFqSEJPdnCh59krRiAyx0tdwGZHg459BLruxJcCbfk6vNL3HFz6Z0Ae5BFoeaC8CvBEJsJn0JVQSS8SjKVrEFEsI6UPLkL4IT4eKznPZXew6OMf3ZWX5QyC2Dkk7fFIa84LBnaRcciUuTHSuRUXOTA1vqpFSlZNt2KDhbMA2gxR4oxrTSgzWC1VUmeU4a5FC7LzELPhhEXEPVerGABi0kVzQX35rntIyUSWF4JPXt5N4LZ7TXbeFlg0e4MOmQRvq3ZYfKOga3fRaMb4Y445fCmv1n0As6PxL9dOylUmG1Zv4IGtj3SNZ4spB5QuJNcXCjjX1OjUlL4WGMWSfJ2oTj1r3Ab7yDM6FIoy3WhqRxtGKAzlRPFyr1UpazEEuN65FnkuWsr1v97mEGTAVIHQDRZRYMSgEiC79iZXMHzvyne443saJaBNhR3eoV3ZPzuI6L6ShLUCwhvODFl3pKZPWKtvEbnzOKLIa'),
(2, 'pbkdf2_sha256$870000$aTaFgSvXv6P4xBzmwr7dZq$BQal9x9VzdSU48w9AUOUuyxiqc8HAyF+oSA8o3vybQQ=', NULL, 0, 'Vandan Darji', 'Vandan', 'Darji', 'vandan13@gmail.com', '8444444444', 'User', 0, 1, '2025-03-27 10:03:07.671167', 'UskGfVFv6f7JLLDdH89XTjKQanFSLDxbFi7od4f1eFMgStRIkzJpWHomwl0M2hetNZbJKJPtWaU6ZUX4tzW1okkH6ak6Z5lzINt61MUO0jORMBCIrT8OvusEIVIFryYs0pzsA7m1LFIHCXjDe9jVo5zWnQwpNelaAIOdQahsfD90ehzq640P9hjYr63rWBbFMArlm0ZQc45adEI2cJlqaqNbWpzGgM5BwOcUuKOs25ymGuikdaA7J38WM42TlamIkBD5rsdVlL8194gfZi9mfFOiDUluAB9Nu0rSCw1D5N4kIy03zFjIfX5tf0N61e6c7Tlx3vgE8Pb67N8hSUUvLPU0XJ0jV2I4Yak4PFkbaMgCiXrBHZxNPK4w5crFBSLvOkJOGPrkFFTaEbDi93gVQok058I8YC7MoJKH0ABgEs2kzLQQeReHQXxwmTdwSpmqRzupuDMiheM8vp7G4CuLTx5SL7KiZqz9yOLUnB0wjKrwpzrXLbB1');

-- --------------------------------------------------------

--
-- Table structure for table `invapp_customuser_groups`
--

CREATE TABLE `invapp_customuser_groups` (
  `id` bigint(20) NOT NULL,
  `customuser_id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invapp_customuser_user_permissions`
--

CREATE TABLE `invapp_customuser_user_permissions` (
  `id` bigint(20) NOT NULL,
  `customuser_id` bigint(20) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authtoken_token`
--
ALTER TABLE `authtoken_token`
  ADD PRIMARY KEY (`key`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `auth_group`
--
ALTER TABLE `auth_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_group_permissions_group_id_permission_id_0cd325b0_uniq` (`group_id`,`permission_id`),
  ADD KEY `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` (`permission_id`);

--
-- Indexes for table `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_permission_content_type_id_codename_01ab375a_uniq` (`content_type_id`,`codename`);

--
-- Indexes for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `django_admin_log_content_type_id_c4bce8eb_fk_django_co` (`content_type_id`),
  ADD KEY `django_admin_log_user_id_c564eba6_fk_invapp_customuser_id` (`user_id`);

--
-- Indexes for table `django_content_type`
--
ALTER TABLE `django_content_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `django_content_type_app_label_model_76bd3d3b_uniq` (`app_label`,`model`);

--
-- Indexes for table `django_migrations`
--
ALTER TABLE `django_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `django_session`
--
ALTER TABLE `django_session`
  ADD PRIMARY KEY (`session_key`),
  ADD KEY `django_session_expire_date_a5c62663` (`expire_date`);

--
-- Indexes for table `invapp_categories`
--
ALTER TABLE `invapp_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `invapp_customuser`
--
ALTER TABLE `invapp_customuser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_code` (`user_code`);

--
-- Indexes for table `invapp_customuser_groups`
--
ALTER TABLE `invapp_customuser_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invapp_customuser_groups_customuser_id_group_id_3e180920_uniq` (`customuser_id`,`group_id`),
  ADD KEY `invapp_customuser_groups_group_id_3f72a2d2_fk_auth_group_id` (`group_id`);

--
-- Indexes for table `invapp_customuser_user_permissions`
--
ALTER TABLE `invapp_customuser_user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invapp_customuser_user_p_customuser_id_permission_39b0ad77_uniq` (`customuser_id`,`permission_id`),
  ADD KEY `invapp_customuser_us_permission_id_eb597b93_fk_auth_perm` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_group`
--
ALTER TABLE `auth_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_permission`
--
ALTER TABLE `auth_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `django_content_type`
--
ALTER TABLE `django_content_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `django_migrations`
--
ALTER TABLE `django_migrations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `invapp_categories`
--
ALTER TABLE `invapp_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invapp_customuser`
--
ALTER TABLE `invapp_customuser`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invapp_customuser_groups`
--
ALTER TABLE `invapp_customuser_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invapp_customuser_user_permissions`
--
ALTER TABLE `invapp_customuser_user_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `authtoken_token`
--
ALTER TABLE `authtoken_token`
  ADD CONSTRAINT `authtoken_token_user_id_35299eff_fk_invapp_customuser_id` FOREIGN KEY (`user_id`) REFERENCES `invapp_customuser` (`id`);

--
-- Constraints for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD CONSTRAINT `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  ADD CONSTRAINT `auth_group_permissions_group_id_b120cbf9_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`);

--
-- Constraints for table `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD CONSTRAINT `auth_permission_content_type_id_2f476e4b_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`);

--
-- Constraints for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  ADD CONSTRAINT `django_admin_log_user_id_c564eba6_fk_invapp_customuser_id` FOREIGN KEY (`user_id`) REFERENCES `invapp_customuser` (`id`);

--
-- Constraints for table `invapp_customuser_groups`
--
ALTER TABLE `invapp_customuser_groups`
  ADD CONSTRAINT `invapp_customuser_gr_customuser_id_07f26544_fk_invapp_cu` FOREIGN KEY (`customuser_id`) REFERENCES `invapp_customuser` (`id`),
  ADD CONSTRAINT `invapp_customuser_groups_group_id_3f72a2d2_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`);

--
-- Constraints for table `invapp_customuser_user_permissions`
--
ALTER TABLE `invapp_customuser_user_permissions`
  ADD CONSTRAINT `invapp_customuser_us_customuser_id_c5f6d051_fk_invapp_cu` FOREIGN KEY (`customuser_id`) REFERENCES `invapp_customuser` (`id`),
  ADD CONSTRAINT `invapp_customuser_us_permission_id_eb597b93_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
