-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 22 يوليو 2026 الساعة 15:27
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'أثاث مكتبي', NULL),
(2, 'إلكترونيات وأجهزة ذكية', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image_url`, `image`) VALUES
(3, 1, 'طاولة مكتبية مودرن', 'طاولة مكتب أنيقة مصنوعة من خشب هندسي وزان روماني عالي الجودة، مع قاعدة معدنية قوية. متوفرة بأربعة مقاسات. لونها خشبي طبيعي يضفي لمسة كلاسيكية على مكتبك. سهلة التنظيف بمسحة خفيفة فقط. تأتي مع ضمان لمدة سنتين ضد عيوب التصنيع.', 250.00, 10, '', 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=500&fit=crop'),
(4, 1, 'مصباح مكتب ذكي LED', 'مصباح مكتب LED من ايه دي كارت، مصباح مكتب ذكي مع عرض درجة الحرارة والتاريخ، مناسب للمكتب والعمل والقراءة على سطح المكتب وتعلم الطلاب', 45.00, 25, '1784703718_images (7).jpeg', 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&fit=crop'),
(5, 1, 'حامل شاشة هيدروليكي مزدوج', 'حامل هيدروليكي مزدوج يناسب الشاشات قياس 13 - 32 انش،4.4 - 14.3 باوند، قابل للتعديل باستخدام مشبك على شكل حرف C وحامل سطح المكتب', 75.00, 15, '1784703848_61EMq5fwA2L._AC_SX679_.jpg', 'https://m.media-amazon.com/images/I/61EMq5fwA2L._AC_SX679_.jpg'),
(7, 1, 'خزانة ملفات ثلاثية الأدراج', 'خزانة ملفات خشبية للمكتب مع قفل، درج ثلاثي الطبقات، خزانة تخزين منزلية متحركة، خزانة للدراسة، خزانة ملفات (E)', 180.00, 8, '1784704667_1784704052_31avZ8uJj0L._AC_.jpg', 'https://images.unsplash.com/photo-1595428774223-ef52624120d2?w=500&fit=crop'),
(9, 1, 'حاسوب محمول أبل ماك بوك', 'لابتوب حديث بسعة تخزين فائقة وأداء متميز للأعمال والبرمجة.', 1200.00, 10, NULL, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&fit=crop'),
(10, 1, 'ساعة ذكية أنيقة', 'ساعة ذكية لتتبع النشاط الرياضي ومعدل نبضات القلب ومقاومة للماء.', 199.00, 10, NULL, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&fit=crop'),
(13, 1, 'كاميرا تصوير احترافية', 'كاميرا بدقة عالية 4K لتصوير الفيديوهات والصور الاحترافية بسهولة.', 850.00, 8, NULL, 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&fit=crop'),
(15, 2, 'مكبر صوت بلوتوث محمول', 'سماعة سبيكر بصلابة عالية، مقاومة للمياه بصوت نقي وباس قوي.', 89.00, 20, NULL, 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=500&fit=crop'),
(16, 2, 'لوحة مفاتيح ميكانيكية مضيئة', 'كيبورد ميكانيكي بإضاءة RGB متعددة الألوان مريح جداً للكتابة والألعاب.', 65.00, 15, NULL, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&fit=crop'),
(17, 2, 'خاتم ذكي لتتبع الصحة والنشاط', 'خاتم تيتانيوم متطور يقيس نبضات القلب، جودة النوم، ومستويات التوتر بدقة فائقة على مدار الساعة.', 199.00, 12, NULL, 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500&fit=crop'),
(18, 2, 'نظارة ذكية للواقع المعزز AR', 'نظارة خفيفة الوزن تعرض التنبيهات، الترجمة الفورية، والخرائط مباشرة أمام عينيك بتقنية الواقع المعزز.', 450.00, 5, NULL, 'https://images.unsplash.com/photo-1592478411213-6153e4ebc07d?w=500&fit=crop'),
(20, 2, 'كوب قهوة ذكي بالتحكم الحراري', 'كوب يحافظ على قهوتك بدرجة الحرارة المثالية التي تختارها عبر تطبيق الهاتف لمدة تصل إلى 8 ساعات.', 85.00, 18, NULL, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&fit=crop'),
(22, 1, 'سماعات رأس لاسلكية محيطية', 'سماعات رأس عازلة للضوضاء بتصميم أنيق وصوت نقي جداً، مريحة للأذن ومناسبة للعمل والعمل المكتبي.', 85.00, 15, NULL, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&fit=crop'),
(25, 1, 'نظارة ذكية بسماعات مدمجة', 'نظارة أنيقة وعصرية مزودة بسماعات صوتية مخفية لاتصال بلوتوث سلس واستماع للموسيقى والمكالمات.', 95.00, 10, NULL, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&fit=crop'),
(26, 1, 'حقيبة تنظيم الكابلات والشواحن', 'حقيبة مقاومة للماء ومقسمة بشكل ذكي لترتيب الكابلات والشواحن أثناء السفر.', 22.00, 30, NULL, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&fit=crop'),
(28, 2, 'ساعة حائط مودرن صامتة', 'ساعة حائط بتصميم عصري وأرقام واضحة مع حركة عقارب صامتة تماماً.', 40.00, 15, NULL, 'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=600&fit=crop'),
(29, 1, 'حامل لابتوب ألومنيوم قابل للطي', 'حامل ألومنيوم خفيف الوزن وقابل للطي لرفع اللابتوب وتحسين زاوية الرؤية.', 32.00, 18, NULL, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&fit=crop'),
(30, 1, 'قاعدة تدفئة وتعقيم الأكواب الذكية', 'قاعدة مكتبية مبتكرة للتحكم بفرز حرارة المشروبات مع خاصية التعقيم بالأشعة فوق البنفسجية.', 48.00, 15, NULL, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&fit=crop'),
(31, 1, 'مكبر شاشة الهواتف الذكية ثلاثي الأبعاد', 'جهاز مكتبي خفيف يضخم شاشة الجوال بوضوح HD بدون كهرباء للحماية من إجهاد العين.', 29.00, 22, NULL, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&fit=crop');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2026-06-08 13:08:08'),
(2, 'sulafa', '123456', 'admin', '2026-07-21 07:15:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
