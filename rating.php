<?php 
/*
Plugin Name: rating
Description: امتیاز دهی به محصولات.
Version: 1.0
Author: Shayan Rezaei
*/


add_filter ('the_content', function ($content){
    if(is_single())
    {
            $form = '
            <form id="rating-form" method="post">
                <label for="rating">امتیاز خود را ثبت کنید:</label>
                <select name="rating" id="rating">
                    <option value="1">⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                </select>
                <button type="button" id="submit-rating">ثبت امتیاز</button>
            </form>
            <div id="rating-message"></div>
        ';
        return $content . $form; // اضافه کردن فرم به محتوای نوشته
    }
    return $content;
});


// بارگذاری فایل JavaScript
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'rating-script', // نام اسکریپت
        plugin_dir_url(__FILE__) . 'rating.js', // مسیر فایل
        ['jquery'], // وابستگی به jQuery
        '1.0',
        true // بارگذاری در پایان صفحه
    );

    // ارسال nonce و آدرس AJAX به اسکریپت
    wp_localize_script('rating-script', 'ratingAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rating_nonce'),
    ]);
});


// ثبت امتیاز با AJAX
add_action('wp_ajax_save_rating', 'save_rating');
add_action('wp_ajax_nopriv_save_rating', 'save_rating');

function save_rating() {
    // بررسی nonce برای امنیت
    check_ajax_referer('rating_nonce', 'nonce');

    $postID = intval($_POST['post_id']);
    $rating = intval($_POST['rating']);

    if ($postID && $rating >= 1 && $rating <= 5) {
        // گرفتن مقادیر قبلی
        $ratings_count = get_post_meta($postID, 'ratings_count', true) ?: 0;
        $ratings_total = get_post_meta($postID, 'ratings_total', true) ?: 0;

        // بروزرسانی مقادیر
        $ratings_count++;
        $ratings_total += $rating;

        update_post_meta($postID, 'ratings_count', $ratings_count);
        update_post_meta($postID, 'ratings_total', $ratings_total);

        $average_rating = round($ratings_total / $ratings_count, 2);

        wp_send_json_success(['message' => "امتیاز شما ثبت شد! میانگین امتیاز: $average_rating"]);
    } else {
        wp_send_json_error(['message' => 'امتیاز نامعتبر است.']);
    }
}


// شورت‌کد برای نمایش میانگین امتیازات
add_shortcode('average_rating', function ($atts) {
    $postID = get_the_ID();
    $ratings_count = get_post_meta($postID, 'ratings_count', true) ?: 0;
    $ratings_total = get_post_meta($postID, 'ratings_total', true) ?: 0;
    
    if ($ratings_count > 0) {
        $average_rating = round($ratings_total / $ratings_count, 2);
        return "میانگین امتیازات این نوشته: $average_rating از 5 ⭐";
    } else {
        return "این نوشته هنوز امتیازی ندارد.";
    }
});
