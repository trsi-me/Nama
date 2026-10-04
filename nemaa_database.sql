-- ============================================
-- قاعدة بيانات منصة نماء - منصة العلوم الشرعية
-- ============================================
-- تاريخ الإنشاء: 2025
-- الترميز: UTF-8
-- ============================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `nemaa_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `nemaa_db`;

-- ============================================
-- جدول تسجيلات الدورات
-- ============================================
CREATE TABLE IF NOT EXISTS `course_registrations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'اسم المسجل',
  `email` VARCHAR(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `phone` VARCHAR(20) NOT NULL COMMENT 'رقم الجوال',
  `type` VARCHAR(50) NOT NULL DEFAULT 'دورة' COMMENT 'نوع التسجيل',
  `title` VARCHAR(255) NOT NULL COMMENT 'عنوان الدورة',
  `status` VARCHAR(50) NOT NULL DEFAULT 'ينتظر' COMMENT 'حالة التسجيل (ينتظر، مقبول، مرفوض)',
  `notes` TEXT NULL COMMENT 'ملاحظات إضافية',
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ التسجيل',
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_status` (`status`),
  INDEX `idx_registration_date` (`registration_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='تسجيلات الدورات';

-- ============================================
-- جدول تسجيلات المحاضرات
-- ============================================
CREATE TABLE IF NOT EXISTS `lecture_registrations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'اسم المسجل',
  `email` VARCHAR(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `phone` VARCHAR(20) NOT NULL COMMENT 'رقم الجوال',
  `type` VARCHAR(50) NOT NULL DEFAULT 'محاضرة' COMMENT 'نوع التسجيل',
  `title` VARCHAR(255) NOT NULL COMMENT 'عنوان المحاضرة',
  `status` VARCHAR(50) NOT NULL DEFAULT 'ينتظر' COMMENT 'حالة التسجيل (ينتظر، مقبول، مرفوض)',
  `notes` TEXT NULL COMMENT 'ملاحظات إضافية',
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ التسجيل',
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_status` (`status`),
  INDEX `idx_registration_date` (`registration_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='تسجيلات المحاضرات';

-- ============================================
-- جدول تسجيلات الندوات
-- ============================================
CREATE TABLE IF NOT EXISTS `seminar_registrations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'اسم المسجل',
  `email` VARCHAR(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `phone` VARCHAR(20) NOT NULL COMMENT 'رقم الجوال',
  `type` VARCHAR(50) NOT NULL DEFAULT 'ندوة' COMMENT 'نوع التسجيل',
  `title` VARCHAR(255) NOT NULL COMMENT 'عنوان الندوة',
  `status` VARCHAR(50) NOT NULL DEFAULT 'ينتظر' COMMENT 'حالة التسجيل (ينتظر، مقبول، مرفوض)',
  `notes` TEXT NULL COMMENT 'ملاحظات إضافية',
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ التسجيل',
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_status` (`status`),
  INDEX `idx_registration_date` (`registration_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='تسجيلات الندوات';

-- ============================================
-- جدول رسائل التواصل
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'اسم المرسل',
  `email` VARCHAR(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `phone` VARCHAR(20) NOT NULL COMMENT 'رقم الجوال',
  `subject` VARCHAR(255) NOT NULL COMMENT 'موضوع الرسالة',
  `message` TEXT NOT NULL COMMENT 'نص الرسالة',
  `status` VARCHAR(50) NOT NULL DEFAULT 'جديد' COMMENT 'حالة الرسالة (جديد، مقروء، تم الرد)',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ الإرسال',
  `read_at` DATETIME NULL COMMENT 'تاريخ القراءة',
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='رسائل التواصل';

-- ============================================
-- جدول الاشتراكات في النشرة البريدية
-- ============================================
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `name` VARCHAR(255) NULL COMMENT 'اسم المشترك',
  `status` VARCHAR(50) NOT NULL DEFAULT 'نشط' COMMENT 'حالة الاشتراك (نشط، ملغي)',
  `subscribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ الاشتراك',
  `unsubscribed_at` DATETIME NULL COMMENT 'تاريخ إلغاء الاشتراك',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_subscribed_at` (`subscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='الاشتراكات في النشرة البريدية';

-- ============================================
-- بيانات تجريبية (اختياري)
-- ============================================

-- إدراج تسجيل تجريبي في دورة
INSERT INTO `course_registrations` (`name`, `email`, `phone`, `type`, `title`, `status`, `notes`, `registration_date`) 
VALUES 
('أحمد محمد', 'ahmed@example.com', '0501234567', 'دورة', 'دورة الفقه الميسر', 'ينتظر', 'يريد التسجيل في الدورة', NOW()),
('فاطمة علي', 'fatima@example.com', '0507654321', 'دورة', 'دورة تعلم التجويد', 'مقبول', 'تم قبول التسجيل', NOW());

-- إدراج تسجيل تجريبي في محاضرة
INSERT INTO `lecture_registrations` (`name`, `email`, `phone`, `type`, `title`, `status`, `notes`, `registration_date`) 
VALUES 
('خالد أحمد', 'khalid@example.com', '0501111111', 'محاضرة', 'أحكام الزكاة في الإسلام', 'ينتظر', 'يريد حضور المحاضرة', NOW());

-- إدراج تسجيل تجريبي في ندوة
INSERT INTO `seminar_registrations` (`name`, `email`, `phone`, `type`, `title`, `status`, `notes`, `registration_date`) 
VALUES 
('سارة محمد', 'sara@example.com', '0502222222', 'ندوة', 'الأسرة في ظل التربية الإسلامية', 'ينتظر', 'مهتمة بالندوة', NOW());

-- إدراج رسالة تواصل تجريبية
INSERT INTO `contact_messages` (`name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) 
VALUES 
('محمد عبدالله', 'mohammed@example.com', '0503333333', 'استفسار عام', 'أريد الاستفسار عن مواعيد الدورات القادمة', 'جديد', NOW());

-- إدراج اشتراك تجريبي في النشرة البريدية
INSERT INTO `newsletter_subscriptions` (`email`, `name`, `status`, `subscribed_at`) 
VALUES 
('subscriber@example.com', 'مشترك تجريبي', 'نشط', NOW());

-- ============================================
-- نهاية الملف
-- ============================================
-- ملاحظات:
-- 1. جميع الجداول تستخدم UTF-8 (utf8mb4) لدعم اللغة العربية بشكل كامل
-- 2. تم إضافة فهارس (Indexes) على الحقول المستخدمة في البحث لتحسين الأداء
-- 3. جميع الجداول تستخدم InnoDB Engine لدعم المعاملات (Transactions)
-- 4. تم إضافة تعليقات (Comments) على جميع الجداول والحقول لتوضيح الغرض منها
-- 5. يمكنك تعديل إعدادات قاعدة البيانات (DB_HOST, DB_USER, DB_PASS) في ملف Config.php
-- ============================================
