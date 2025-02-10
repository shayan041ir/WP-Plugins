<?php
/*
Plugin Name: My AJAX Plugin
Description: A simple example of AJAX in WordPress.
Version: 1.0
Author: Shayan Rezaei
*/

// 1. ثبت اسکریپت و اضافه کردن متغیرهای AJAX
function my_ajax_enqueue_scripts() {
    wp_enqueue_script(
        'ajax-script',
        plugin_dir_url(__FILE__) . 'js/ajax-script.js',
        array('jquery'),
        null,
        true
    );

    // ارسال اطلاعات AJAX به اسکریپت
    wp_localize_script('ajax-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('my_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'my_ajax_enqueue_scripts');

// 2. تعریف Callback برای پردازش درخواست AJAX
function my_ajax_handle_request() {
    check_ajax_referer('my_ajax_nonce', 'security');

    // دریافت داده از درخواست
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : 'Guest';

    // ارسال پاسخ
    wp_send_json_success(array(
        'message' => "سلام، $name! درخواست AJAX با موفقیت پردازش شد.",
    ));
}
add_action('wp_ajax_my_action', 'my_ajax_handle_request');
add_action('wp_ajax_nopriv_my_action', 'my_ajax_handle_request');
