<?php

if (!defined('ABSPATH')) return;

add_action('admin_enqueue_scripts', 'surfw_enqueue_admin_scripts');
add_action('wp_enqueue_scripts', 'surfw_enqueue_front_scripts');

function surfw_enqueue_admin_scripts()
{
    wp_enqueue_style('datatables', SURFW__PATH . '/assets/css/datatables.css', [], 1);
    wp_enqueue_style('bootstrap', SURFW__PATH . '/assets/css/bootstrap.min.css', [], 1);
    wp_enqueue_script('bootstrap-bundle', SURFW__PATH . '/assets/js/bootstrap-bundle.min.js', [], 1);
    wp_enqueue_script('datatables', SURFW__PATH . '/assets/js/datatables.js' , ['jquery'], 1);
    wp_enqueue_script('sweetalert', SURFW__PATH . '/assets/js/sweetalert2.min.js', [], 1);
}

function surfw_enqueue_front_scripts()
{
    wp_enqueue_script('sweetalert', SURFW__PATH . '/assets/js/sweetalert2.min.js', [], 1);
}