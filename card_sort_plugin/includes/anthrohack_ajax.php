<?php

add_action( 'wp_ajax_save_sort', 'anthrohack_save_sort_callback' );
add_action( 'wp_ajax_nopriv_save_sort', 'anthrohack_save_sort_callback' );

function anthrohack_save_sort_callback() {

    $response = (object) array();
   //check if its an ajax request, exit if not

    if($_REQUEST){

        //check if its an ajax request, exit if not
        // if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        //     die();
        // }
        //check $_POST vars are set, exit if any missing
        if (!isset($_REQUEST['data']['piles'])) {
            die();
        }

        //enclose any tasks in a try/catch because any PHP errors throw a CORS exception in AJAX
        try {

            $study = get_post($_REQUEST['data']['study_id']);
            //chack if study exists
            if($study != NULL){
                
                $post_title = $study->post_title . " - sort " . (count(anthrohack_get_sorts_by_study_id( $_REQUEST['data']['study_id'] )) + 1);

                //save new sort and associate with study
                $sort_id = wp_insert_post(array (
                   'post_type' => 'sort',
                   'post_title' => $post_title,
                   'post_status' => 'publish',
                ));

                // $response->post_id = $_REQUEST['data']['study_id']; 

                if ($sort_id) {
                    // insert post meta
                    add_post_meta($sort_id, 'study_id', $_REQUEST['data']['study_id'], true);
                    add_post_meta($sort_id, 'piles', $_REQUEST['data']['piles'], true);
                    add_post_meta($sort_id, 'piles', $_REQUEST['data']['questions'], true);

                    //send success response
                    $response->message = "Success! - saved " . $post_title;

                }else{
                    $response->message = "Error - sort not created";
                    wp_send_json_error( $response , 400 );
                    die();
                }

            }else{
                $response->message = "Error - no study_id";
                wp_send_json_error( $response , 400 );
                die();
            } //end if
   

        }catch (exception $e) {

            $response->message = $e->getMessage();
            $response->code = $e->getCode();

            wp_send_json_error( $response , 400 );
            die();
        }
        
    }else{
        $response->message = "There was an error";
    }

    echo json_encode($response);
    wp_die();//needed to return a valid response;
}
