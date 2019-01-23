<?php class anthrohack_REST_Controller extends WP_REST_Controller {

// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
// https://jacobmartella.com/2017/12/22/simple-guide-adding-wp-rest-api-controller/
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
	    $version = '1';
	    $namespace = 'anthrohack-v' . $version;
	    $base = 'forms';
	    // http://interactivefunnelforms.com/wp-json/anthrohack-v1/forms/324
	    register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
		    'methods' => 'Get',
		    'callback' => array( $this,'render_form'),
		    // 'permission_callback' => array( $this, 'get_item_permissions_check' ),
		    'args' => array(
				'id' => array(
					'validate_callback' => function($param, $request, $key) {
							return is_numeric( $param );
						}
					),
				),
		  	) 
		);
	}


  // public function render_form( $request ) {
  // 	$data = array("foo" => "bar");

  // 	return new WP_REST_Response( $data, 200 );
  // }

	public function render_form( $request ) {
    
	  	//if no ID specified, use query
		$vars = array( 
			'post_type' => 'form_embed', 
			'post_status' => 'publish',
			'posts_per_page' => 1,
			// 'p' => (integer) $request['id'],
		);

		if(FALSE != get_post_status((integer) $request['id'] )){
			$vars['p'] = (integer) $request['id'];
		}

	    $loop = new WP_Query($vars);   
	    $data = array();           
	    while ( $loop->have_posts() ) : $loop->the_post(); 
	    	global $post;
	    	$form_meta = get_post_meta( $post->ID ); 
	    	$data['name'] = $post->post_name;
	    	$data['form_content'] = esc_html(do_shortcode(get_the_content($post)));
	    	$data['target_url'] = anthrohack_check_meta_var($form_meta, 'target_url');
	    	$data['css_url'] =  plugins_url( '/css/anthrohack.css' , __FILE__ );
	    	$data['ajax_url'] = admin_url('admin-ajax.php');
	    	// http://interactivefunnelforms.com/wp-content/plugins/interactive_anthrohack_forms/includes/css/anthrohack.css

	    	ob_start(); ?>

		    	<style>
					#anthrohack_form_embed,
					html .caldera-grid .radio label, 
					.caldera-grid .btn.btn-default, 
					.caldera-grid .btn.cf-page-btn-next {
						color: <?php echo anthrohack_check_meta_var($form_meta, 'field_fontcolor'); ?>!important;
						font-size: <?php echo anthrohack_check_meta_var($form_meta, 'field_fontsize'); ?>px!important;
					}

					#anthrohack_form_embed label,
					.caldera-form-page .form-group label{
						color: <?php echo anthrohack_check_meta_var($form_meta, 'label_fontcolor'); ?>!important;
						font-size: <?php echo anthrohack_check_meta_var($form_meta, 'label_fontsize'); ?>px!important;
					}

				</style>

	    	<?php $data['form_style'] = ob_get_clean();
		endwhile;
		wp_reset_postdata();
		

	    return new WP_REST_Response( $data, 200 );
	}


} //end controller

/**
 * Register the custom routes for the tables
 *
 * @since 1.4
 */
function anthrohack_register_endpoints(){

	//* Register the teams route
  	$controller = new anthrohack_REST_Controller();
	$controller->register_routes();
  
}
add_action('rest_api_init','anthrohack_register_endpoints');
?>