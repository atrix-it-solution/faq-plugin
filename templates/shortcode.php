<?php 


function faq_shortcode($atts) {
    $faq_options = get_option('faq_options');
    
    // Set defaults from settings
    $default_limit = isset($faq_options['items_per_page']) ? $faq_options['items_per_page'] : 6;
    $default_style = isset($faq_options['template_style']) ? $faq_options['template_style'] : 'simple';

    $atts = shortcode_atts(array(
        'category' => isset($faq_options['default_categories']) ? implode(',', $faq_options['default_categories']) : '',     
        'limit'    => $default_limit,    
        'orderby'  => 'date', 
        'order'    => $faq_options['default_order'] ?? 'DESC',
        'style'    => $default_style  // 'simple' or 'grid'
    ), $atts, 'display_faqs');

    $args = array(
        'post_type' => 'faqs',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => sanitize_text_field($atts['orderby']),
        'order' => sanitize_text_field($atts['order']),
    );

    if (!empty($atts['category'])) {
        $field = is_numeric($atts['category']) ? 'term_id' : 'slug';
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'faqs_category',
                'field' => $field,
                'terms' => $atts['category']
            )
        );
    }

    ob_start();
    $faq_query = new WP_Query($args);

    if ($faq_query->have_posts()) {
        // Choose layout based on style parameter
        if ($atts['style'] === 'grid') {
            echo '<div class="faq-container faq-grid">';
            echo '<div class="faq-grid-wrapper">';
            
            while ($faq_query->have_posts()) {
                $faq_query->the_post();
                
                echo '<div class="faq-item">';
                echo '<h3 class="faq-question">' . get_the_title() . '</h3>';
                echo '<div class="faq-answer">' . apply_filters('the_content', get_the_content()) . '</div>';
                echo '</div>';
            }
            
            echo '</div></div>';
        } else {
            // Default simple list layout
            echo '<div class="faq-container faq-simple">';
            
            while ($faq_query->have_posts()) {
                $faq_query->the_post();
                echo '<div class="faq-item">';
                echo '<h3 class="faq-question">' . get_the_title() . '</h3>';
                echo '<div class="faq-answer">' . apply_filters('the_content', get_the_content()) . '</div>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        wp_reset_postdata();
    } else {
        echo '<p>No FAQs found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('display_faqs', 'faq_shortcode');
