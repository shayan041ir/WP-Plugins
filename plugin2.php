<?php

/*
Plugin Name: Simple Page View Counter
Description: شمارشگر بازدید از نوشته‌ها.
Version: 1.0
Author: Shayan Rezaei
*/


function increment_view_count($postID)
{
    $views = get_post_meta($postID, 'view_count', true);
    $views = $views ? $views + 1 : 1;
    update_post_meta($postID, 'view_count', $views);
}

add_action('wp_head', function () {
    echo '<div class="notice notice-success is-dismissible">
    <p>🌟</p>
    </div>';

    if (is_single()) {
        increment_view_count(get_the_ID());
    }
});

add_shortcode('view_count', function ($atts) {
    $postID = get_the_ID();
    $views = get_post_meta($postID, 'view_count', true) ?? 0;
    return "این نوشته $views بازدید داشته است.";
});
