<?php
// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Remove options from the database
delete_option('clb_logo_url');
delete_option('clb_background_color');
delete_option('clb_button_color');
