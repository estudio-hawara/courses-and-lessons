<?php

/**
 * Plugin Name: Courses and Lessons
 * Description: Creates a custom post type for Lessons and a custom taxonomy for Courses
 * Version: 1.0
 * Author: Carlos Capote <carlos.capote@hawara.es>
 * Text Domain: courses-and-lessons
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once 'src/Plugin.php';

$plugin = new Plugin(
    plugin_dir_path(__FILE__)
);

$plugin->addActions();
$plugin->addFilters();

register_activation_hook(__FILE__, fn()  => flush_rewrite_rules());
register_deactivation_hook(__FILE__, fn() => flush_rewrite_rules());