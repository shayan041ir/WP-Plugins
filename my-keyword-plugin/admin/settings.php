<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function my_keyword_plugin_add_admin_menu() {
    add_menu_page(
        'تنظیمات کلیدواژه', 
        'Keyword Research', 
        'manage_options', 
        'my-keyword-plugin', 
        'my_keyword_plugin_settings_page'
    );
}
add_action( 'admin_menu', 'my_keyword_plugin_add_admin_menu' );

function my_keyword_plugin_settings_page() {
    // بررسی و ذخیره تنظیمات در صورت ارسال فرم
    if ( isset( $_POST['submit'] ) && check_admin_referer( 'my_keyword_plugin_settings' ) ) {
        $api_key = sanitize_text_field( $_POST['openai_api_key'] );
        update_option( 'my_keyword_plugin_api_key', $api_key );
        echo '<div class="updated"><p>تنظیمات ذخیره شد!</p></div>';
    }
    $api_key = get_option( 'my_keyword_plugin_api_key', '' );
    ?>
    <div class="wrap">
        <h1>تنظیمات افزونه Keyword Research</h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'my_keyword_plugin_settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenAI API Key</th>
                    <td><input type="text" name="openai_api_key" value="<?php echo esc_attr( $api_key ); ?>" size="50"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
