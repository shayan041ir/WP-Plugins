<?php
/*
Plugin Name: My Form Display Plugin
Description: پلاگینی برای نمایش اطلاعات از فرم
Version: 1.0
Author: shayan rezayi
*/

function display_form_data_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_data';

    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY submission_time DESC" );

    if ( $results ) {
        $output = '<table>';
        $output .= '<tr><th>username</th><th>password</th><th>submission time</th></tr>';
        foreach ( $results as $row ) {
            $output .= '<tr>';
            $output .= '<td>' . esc_html( $row->name ) . '</td>';
            $output .= '<td>' . esc_html( $row->password ) . '</td>';
            $output .= '<td>' . esc_html( $row->submission_time ) . '</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
    } else {
        $output = 'no data found';
    }

    return $output;
}
add_shortcode( 'display_form_data', 'display_form_data_shortcode' );
