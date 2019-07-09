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
	    // http://<your domain here>/wp-json/anthrohack-v1/forms/<form_id>
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
      
	    $data = array();           
	    $data['form_content'] = esc_html(do_shortcode('[card_sort_study id='.$request['id'].' echo=false]'));

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
