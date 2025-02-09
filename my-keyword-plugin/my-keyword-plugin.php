<?php
/**
 * Plugin Name: My Keyword Research Plugin
 * Description: افزونه‌ای برای انجام Keyword Research با استفاده از API چت‌جی‌پی‌تی.
 * Version: 1.0.0
 * Author: shayan rezayi
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// تعریف ثابت‌های مسیر افزونه
define( 'MY_KEYWORD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MY_KEYWORD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// بارگذاری فایل‌های مورد نیاز
require_once MY_KEYWORD_PLUGIN_PATH . 'includes/functions.php';
require_once MY_KEYWORD_PLUGIN_PATH . 'includes/class-api-handler.php';
require_once MY_KEYWORD_PLUGIN_PATH . 'admin/settings.php';
require_once MY_KEYWORD_PLUGIN_PATH . 'public/form-shortcode.php';

// بارگذاری استایل و اسکریپت‌های افزونه
function my_keyword_plugin_enqueue_scripts() {
    wp_enqueue_style( 'my-keyword-plugin-style', MY_KEYWORD_PLUGIN_URL . 'assets/css/my-keyword-plugin.css' );
    wp_enqueue_script( 'my-keyword-plugin-script', MY_KEYWORD_PLUGIN_URL . 'assets/js/my-keyword-plugin.js', array('jquery'), '1.0', true );
    // ارسال آدرس AJAX به اسکریپت
    wp_localize_script( 'my-keyword-plugin-script', 'myKeywordPlugin', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( 'wp_enqueue_scripts', 'my_keyword_plugin_enqueue_scripts' );
