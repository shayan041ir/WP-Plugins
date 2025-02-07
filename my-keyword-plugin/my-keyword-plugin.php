<?php
/**
 * Plugin Name: My Keyword Research Plugin
 * Plugin URI: https://example.com
 * Description: افزونه‌ای برای انجام Keyword Research با استفاده از API چت‌جی‌پی‌تی.
 * Version: 1.0.0
 * Author: shayan rezayi
 * License: GPL2
 */

// جلوگیری از دسترسی مستقیم
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// بارگذاری فایل‌های لازم
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-api-handler.php';

// افزودن اکشن‌های مربوط به Shortcode و تنظیمات
require_once plugin_dir_path( __FILE__ ) . 'public/form-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';
