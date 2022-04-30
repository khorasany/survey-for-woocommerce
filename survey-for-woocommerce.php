<?php

/*
Plugin Name:       Survey for Woocommerce
Description:       Survey for Woocommerce is a simple survey plugin that work with Woocommerce plugin developed and designed for wordpress websites by kianland.co
Plugin URI:        https://kianlandsoft.ir/survey-for-woocommerce
Contributors:      (Alireza Saffar)
Author:            Alireza Saffar Khorasany
Author URI:        https://kianlandsoft.ir/
Donate link:       https://kianlandsoft.ir/donate/
Tags:              kianland
Version:           1.0
Stable tag:        1.0
Requires at least: 5.6
Tested up to:      5.6
Text Domain:       survey-for-woocommerce
Domain Path:       /languages
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined("ABSPATH")) exit;

define('SURFW_VERSION',1);
define('SURFW__PATH',plugin_dir_url(__FILE__));

require_once "uninstall.php";

function surfw_activate_plugin()
{
    if (!current_user_can("activate_plugins")) return;
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $options = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "survey_options (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          survey_name varchar(55) NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";

    $results = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "survey_results (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          survey_id int NOT NULL,
          user_id int NOT NULL,
          PRIMARY KEY  (id) 
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($options);
    dbDelta($results);
}

register_activation_hook(__FILE__, "surfw_activate_plugin");
require_once 'includes/surfw-enqueue.php';
require_once 'includes/surfw-menu.php';
require_once 'includes/surfw-front-handler.php';