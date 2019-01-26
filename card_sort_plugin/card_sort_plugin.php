<?php
/*
Plugin Name: Card Sort
Version: 1.0
Author: Future Web Studio 
Website: http://futurewebstudio.com
Description: This plugin allows researchers to create drag and drop card sorting studies and download results 
*/

//globals

$background_colors = array(
    "Dark Orange" => "#EF7521",
    "Light Orange" => "#FFA200",
    "Yellow" => "#FFC627",
    "Dark Blue" => "#00538A",
    "Medium Blue" => "#0083C1",
    "Light Blue" => "#009DE6",
    "Green" => "#ABAC21",
    "Light Gray" => "#EAEAEA",
    "Med Gray" => "#B3B5B7",
    "Dark Gray" => "#3b3c3d",
    "White" => "#fff",
    "Black" => "#000",
    "None" => ""
);

/**
 * Include Dependencies
 */
include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_options.php');
include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_shortcodes.php');
include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_metaboxes.php');
include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_ajax.php');
// include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_rest.php');

/**
* Add the scripts and styles we'll need to frontend...
**/
if ( ! function_exists( 'anthrohack_enqueue_and_register_my_scripts' ) ) :
	function anthrohack_enqueue_and_register_my_scripts(){

        //muuri drag and drop grid
        wp_enqueue_script( 'hammer_js', 'https://unpkg.com/hammerjs@2.0.8/hammer.min.js', array(), false, true );
        wp_enqueue_script( 'web_animations_polyfil', 'https://unpkg.com/web-animations-js@2.3.1/web-animations.min.js', array(), false, true );
        wp_enqueue_script( 'muuri_js', 'https://unpkg.com/muuri@0.7.1/dist/muuri.min.js', array( 'hammer_js', 'web_animations_polyfil' ), false, true );
        wp_enqueue_script( 'draggable_js', plugins_url( '/includes/js/anthrohack_draggable.js' , __FILE__ ), array( 'muuri_js' ), false, true );

	    //common scripts for plugin
	    wp_enqueue_script( 'anthrohack_js', plugins_url( '/includes/js/anthrohack.js' , __FILE__ ), array( 'jquery' ), false, true );
        wp_localize_script('anthrohack_js', 'anthrohack_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

	    //styles
	    wp_enqueue_style('icomoon_css', plugins_url('/includes/fonts/icomoon.css', __FILE__ ));
	    wp_enqueue_style('anthrohack_css', plugins_url( '/includes/css/anthrohack.css' , __FILE__ ));
	}
	add_action( 'wp_enqueue_scripts', 'anthrohack_enqueue_and_register_my_scripts' );
endif;

/**
 * Add the scripts and styles we'll need to admin...
 */
if ( ! function_exists( 'anthrohack_enqueue_admin' ) ) :
	function anthrohack_enqueue_admin( $hook_suffix ) {

	    wp_enqueue_media();
		wp_enqueue_script('media-upload');

        //styles
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style('icomoon_css', plugins_url('/includes/fonts/icomoon.css', __FILE__ ));
        wp_enqueue_style('bootstrap_slider_css', plugins_url('/includes/css/bootstrap-slider.min.css', __FILE__ ));
        wp_enqueue_style('admin_css', plugins_url('/includes/css/anthrohack_admin.css', __FILE__ ));

		// wp_enqueue_script('suggest');
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script('image_upload', plugins_url( '/includes/js/anthrohack_image_upload.js' , __FILE__ ), array( 'jquery' ), false, true );
		wp_enqueue_script('validate_js', 'https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_slider_js', plugins_url( '/includes/js/bootstrap-slider.min.js' , __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'wp_tinymce_js', plugins_url( '/includes/js/tinymce/wordpress-tinymce.js' , __FILE__ ) , array( 'jquery' ), false, true );
		wp_enqueue_script('admin_js', plugins_url( '/includes/js/anthrohack_admin.js' , __FILE__ ), array( 'jquery', 'wp-color-picker', 'wp_tinymce_js', 'media-editor' ) );
        wp_localize_script('admin_js', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));


	}
	add_action( 'admin_enqueue_scripts', 'anthrohack_enqueue_admin' );

endif;

/**
* Create custom Taxonomies
* // (have to create taxonomy before post type)
**/

// function anthrohack_create_taxonomies() {

//     $args = array(
//         'hierarchical'      => true,
//         'labels'            => array(
//             'name'              => _x( 'Piles', 'taxonomy general name' ),
//             'singular_name'     => _x( 'Pile', 'taxonomy singular name' ),
//             'search_items'      => __( 'Search Piles' ),
//             'all_items'         => __( 'All Piles' ),
//             'parent_item'       => __( 'Parent Piles' ),
//             'parent_item_colon' => __( 'Parent Piles:' ),
//             'edit_item'         => __( 'Edit Pile' ),
//             'update_item'       => __( 'Update Pile' ),
//             'add_new_item'      => __( 'Add New Pile' ),
//             'new_item_name'     => __( 'New Job Pile' ),
//             'menu_name'         => __( 'Job Piles' ),
//         ),
//         'show_ui'           => true,
//         'show_admin_column' => true,
//         'query_var'         => true,
//         'rewrite'           => array( 'slug' => "pile" ),
//     );
//     register_taxonomy( "pile", "card", $args );
    

// }
// add_action( 'init', 'anthrohack_create_taxonomies', 0 );

/**
* Add custom Post Types.
**/
function anthrohack_create_post_type() {

    register_post_type( 'study',
        array(
            'labels' => array(
                'name' => __( 'Studies' ),
                'singular_name' => __( 'Study' ),
                'edit_item' => 'Edit Study',
                'add_new_item' => 'Add New Study',
                'view_item' => 'View Study'
            ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'supports' => array('title', 'revisions'),
        'description' => ""

        )
    );

    register_post_type( 'sort',
        array(
            'labels' => array(
                'name' => __( 'Sorts' ),
                'singular_name' => __( 'Sort' ),
                'edit_item' => 'Edit Sort',
                'add_new_item' => 'Add New Sort',
                'view_item' => 'View Sort'
            ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'supports' => array(),
        'description' => ""

        )
    );


}//end anthrohack_create_post_type
add_action( 'init', 'anthrohack_create_post_type' );

/**
* Add custom Columns in Admin
*/
//studys
function anthrohack_study_columns_head($defaults) {
    // var_dump($defaults);
    $defaults['shortcode'] = 'Shortcode';
    return $defaults;
}

// // Make these columns sortable (thank you https://wordpress.org/support/topic/viewsort-by-custom-field-in-admin-post-list/)
function anthrohack_study_sortable_columns() {
  return array(
    'shortcode' => 'shortcode',
  );
}

function anthrohack_study_columns_content($column_name, $post_ID) {

    if ($column_name == 'shortcode') {
        echo "[card_sort_study id=" . $post_ID . "]"; 
    }  

}
add_filter('manage_study_posts_columns', 'anthrohack_study_columns_head', 10);
add_filter( "manage_edit-study_sortable_columns", "anthrohack_study_sortable_columns" );
add_action('manage_study_posts_custom_column', 'anthrohack_study_columns_content', 10, 2); 

//end custom columns

/**
* Remove SEO metaboxes from selected CPT
*/
function anthrohack_remove_wp_seo_meta_boxes() {
    $custom_post_types = array('card', 'sort', 'study');
    foreach ($custom_post_types as $name) {
        if(post_type_exists( $name ))
            remove_meta_box('wpseo_meta', $name, 'normal');
    }
}
add_action('add_meta_boxes', 'anthrohack_remove_wp_seo_meta_boxes', 100);


function anthrohack_custom_content_template($content) {
	global $post;	
  	if ( $post->post_type == 'study' ) {
    	if ( file_exists( plugin_dir_path( __FILE__ ) . '/templates/content-study.php' ) ) {
            $path = plugin_dir_path( __FILE__ ) . '/templates/content-study.php';
       		
       		ob_start();
				require $path;
       		$content = ob_get_clean();

        }
    }
    return $content;
}
add_filter('the_content', 'anthrohack_custom_content_template');


// utility for fetching params from post meta or options array
function anthrohack_check_meta_var($meta, $variable, $fallback = false){
    //checks if var exists in given meta array (can be options or page meta). returns var if exists, else returns fallback. Fallback defaults to false
    if(is_array($meta)){
        if( (array_key_exists($variable,$meta)) ){
            if(is_array($meta[$variable])){
                if( $meta[$variable][0] != ""){
                    return $meta[$variable][0];
                }
            }else{
                return $meta[$variable];
            }
        }
    }else if(is_object($meta)){
        if(property_exists($meta, $variable)){
            return $meta->{$variable};
        }
    }
    return $fallback;
}

// utility for returnong pase path of the installed plugin
function plugin_base_path(){
    return plugin_dir_path( __FILE__ );
}


