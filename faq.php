<?php
/**
 * Plugin Name: FAQ Manager
 * Plugin URI: https://wordpress.org/plugins/
 * Description: Manage FAQs with categories
 * Version: 1.0.1
 * Author: Webshouters
 * Author URI: https://www.mysite.com/
 * Text Domain: faq
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

defined('ABSPATH') or die('Direct access not allowed!');

// Load text domain for translations
function faq_load_textdomain() {
    load_plugin_textdomain('faq', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'faq_load_textdomain');

// Include the post type and taxonomy registration file
require_once plugin_dir_path(__FILE__) . '/blocks/post_type.php';
require_once plugin_dir_path(__FILE__) . '/templates/shortcode.php';
// require_once plugin_dir_path(__FILE__) . '/templates/settings.php';

// Enqueue assets
function faq_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style(
        'faq-styles',
        plugin_dir_url(__FILE__) . '/css/faq.css'
    );
    
    // Enqueue JavaScript
    wp_enqueue_script(
        'faq-scripts',
        plugin_dir_url(__FILE__) . '/js/faq.js',
        array('jquery'),
        '1.0',
        true
    );
    
    // Pass settings to JavaScript
    $options = get_option('faq_options');
    wp_localize_script('faq-scripts', 'faqSettings', array(
        'singleOpen' => isset($options['default_single_open']) ? $options['default_single_open'] : 0
    ));
}
add_action('wp_enqueue_scripts', 'faq_enqueue_assets');



// function faq_admin_enqueue_scripts($hook) {
//     if ('faqs_page_faq-settings' !== $hook) {
//         return;
//     }
    
//     // Select2 for category multi-select
//     wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
//     wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
    
//     // Custom admin CSS
//     wp_enqueue_style('faq-admin', plugins_url('css/admin.css', __FILE__));
// }
// add_action('admin_enqueue_scripts', 'faq_admin_enqueue_scripts');