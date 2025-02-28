<?php

/**
 * Plugin Name: Elementor Gmail AI Widget
 * Plugin URI:  https://www.shantanugoswami.online
 * Description: Connects to Gmail and analyzes emails using DeepSeek AI API.
 * Version: 1.0
 * Author: Shantanu Goswami
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

// Register Elementor Widget
function register_gmail_ai_widget($widgets_manager)
{
  require_once plugin_dir_path(__FILE__) . 'includes/widget.php';
  // Register the widget
  $widgets_manager->register(new \Gmail_AI_Widget());
}
add_action('elementor/widgets/register', 'register_gmail_ai_widget');

// Load dependencies
require_once plugin_dir_path(__FILE__) . 'includes/gmail-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/deepseek-api.php';
