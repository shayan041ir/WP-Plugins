<?php

/*
Plugin Name: welcome massage Plugin
Description : سفارشی‌سازی پیام خوشامدگویی سایت.
Version: 1.0
Author: Shayan Rezaei
*/

// ایجاد صفحه تنظیمات در منو مدیریت 
add_action('admin_menu', function () {
    add_menu_page(
        'تنظیمات پلاگین ما', // عنوان صفحه
        'پلاگین ما',          // عنوان منو
        'manage_options',     // قابلیت دسترسی
        'my-plugin-settings', // slug صفحه
        'welcom_massage_settings_page', // کال‌بک برای محتوای صفحه
        'dashicons-admin-generic', // آیکون
        20                     // موقعیت
    );
});

// تابع نمایش صفحه تنظیمات
function welcom_massage_settings_page()
{
    ?>
    <div class="warp">
        <h1>تنظیمات خوش‌آمدگویی</h1>
        <form action="options.php" method="post">
            <?php
            // تولید فیلدهای لازم برای ذخیره تنظیمات
            settings_fields('welcome_message_settings_group');
            do_settings_sections('welcome-message-settings');
            // دکمه ذخیره تنظیمات
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// ثبت تنظیمات با Settings API
add_action('admin_init', function () { // نام اکشن صحیح: admin_init
    // ثبت گروه تنظیمات
    register_setting('welcome_message_settings_group', 'welcome_message_text');

    // اضافه کردن بخش تنظیمات
    add_settings_section(
        'welcome_message_section',       // شناسه بخش
        'تنظیمات پیام',                  // عنوان بخش
        function () {                    // توضیحات
            echo 'پیام خوشامدگویی را برای سایت خود تنظیم کنید.';
        },
        'welcome-message-settings'       // صفحه‌ای که تنظیمات نمایش داده می‌شود
    );

    // اضافه کردن فیلد تنظیمات
    add_settings_field(
        'welcome_message_text',          // شناسه فیلد
        'متن پیام خوشامدگویی',          // عنوان فیلد
        function () {                    // تابع callback برای نمایش فیلد
            $value = get_option('welcome_message_text', 'به سایت ما خوش آمدید!');
            echo '<input type="text" name="welcome_message_text" value="' . esc_attr($value) . '" style="width: 400px;">';
        },
        'welcome-message-settings',      // صفحه‌ای که فیلد نمایش داده می‌شود
        'welcome_message_section'        // بخشی که فیلد در آن قرار می‌گیرد
    );
});

// نمایش پیام خوشامدگویی در هدر سایت
add_action('wp_head', function () {
    $welcome_message = get_option('welcome_message_text', 'به سایت ما خوش آمدید!');
    echo '<div style="text-align: center; font-size: 18px; background: #f4f4f4; padding: 10px;">' . esc_html($welcome_message) . '</div>';
});
