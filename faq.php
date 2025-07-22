<?php
/**
 * Plugin Name: FAQ Manager
 * Plugin URI: https://wordpress.org/plugins/
 * Description: Manage FAQs with categories
 * Version: 1.0.1
 * Author: Webshouters
 * Author URI: https://www.mysite.com/
 * Text Domain: faq
 */

defined('ABSPATH') or die('Direct access not allowed!');

// Load text domain for translations
function faq_load_textdomain() {
    load_plugin_textdomain('faq', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'faq_load_textdomain');

// Load the update checker with error handling
// Replace the existing PUC loading code with this:
try {
    $pucPath = MY_CAROUSEL_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
    
    if (!file_exists($pucPath)) {
        // Provide a more helpful error message
        $message = 'Plugin Update Checker not found at: ' . $pucPath . '<br>';
        $message .= 'Please download it from: https://github.com/YahnisElsts/plugin-update-checker<br>';
        $message .= 'And place it in: ' . MY_CAROUSEL_PLUGIN_DIR . 'plugin-update-checker/';
        
        throw new Exception($message);
    }
    
    require_once $pucPath;
    
    if (!class_exists('YahnisElsts\PluginUpdateChecker\v5p6\PucFactory')) {
        throw new Exception('PucFactory class not found. Please ensure you have version 5.x of the Plugin Update Checker.');
    }
} catch (Exception $e) {
    add_action('admin_notices', function() use ($e) {
        echo '<div class="error"><p><strong>Carousel Plugin Error:</strong> ' . esc_html($e->getMessage()) . '</p></div>';
    });
    return;
}

// Initialize the update checker
add_action('plugins_loaded', function() {
    try {
        $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5p6\PucFactory::buildUpdateChecker(
            'https://github.com/atrix-it-solution/faq-plugin',
            __FILE__,
            'faq-plugin'
        );
        
        $myUpdateChecker->setBranch('main');
        
        // Only set authentication if needed
        // $myUpdateChecker->setAuthentication('your-token-here');
        
    } catch (Exception $e) {
        add_action('admin_notices', function() use ($e) {
            echo '<div class="error"><p>Carousel Update Checker Error: ' . esc_html($e->getMessage()) . '</p></div>';
        });
    }
});

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