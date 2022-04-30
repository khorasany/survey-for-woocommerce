<?php

if (!defined(("ABSPATH"))) exit;

function surfw_uninstall_plugin()
{
    if (!current_user_can("uninstall_plugins")) return;
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."survey_options");
    $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."survey_results");
}

function surfw_deactivate_plugin()
{
    if (!current_user_can("deactivate_plugins")) return;
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, "surfw_deactivate_plugin");
register_uninstall_hook(__FILE__, "surfw_uninstall_plugin");
