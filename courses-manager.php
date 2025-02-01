<?php
/*
Plugin Name: Courses Manager
Description: مدیریت دوره‌های آموزشی با Custom Post Types.
Version: 1.0
Author: Shayan Rezaei
*/

// ثبت Custom Post Type برای دوره‌های آموزشی
function register_courses_post_type() {
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

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'courses'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
    );

    register_post_type('courses', $args);
}
add_action('init', 'register_courses_post_type');

// ثبت Taxonomy برای دسته‌بندی دوره‌ها
function register_course_taxonomy() {
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
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'course-category'),
    );

    register_taxonomy('course_category', 'courses', $args);
}
add_action('init', 'register_course_taxonomy');

// ثبت Shortcode برای نمایش لیست دوره‌ها با Pagination
function display_courses_with_categories($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'per_page' => 5, // تعداد دوره‌ها در هر صفحه
    ), $atts, 'courses_list');

    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => intval($atts['per_page']),
        'paged'          => $paged,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'course_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($atts['category']),
            ),
        );
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        echo '<div class="courses-list">';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="course-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<a href="' . esc_url(get_permalink()) . '">مشاهده جزئیات</a>';
            echo '</div>';
        }
        echo '</div>';

        // Pagination
        echo '<div class="courses-pagination">';
        echo paginate_links(array(
            'total'     => $query->max_num_pages,
            'current'   => $paged,
            'prev_text' => 'قبلی',
            'next_text' => 'بعدی',
        ));
        echo '</div>';
    } else {
        echo '<p>دوره‌ای یافت نشد.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('courses_list', 'display_courses_with_categories');

// اضافه کردن فایل function.php
include "function.php";

// add_filter('the_content', function ($content) {
//     if (is_page('دوره ها')) { // بررسی نامک (slug) برگه
//         $courses = do_shortcode('[courses_list]');
//         $content .= $courses;    
//     }
//     return $content; // اضافه کردن فرم به محتوای برگه
// });
