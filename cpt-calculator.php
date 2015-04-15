<?php
/*
Plugin Name: Custom Post Type Calculator
Plugin URI: http://getbutterfly.com/wordpress-plugins-free/
Description: Create a multi-purpose category-based calculator and use it to calculate item amounts and values. Allow the user to request a quote based on current calculation.
Version: 0.8
License: GPLv3
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/

Copyright 2014, 2015 Ciprian Popescu (email: getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

define('CPTC_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('CPTC_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('CPTC_PLUGIN_VERSION', '0.8');

// plugin localization
load_plugin_textdomain('cptc', false, dirname(plugin_basename(__FILE__)) . '/languages/');

include(CPTC_PLUGIN_PATH . '/includes/page-settings.php');

add_action('init', 'cptc_plugin_init');
add_action('admin_menu', 'cptc_menu'); // settings menu

function cptc_menu() {
    add_submenu_page('edit.php?post_type=items', 'CPTC Settings', 'CPTC Settings', 'manage_options', 'cptc_admin_page', 'cptc_admin_page');
}

add_shortcode('cpt-calculator', 'cptc_main');

function cptc_main($atts, $content = null) {
	extract(shortcode_atts(array(
		'category' => '',
	), $atts));

    global $current_user;
	$out = '';

	if(isset($_POST['cptc_submit'])) {
        $emailout = '';

        foreach($_POST as $key => $value) {
            if($key != 'cptc_submit')
                if($value != '0')
                    $emailout .= '' . ucwords(str_replace('_', ' ', $key)) . ': <b>' . $value . '</b><br>';
        }

        // send notification email to administrator
        $item_admin_email = get_option('item_admin_email');
        $item_notification_subject = __('New calculation received!', 'cptc') . ' | ' . get_bloginfo('name');
        $item_notification_message = __('New calculation received!', 'cptc') . ' | ' . get_bloginfo('name');

        $headers = '';
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";
        wp_mail($item_admin_email, $item_notification_subject, $emailout, $headers);

        $out .= '<p class="cptc_confirmation_message">' . __('Item calculation submitted!', 'cptc') . '</p>';
	}


	$args = array(
		'item_category'   => $category,
		'post_type'       => 'items',
		'post_status'     => 'publish',
		'posts_per_page'  => 999,
        'orderby'         => 'title',
        'order'           => 'ASC'
	);

	$posts = new WP_Query($args);

    $out .= '<form class="pure-form pure-form-aligned" method="post">
        <fieldset>';
            foreach($posts->posts as $item) {
                $item_cats = get_the_terms($item->ID, 'item_category');

                $item_name = get_the_title($item->ID);
                $item_slug = sanitize_title($item_name);
                $item_value = get_post_meta($item->ID, 'item_value', true);

                $item_surface_label = get_option('item_surface_label');
                $item_measurement_label = get_option('item_measurement_label');
                $item_calculate_button_label = get_option('item_calculate_button_label');
                $item_result_label = get_option('item_result_label');

                $item_contact_section_title = get_option('item_contact_section_title');
                $item_contact_button_label = get_option('item_contact_button_label');

                $item_currency = get_option('item_currency');

                $item_show_quote = get_option('item_show_quote');

                $out .= '
                <div>
                    ' . $item_surface_label . ' <input name="' . $item_slug . '" type="number" data-value="' . $item_value . '" value="0" min="0" step="0.01" class="pure-u-1-8"><em>' . $item_measurement_label . '</em> 
                    <!--<input type="hidden" name="' . $item_slug . '" value="' . $item_value . '">-->
                    <label> <i class="fa fa-angle-right"></i> <b>' . $item_name . '</b></label>
                </div>';
            }
            $out .= '
        </fieldset>
        <fieldset>
            <div>
                <button id="calculate" class="pure-button">' . $item_calculate_button_label . ' <i class="fa fa-angle-double-right"></i></button>
                <p>' . $item_result_label . ' <b><span id="total">0</span></b> ' . $item_currency . '</p>
                <input type="hidden" id="total_value" name="total_value" value="0">
            </div>
        </fieldset>';

        if($item_show_quote == 1) {
        $out .= '<h3>' . $item_contact_section_title . '</h3>
        <fieldset class="pure-group">
            <input name="contact_name" type="text" class="pure-input-1-2" placeholder="' . __('Name', 'cptc') . '">
            <input name="contact_email" type="email" class="pure-input-1-2" placeholder="' . __('Email Address', 'cptc') . '">
        </fieldset>
        <fieldset class="pure-group">
            <textarea name="contact_notes" class="pure-input-1-2" rows="4" placeholder="' . __('Notes', 'cptc') . '"></textarea>
        </fieldset>
        <input name="cptc_submit" type="submit" class="pure-button" value="' . $item_contact_button_label . '">';
        }
    $out .= '</form>';

	return $out;
}


function cptc_plugin_init() {
	add_option('item_admin_email', '');

    add_option('item_calculate_button_label', 'Calculate');
    add_option('item_surface_label', 'Surface');
    add_option('item_measurement_label', 'sqm');

    add_option('item_contact_section_title', 'Need a quote?');
    add_option('item_contact_button_label', 'Send');

    add_option('item_result_label', 'Estimated cost is');
    add_option('item_currency', 'EUR');

    add_option('item_show_quote', 1);

	$item_type_labels = array(
		'name' 					=> _x('Items', 'post type general name'),
		'singular_name' 		=> _x('Item', 'post type singular name'),
		'add_new' 				=> _x('Add New Item', 'item'),
		'add_new_item' 			=> __('Add New Item'),
		'edit_item' 			=> __('Edit Item'),
		'new_item' 				=> __('Add New Item'),
		'all_items' 			=> __('View Items'),
		'view_item' 			=> __('View Item'),
		'search_items' 			=> __('Search Items'),
		'not_found' 			=> __('No items found'),
		'not_found_in_trash' 	=> __('No items found in trash'), 
		'parent_item_colon' 	=> '',
		'menu_name' 			=> __('CPT Calculator', 'cpt-calculator')
	);

	$item_type_args = array(
		'labels' 				=> $item_type_labels,
		'public' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> true,
		'capability_type' 		=> 'post',
		'has_archive' 			=> true,
		'hierarchical' 			=> false,
		'map_meta_cap' 			=> true,
		'menu_position' 		=> null,
		'supports' 				=> array('title', 'custom-fields'),
		'menu_icon' 			=> 'dashicons-editor-kitchensink',
	);

	register_post_type('items', $item_type_args);

	$item_category_labels = array(
		'name' 					=> _x('Item Categories', 'taxonomy general name'),
		'singular_name' 		=> _x('Item', 'taxonomy singular name'),
		'search_items' 			=> __('Search Item Categories'),
		'all_items' 			=> __('All Item Categories'),
		'parent_item' 			=> __('Parent Item Category'),
		'parent_item_colon' 	=> __('Parent Item Category:'),
		'edit_item' 			=> __('Edit Item Category'), 
		'update_item' 			=> __('Update Item Category'),
		'add_new_item' 			=> __('Add New Item Category'),
		'new_item_name' 		=> __('New Item Name'),
		'menu_name' 			=> __('Item Categories'),
	);

	$item_category_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $item_category_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'item_category'),
	);

	register_taxonomy('item_category', array('items'), $item_category_args);
}



add_action('wp_enqueue_scripts', 'cptc_enqueue_scripts');
function cptc_enqueue_scripts() {
    wp_enqueue_style('fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

	// purecss.io // 0.6.0 // load as 'pure'
	wp_enqueue_style('pure', plugins_url('css/pure.css', __FILE__));	

    wp_enqueue_script('cptc-jqmain', plugins_url('js/jquery.main.js', __FILE__), array('jquery'));
}
// end







// basic includes //
/**
 * Item value will be displayed in calculation form
 */
function cptcmeta_post_mode() {
    $cptcmeta_location = array('items');

    foreach($cptcmeta_location as $cptcmeta_locations) {
        add_meta_box('item_value', __('Item Price', 'cptc'), 'cptcmeta_inner_post_mode', $cptcmeta_locations, 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'cptcmeta_post_mode');

function cptcmeta_inner_post_mode($post) {
    wp_nonce_field('cptcmeta_inner_post_mode', 'cptcmeta_inner_post_mode_nonce');

    $value = get_post_meta($post->ID, 'item_value', true);

    echo '<label for="cptcmeta_post_field">';
        _e('Item Price', 'cptcmeta_box');
    echo '</label> '; ?>
    <input type="number" name="cptcmeta_post_field" value="<?php echo $value; ?>" class="text" min="0" step="0.01">
    <?php
}

function cptcmeta_save_meta_box($post_id) {
    if(!isset($_POST['cptcmeta_inner_post_mode_nonce']))
        return $post_id;

    $nonce = $_POST['cptcmeta_inner_post_mode_nonce'];

    if(!wp_verify_nonce($nonce, 'cptcmeta_inner_post_mode'))
        return $post_id;

    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    if('item' == sanitize_text_field($_POST['post_type'])) {
        if(!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {
        if(!current_user_can('edit_post', $post_id))
            return $post_id;
    }

    $mydata = sanitize_text_field($_POST['cptcmeta_post_field']);

    update_post_meta($post_id, 'item_value', $mydata);
}
add_action('save_post', 'cptcmeta_save_meta_box');
?>
