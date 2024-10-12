<?php
/*
Plugin Name: Personalized Login
Plugin URI: https://pattyweb.com.br/plugins/personalized-login
Description: Customize the WordPress login page with your logo, colors, and branding.
Version: 1.0
Author: Patricia Rodrigues
Author URI: https://pattyweb.com.br
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue custom styles for the login page
function clb_custom_login_styles() {
    $logo_url = get_option('clb_logo_url'); // Get the custom logo URL from options
    $background_color = get_option('clb_background_color'); // Get the custom background color from options
    $button_color = get_option('clb_button_color'); // Get the button color

    // Fallback if no logo URL is saved
    if (!$logo_url) {
        $logo_url = plugin_dir_url(__FILE__) . 'default-logo.png'; // Use a default logo if no logo uploaded
    }

    ?>
    <style type="text/css">
        body.login {
            background-color: <?php echo esc_attr($background_color); ?>;
        }
        .login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>') !important;
            background-size: contain!important;
            width: 300px!important; /* Increase the width of the logo */
            height: 150px!important; /* Increase the height of the logo */
            margin-bottom: -30px!important;
            pointer-events: none; /* Disable clicking on the logo */
        }
        .login #wp-submit {
            background-color: <?php echo esc_attr($button_color); ?>;
            border-color: <?php echo esc_attr($button_color); ?>;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'clb_custom_login_styles');

// Remove the link from the logo
function clb_remove_logo_link() {
    return ''; // Return an empty string to remove the link
}
add_filter('login_headerurl', 'clb_remove_logo_link');

// Use the new login_headertext filter instead of login_headertitle
function clb_remove_logo_text() {
    return ''; // Return an empty string to remove the text attribute
}
add_filter('login_headertext', 'clb_remove_logo_text');

// Redirect users to a specific URL after login
function clb_login_redirect($redirect_to, $request, $user) {
    return home_url(); // Redirect to the homepage (can be customized via settings)
}
add_filter('login_redirect', 'clb_login_redirect', 10, 3);

// Create the settings page with media uploader for the logo
function clb_register_settings() {
    register_setting('clb_options_group', 'clb_logo_url', 'esc_url_raw'); // Sanitize URL input
    register_setting('clb_options_group', 'clb_background_color', 'sanitize_hex_color'); // Sanitize color input
    register_setting('clb_options_group', 'clb_button_color', 'sanitize_hex_color'); // Sanitize color input
}
add_action('admin_init', 'clb_register_settings');

function clb_settings_page() {
    ?>
    <div class="wrap">
        <h1>Personalized Login</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('clb_options_group');
            wp_nonce_field('clb_save_settings', 'clb_nonce'); // Add the nonce here
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Upload Logo</th>
                    <td>
                        <input type="hidden" id="clb_logo_url" name="clb_logo_url" value="<?php echo esc_attr(get_option('clb_logo_url')); ?>" />
                        <input type="button" class="button" value="Upload Logo" id="upload_logo_button" />
                        <img id="logo_preview" src="<?php echo esc_attr(get_option('clb_logo_url')); ?>" style="max-width: 150px; display: block; margin-top: 10px;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="text" name="clb_background_color" value="<?php echo esc_attr(get_option('clb_background_color')); ?>" class="color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Login Button Color</th>
                    <td><input type="text" name="clb_button_color" value="<?php echo esc_attr(get_option('clb_button_color')); ?>" class="color-picker" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
add_action('admin_menu', 'clb_add_settings_page');

function clb_add_settings_page() {
    add_options_page(
        'Personalized Login',
        'Login Branding',
        'manage_options',
        'personalized-login',
        'clb_settings_page'
    );
}

// Enqueue media uploader script
function clb_enqueue_media_uploader() {
    wp_enqueue_media(); // Load the WordPress media uploader
    wp_enqueue_script(
        'clb-media-uploader',
        plugin_dir_url(__FILE__) . 'media-uploader.js',
        array('jquery'),
        '1.0.0', // Set explicit version number
        true
    );
}
add_action('admin_enqueue_scripts', 'clb_enqueue_media_uploader');

// Enqueue color picker for settings page
function clb_enqueue_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'clb-script-handle',
        plugins_url('personalized-login.js', __FILE__),
        array('wp-color-picker', 'jquery'),
        '1.0.0', // Set explicit version number
        true
    );
}
add_action('admin_enqueue_scripts', 'clb_enqueue_color_picker');
