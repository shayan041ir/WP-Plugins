<?php
/*
Plugin Name: My First Plugin
Description: پلاگین نمونه برای یادگیری.
Version: 1.0
Author: Shayan Rezaei
*/

add_action('admin_notices', function() {
    echo '<div class="notice notice-success is-dismissible"><p>پلاگین من فعال است!</p></div>';
});


add_shortcode('my_message', function() {
    return "سلام دنیا از پلاگین من!";
});

