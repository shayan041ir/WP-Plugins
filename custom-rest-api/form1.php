<?php
/*
Plugin Name: My Form Plugin
Description: پلاگینی برای گرفتن اطلاعات از فرم و ارسال به سرور دیگر
Version: 1.0
Author: shayan rezayi
*/
function my_form_shortcode() {
    return '<form method="post" action="">
                <label for="name">username:</label>
                <input type="text" name="name" id="name" required>
                <label for="email">password:</label>
                <input type="password" name="password" id="password" required>
                <input type="submit" value="send">
            </form>';
}
add_shortcode('my_form', 'my_form_shortcode');

function process_my_form() {
    if (isset($_POST['name']) && isset($_POST['password'])) {
        $username = $_POST['name'];
        $password = $_POST['password'];

        $url = 'https://api.aykalapp.com/api/add_bot_to_user';
        $data = array(
            'access_token' => '1234567890',
            'username' => $username,
            'password' => $password
        );

        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'body' => json_encode($data),
            'headers' => array(
                'Content-Type' => 'application/json'
            )
        ));

        if (is_wp_error($response)) {
            echo 'error in sending data';
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'form_data';
            $submission_time = current_time('mysql');
            $sql = $wpdb->prepare(
                "INSERT INTO $table_name (name, password, submission_time) VALUES (%s, %s, %s)",
                $username,
                $password,
                $submission_time
            );
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $username,
                    'password' => $password,
                    'submission_time' => $submission_time
                )
            );
            echo 'data send successfully';
        }
    }
}
add_action('wp_enqueue_scripts', 'process_my_form');


function create_form_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_data'; // نام جدول با پیشوند wp_
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        password varchar(100) NOT NULL,
        submission_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'create_form_data_table' );



