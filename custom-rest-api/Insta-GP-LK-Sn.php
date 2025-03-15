<?php
/*
Plugin Name: Instagram Group Link Sender
Description: پلاگینی برای ارسال لینک پست اینستاگرام و اطلاعات گروه به سرور بات
Version: 1.0
Author: shayan rezayi
*/

function instagram_group_link_form_shortcode() {
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
    
    <form id="my-contact-form" method="post" action="">
                <label for="instagram_link">لینک پست اینستاگرام:</label>
                <input type="text" name="instagram_link" id="instagram_link" required><br>
                <label for="group_info">اطلاعات گروه (مثلاً شناسه گروه):</label>
                <input type="text" name="group_info" id="group_info" required><br>
                <input type="submit" value="ارسال">
            </form>';

}
add_shortcode('instagram_group_link_form', 'instagram_group_link_form_shortcode');


function process_instagram_group_link_form() {
    if (isset($_POST['instagram_link']) && isset($_POST['group_info'])) {
        $instagram_link = esc_url_raw($_POST['instagram_link']); // برای لینک
        $group_info = sanitize_text_field($_POST['group_info']); // برای اطلاعات گروه

        $bot_server_url = 'https://your-bot-server.com/api/endpoint';

        $data = array(
            'link' => $instagram_link,
            'group_info' => $group_info
        );

        $response = wp_remote_post($bot_server_url, array(
            'method' => 'POST',
            'body' => json_encode($data),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            echo 'error: ' . $response->get_error_message();
        } else {
            echo 'success';
        }
    }
}
add_action('init', 'process_instagram_group_link_form');

