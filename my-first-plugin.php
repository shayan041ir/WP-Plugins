<?php
/*
Plugin Name: My First Plugin
Description: پلاگین نمونه برای یادگیری.
Version: 1.0
Author: Shayan Rezaei
*/

add_action('admin_notices', function() {
    echo '<div class="notice notice-success is-dismissible">
        <p>سلام! خوش اومدی به داشبورد مدیریت وردپرس 🌟</p>
    </div>';
});

add_shortcode('current_datetime', function(){
    return date('Y-m-d H:i:s');
});


