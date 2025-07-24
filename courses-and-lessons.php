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

require_once 'vendor/autoload.php';

use CoursesAndLessons\Plugin;

$plugin = new Plugin(__FILE__);
$plugin->addActions();
$plugin->addFilters();
$plugin->registerHooks();