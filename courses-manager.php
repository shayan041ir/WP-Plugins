<?php

/*
Plugin Name: Courses Manager
Description: مدیریت دوره‌های آموزشی با Custom Post Types.
Version: 1.0
Author: Shayan Rezaei
*/

// ثبت Custom Post Type برای دوره‌های آموزشی
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
