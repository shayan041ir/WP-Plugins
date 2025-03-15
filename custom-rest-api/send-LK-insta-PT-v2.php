<?php 
/*
Plugin Name: Group Post Link Sender 2
Description: پلاگینی برای ارسال تعداد، اسم گروه و لینک پست به سرور بات
Version: 2.0
Author: shayan rezayi
*/

function group_post_link_form_shortcode() {
    return '
    <style>
                #my-contact-form {
            max-width: 400px;
            margin: 0 auto;
        }

        #my-contact-form label {
            display: block;
            margin-top: 10px;
        }

        #my-contact-form input,
        #my-contact-form textarea {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
        }

        #my-contact-form button {
            padding: 5px 10px;
            margin-top: 10px;
            background-color: #0073aa;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #response-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
    <form id="my-contact-form" method="post">
                <label for="count">تعداد:</label>
                <input type="number" name="count" id="count" required><br>
                <label for="group_name">اسم گروه:</label>
                <input type="text" name="group_name" id="group_name" required><br>
                <label for="post_link">لینک پست:</label>
                <input type="text" name="post_link" id="post_link" required><br>
                <input type="submit" value="ارسال">
            </form>';
}
add_shortcode('group_post_link_form', 'group_post_link_form_shortcode');


function ajax_process_group_post_link_form() {
    if (isset($_POST['count']) && isset($_POST['group_name']) && isset($_POST['post_link'])) {
        $count = intval($_POST['count']);
        $group_name = sanitize_text_field($_POST['group_name']);
        $post_link = esc_url_raw($_POST['post_link']);

        $bot_server_url = 'https://your-bot-server.com/api/endpoint'; 

        $data = array(
            'count' => $count,
            'group_name' => $group_name,
            'post_link' => $post_link
        );

        $response = wp_remote_post($bot_server_url, array(
            'method' => 'POST',
            'body' => json_encode($data),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            wp_send_json_error('خطا در ارسال داده‌ها');
        } else {
            wp_send_json_success('داده‌ها با موفقیت ارسال شدند');
        }
    } else {
        wp_send_json_error('داده‌ها ناقص هستند');
    }
}
add_action('wp_ajax_process_group_post_link', 'ajax_process_group_post_link_form');
add_action('wp_ajax_nopriv_process_group_post_link', 'ajax_process_group_post_link_form');

function enqueue_ajax_script() {
    wp_enqueue_script('my-ajax-script', plugins_url('/js/script.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('my-ajax-script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');