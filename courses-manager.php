<?php

/*
Plugin Name: Courses Manager
Description: مدیریت دوره‌های آموزشی با Custom Post Types.
Version: 1.0
Author: Shayan Rezaei
*/

// ثبت Custom Post Type برای دوره‌های آموزشی

use function ElementorDeps\DI\add;

function register_courses_post_type()
{
    //شامل متن‌هایی است که برای بخش مدیریت وردپرس نمایش داده می‌شوند.
    $labels = array(
        'name'               => 'دوره‌های آموزشی',
        'singular_name'      => 'دوره آموزشی',
        'menu_name'          => 'دوره‌ها',
        'name_admin_bar'     => 'دوره آموزشی',
        'add_new'            => 'افزودن دوره جدید',
        'add_new_item'       => 'افزودن دوره آموزشی جدید',
        'new_item'           => 'دوره جدید',
        'edit_item'          => 'ویرایش دوره',
        'view_item'          => 'مشاهده دوره',
        'all_items'          => 'همه دوره‌ها',
        'search_items'       => 'جستجوی دوره‌ها',
        'parent_item_colon'  => 'دوره والد:',
        'not_found'          => 'هیچ دوره‌ای یافت نشد.',
        'not_found_in_trash' => 'هیچ دوره‌ای در زباله‌دان یافت نشد.',
    );

    // تنظیمات اصلی Custom Post Type
    $args = array(
        'labels'             => $labels,
        'public'             => true,   //عمومی بودن را نشان میدهد
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'courses'),
        'capability_type'    => 'post',
        'has_archive'        => true,   //فعال سازی ارشیو برای این نوع محتوا
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-welcome-learn-more', // آیکون منو
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),  // تعیین می‌کند که این محتوا چه امکاناتی داشته باشد (مثل عنوان، محتوا، تصویر شاخص و ...).
    );

    //تعریف یک نوع محتوای جدید
    register_post_type('courses', $args);   //slug مسیر url این نوع محتوا در  courses است

}
add_action('init', 'register_courses_post_type');


// ثبت Taxonomy برای دسته‌بندی دوره‌ها
function register_course_taxonomy()
{
    $labels = array(
        'name'              => 'دسته‌بندی دوره‌ها',
        'singular_name'     => 'دسته‌بندی دوره',
        'search_items'      => 'جستجوی دسته‌ها',
        'all_items'         => 'همه دسته‌ها',
        'parent_item'       => 'دسته والد',
        'parent_item_colon' => 'دسته والد:',
        'edit_item'         => 'ویرایش دسته',
        'update_item'       => 'بروزرسانی دسته',
        'add_new_item'      => 'افزودن دسته جدید',
        'new_item_name'     => 'نام دسته جدید',
        'menu_name'         => 'دسته‌بندی‌ها',
    );
    $args = array(
        //اگر مقدار آن true باشد، Taxonomy مثل دسته‌بندی عمل می‌کند (سلسله مراتبی). اگر false باشد، مثل برچسب عمل می‌کند.
        'hierarchical'      => true, // دسته‌بندی‌ها به صورت سلسله مراتبی باشند (مانند دسته‌ها)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,    //اضافه کردن ستون دسته‌بندی در لیست دوره‌ها در مدیریت وردپرس.
        'query_var'         => true,
        'rewrite'           => array('slug' => 'course-category'),  //تعیین Slug برای URL دسته‌بندی‌ها (در اینجا course-category).
    );

    //این تابع برای تعریف یک Taxonomy جدید استفاده می‌شود
    register_taxonomy('course_category', 'courses', $args);
}
add_action('init', 'register_course_taxonomy');



// ثبت Shortcode برای نمایش لیست دوره‌ها
function display_courses_with_categories($atts)
{
    // دریافت دسته‌بندی از شورتکد (اختیاری)
    $atts = shortcode_atts(array(
        'category' => '', // می‌توان یک دسته خاص را فیلتر کرد
    ), $atts, 'courses_list');
    $atts['category'] = sanitize_text_field($atts['category']);

    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => -1, // نمایش همه دوره‌ها
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    // اگر دسته‌بندی مشخص شده باشد، فیلتر اضافه کن
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'course_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '<div class="courses-list">';

        while ($query->have_posts()) {
            $query->the_post();

            // دریافت دسته‌بندی‌ها
            $categories = get_the_terms(get_the_ID(), 'course_category');
            $category_list = '';
            if (!empty($categories)) {
                $category_list = '<ul class="course-categories">';
                foreach ($categories as $category) {
                    $category_list .= '<li>' . esc_html($category->name) . '</li>';
                }
                $category_list .= '</ul>';
            }

            // نمایش دوره
            //برای خروجی امن، از توابعی مانند esc_html() یا wp_kses_post() استفاده کنید:
            $output .= '<div class="course-item">';
            $output .= '<h3>' . esc_html(get_the_title()) . '</h3>';
            // $output .= $category_list;   //نام فیلتر دسته بندی ها 
            // $output .= '<p>' . wp_kses_post(get_the_excerpt()) . '</p>';   //محتوا    
            $output .= '<a href="' . esc_url(get_permalink()) . '">مشاهده جزئیات</a>';
            $output .= '</div>';
        }

        $output .= '</div>';
    } else {
        $output = '<p>دوره‌ای یافت نشد.</p>';
    }
    wp_reset_postdata();

    return $output;
}
add_shortcode('courses_list', 'display_courses_with_categories');


// add_filter('the_content', function ($content) {
//     if (is_page('دوره ها')) { // بررسی نامک (slug) برگه
//         $courses = do_shortcode('[courses_list]');
//         $content .= $courses;    
//     }
//     return $content; // اضافه کردن فرم به محتوای برگه
// });

include "function.php";
