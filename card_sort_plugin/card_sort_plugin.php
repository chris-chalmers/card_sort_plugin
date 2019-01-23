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
include( plugin_dir_path( __FILE__ ) . './includes/anthrohack_rest.php');

/**
* Add the scripts and styles we'll need to frontend...
**/
if ( ! function_exists( 'anthrohack_enqueue_and_register_my_scripts' ) ) :
	function anthrohack_enqueue_and_register_my_scripts(){
		
	    //common scripts for ff plugin
	    wp_enqueue_script( 'anthrohack_js', plugins_url( '/includes/js/anthrohack.js' , __FILE__ ), array( 'jquery' ), false, true );

	    //styles
	    wp_enqueue_style('anthrohack_css', plugins_url( '/includes/css/anthrohack.css' , __FILE__ ));
	}
	add_action( 'wp_enqueue_scripts', 'anthrohack_enqueue_and_register_my_scripts' );
endif;

/**
 * Add the scripts and styles we'll need to admin...
 */
if ( ! function_exists( 'we_enqueue_admin' ) ) :
	function we_enqueue_admin( $hook_suffix ) {

	    wp_enqueue_media();
		wp_enqueue_script('media-upload');

		// wp_enqueue_script('suggest');
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script('image_upload', plugins_url( '/includes/js/anthrohack_image_upload.js' , __FILE__ ), array( 'jquery' ), false, true );
		wp_enqueue_script('validate_js', 'https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_slider_js', plugins_url( '/includes/js/bootstrap-slider.min.js' , __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'wp_tinymce_js', plugins_url( '/includes/js/tinymce/wordpress-tinymce.js' , __FILE__ ) , array( 'jquery' ), false, true );
		wp_enqueue_script('admin_js', plugins_url( '/includes/js/anthrohack_admin.js' , __FILE__ ), array( 'jquery', 'wp-color-picker', 'wp_tinymce_js', 'media-editor' ) );
        wp_localize_script('admin_js', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

		//styles
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style('bootstrap_slider_css', plugins_url('/includes/css/bootstrap-slider.min.css', __FILE__ ));
		wp_enqueue_style('admin_css', plugins_url('/includes/css/anthrohack_admin.css', __FILE__ ));
	
	}
	add_action( 'admin_enqueue_scripts', 'we_enqueue_admin' );

endif;

/**
* Create custom Taxonomies
* // (have to create taxonomy before post type)
**/

function anthrohack_create_taxonomies() {

    $args = array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => _x( 'Piles', 'taxonomy general name' ),
            'singular_name'     => _x( 'Pile', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Piles' ),
            'all_items'         => __( 'All Piles' ),
            'parent_item'       => __( 'Parent Piles' ),
            'parent_item_colon' => __( 'Parent Piles:' ),
            'edit_item'         => __( 'Edit Pile' ),
            'update_item'       => __( 'Update Pile' ),
            'add_new_item'      => __( 'Add New Pile' ),
            'new_item_name'     => __( 'New Job Pile' ),
            'menu_name'         => __( 'Job Piles' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => "pile" ),
    );
    register_taxonomy( "pile", "card", $args );
    

}
add_action( 'init', 'anthrohack_create_taxonomies', 0 );

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

    register_post_type( 'card',
        array(
            'labels' => array(
                'name' => __( 'Cards' ),
                'singular_name' => __( 'Card' ),
                'edit_item' => 'Edit Card',
                'add_new_item' => 'Add New Card',
                'view_item' => 'View Card'
            ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'revisions'),
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

/*
* Register custom end point to expose CPT metadata (see anthrohack_metaboxes.php for field names)
*/ 
// function anthrohack_create_api_posts_meta_fields() {
 
// 	// first expose meta fields already defined in anthrohack_metaboxes.php
// 	register_rest_field( 'form_embed', 'anthrohack_form_embed', array(
//            'get_callback'    => function ( $object ) {
// 								    //get the id of the post object array
// 								    $post_id = $object['id'];
// 								    //return the post meta
// 								    return get_post_meta( $post_id );
// 								},
//            'schema'          => null,
//         )
//     );

// 	//then add custom fields from scratch	
//     register_rest_field( 'form_embed', 'anthrohack_css_url', array(
//            'get_callback'    => function(){ return plugins_url( 'includes/css/anthrohack.css' , __FILE__ ); },
//            'schema'          => null,
//         )
//     );
// }
// add_action( 'rest_api_init', 'anthrohack_create_api_posts_meta_fields' );

function anthrohack_custom_page_template($single) {
    global $post;
    /* Checks for single template by post type */
    if ( $post->post_type == 'form_embed' ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/templates/single-form_embed.php' ) ) {
            return plugin_dir_path( __FILE__ ) . '/templates/single-form_embed.php';
        }
    }
    return $single;
}
/* Filter the single_template with our custom function*/
add_filter('single_template', 'anthrohack_custom_page_template');


// utility for fetching params from post or options array
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
