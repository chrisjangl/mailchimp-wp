<?php
/*
Plugin Name: Advanced MailChimp Signup
Plugin URI:  http://digitallycultured.com
Description: Adds a widget for you to choose a MailChimp list to add subscribers to
Version:     0.1.1
Author:      Digitally Cultured
Author URI:  http://digitallycultured.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'PLUGIN_PATH' ) ) {
    define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

include('includes/widget.php');
include('includes/shortcode.php');
