<?php

// Register Custom Post Type
function faq_create_post_type() {
    $labels = array(
        'name'                  => _x('FAQs', 'Post Type General Name', 'faq'),
        'singular_name'         => _x('FAQ', 'Post Type Singular Name', 'faq'),
        'menu_name'             => __('FAQs', 'faq'),
        'all_items'             => __('All FAQs', 'faq'),
        'add_new_item'          => __('Add New FAQ', 'faq'),
        'add_new'               => __('Add New', 'faq'),
        'edit_item'             => __('Edit FAQ', 'faq'),
        'update_item'           => __('Update FAQ', 'faq'),
        'view_item'             => __('View FAQ', 'faq'),
    );

    $args = array(
        'label'                 => __('FAQ', 'faq'),
        'description'           => __('Frequently Asked Questions', 'faq'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'taxonomies'            => array('faqs_category'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-editor-help',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'           => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array('slug' => 'faqs'),
        'show_in_rest'          => true,
    );

    register_post_type('faqs', $args);
}
add_action('init', 'faq_create_post_type', 0);

// Register Custom Taxonomy
function faq_create_taxonomy() {
    $labels = array(
        'name'                       => _x('FAQ Categories', 'Taxonomy General Name', 'faq'),
        'singular_name'              => _x('FAQ Category', 'Taxonomy Singular Name', 'faq'),
        'menu_name'                  => __('FAQ Categories', 'faq'),
        'all_items'                  => __('All Categories', 'faq'),
        'parent_item'                => __('Parent Category', 'faq'),
        'parent_item_colon'          => __('Parent Category:', 'faq'),
        'new_item_name'              => __('New Category Name', 'faq'),
        'add_new_item'               => __('Add New Category', 'faq'),
        'edit_item'                  => __('Edit Category', 'faq'),
        'update_item'                => __('Update Category', 'faq'),
        'view_item'                  => __('View Category', 'faq'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                   => array('slug' => 'faqs-category'),
        'show_in_rest'               => true,
    );

    register_taxonomy('faqs_category', array('faqs'), $args);
}
add_action('init', 'faq_create_taxonomy', 0);


// 1. Register the settings page in admin menu
function faq_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=faqs', 
        'FAQ Settings',            
        'FAQ Settings',                
        'manage_options',          
        'faq-settings',            
        'faq_render_settings_page' 
    );
}
add_action('admin_menu', 'faq_add_settings_page');

// 2. Register settings and fields
function faq_register_settings() {
    // Register a setting group
    register_setting(
        'faq_settings_group',
        'faq_options',
        array(
            'sanitize_callback' => 'faq_sanitize_options'
        )
    );
    
    // Add settings section
    add_settings_section(
        'faq_main_section',
        'Main Settings',
        'faq_section_callback',
        'faq-settings'
    );

    add_settings_field(
    'faq_default_categories',
    'Default Categories',
    'faq_default_categories_callback',
    'faq-settings',
    'faq_main_section'
    );
    
    // Add fields
    add_settings_field(
        'faq_items_per_page',
        'limit',
        'faq_items_per_page_callback',
        'faq-settings',
        'faq_main_section'
    );
    

    add_settings_field(
        'faq_default_single_open',
        'Single Open',
        'faq_default_single_open_callback',
        'faq-settings',
        'faq_main_section'
    );
    add_settings_field(
        'faq_template_style',
        'Template Style',
        'faq_template_style_callback',
        'faq-settings',
        'faq_main_section'
    );
    add_settings_field(
    'faq_default_order',
    'Default Order',
    'faq_default_order_callback',
    'faq-settings',
    'faq_main_section'
    );


}
add_action('admin_init', 'faq_register_settings');

// 3. Callback functions for rendering
function faq_render_settings_page() {
    ?>
    <div class="wrap">
         <!-- Add this debug line -->
        <!-- <?php echo '<pre>Debug: '; print_r(get_option('faq_options')); echo '</pre>'; ?> -->
        
        <h1>FAQ Plugin Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('faq_settings_group');
            do_settings_sections('faq-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function faq_section_callback() {
    echo '<p>Configure your FAQ plugin settings</p>';
}

function faq_sanitize_options($input) {
    $input['default_single_open'] = isset($input['default_single_open']) ? 1 : 0;

     // Sanitize order settings
    $input['default_order'] = in_array($input['default_order'] ?? 'DESC', ['ASC', 'DESC']) 
        ? $input['default_order'] 
        : 'DESC';

    // $input['template_style'] = sanitize_text_field($input['template_style'] ?? 'simple');
    // $input['items_per_page'] = absint($input['items_per_page'] ?? 6);
    
    // Sanitize categories
    if (isset($input['default_categories'])) {
        $input['default_categories'] = array_map('absint', $input['default_categories']);
    } else {
        $input['default_categories'] = array();
    }
    
    return $input;
}
function faq_default_single_open_callback() {
    $options = get_option('faq_options');
    $current_value = isset($options['default_single_open']) ? $options['default_single_open'] : 0;
    ?>
    <label>
        <input type="checkbox" name="faq_options[default_single_open]" value="1" <?php checked($current_value, 1); ?>>
        Enable single item open mode
    </label>
    <p class="description">When enabled, only one FAQ item will be open at a time</p>
    <?php
}


function faq_items_per_page_callback() {
    $options = get_option('faq_options');
    echo '<input type="number" name="faq_options[items_per_page]" value="'.esc_attr($options['items_per_page'] ?? 6).'" min="1">';
}


$input['template_style'] = sanitize_text_field($input['template_style'] ?? 'simple');
return $input;

// New callback function
function faq_template_style_callback() {
    $options = get_option('faq_options');
    $current_value = $options['template_style'] ?? 'simple';
    ?>
    <fieldset>
        <label>
            <input type="radio" name="faq_options[template_style]" value="simple" <?php checked($current_value, 'simple'); ?>>
            Simple List
        </label><br>
        <label>
            <input type="radio" name="faq_options[template_style]" value="grid" <?php checked($current_value, 'grid'); ?>>
            Grid Layout
        </label>
    </fieldset>
    <p class="description">Choose how FAQs should be displayed</p>
    <?php
}

function faq_default_order_callback() {
    $options = get_option('faq_options');
    $current_value = $options['default_order'] ?? 'DESC';
    ?>
    <fieldset>
        <label>
            <input type="radio" name="faq_options[default_order]" value="ASC" <?php checked($current_value, 'ASC'); ?>>
            Ascending (ASC)
        </label><br>
        <label>
            <input type="radio" name="faq_options[default_order]" value="DESC" <?php checked($current_value, 'DESC'); ?>>
            Descending (DESC)
        </label>
    </fieldset>
    <p class="description">Default sort direction for FAQs</p>
    <?php
}


function faq_default_categories_callback() {
    $options = get_option('faq_options');
    $selected_categories = isset($options['default_categories']) ? (array)$options['default_categories'] : array();
    $categories = get_terms(array(
        'taxonomy' => 'faqs_category',
        'hide_empty' => false,
    ));
    ?>
    <div >
        <?php foreach ($categories as $category) : ?>
            <label >
                <input type="checkbox" name="faq_options[default_categories][]" value="<?php echo esc_attr($category->term_id); ?>" 
                    <?php checked(in_array($category->term_id, $selected_categories)); ?>>
                <?php echo esc_html($category->name); ?>
            </label>
        <?php endforeach; ?>
    </div>
    <p class="description">Select default categories to display</p>
    <?php
}




