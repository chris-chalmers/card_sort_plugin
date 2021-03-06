<?php

add_action( 'wp_ajax_save_sort', 'anthrohack_save_sort_callback' );
add_action( 'wp_ajax_nopriv_save_sort', 'anthrohack_save_sort_callback' );

add_action( 'wp_ajax_delete_sorts', 'anthrohack_delete_sorts_callback' );

function anthrohack_save_sort_callback() {

    $response = (object) array();
   //check if its an ajax POST, exit if not

    if($_POST){

        //check if its an ajax POST, exit if not
        // if (!isset($_SERVER['HTTP_X_POSTED_WITH']) AND strtolower($_SERVER['HTTP_X_POSTED_WITH']) != 'xmlhttpPOST') {
        //     die();
        // }
        //check $_POST vars are set, exit if any missing
        if (!isset($_POST['data']['piles'])) {
            die();
        }

        //enclose any tasks in a try/catch because any PHP errors throw a CORS exception in AJAX
        try {

            $study = get_post($_POST['data']['study_id']);
            //chack if study exists
            if($study != NULL){
                
                $post_title = $study->post_title . " - sort " . (count(anthrohack_get_sorts( $_POST['data']['study_id'] )) + 1);

                //save new sort and associate with study
                $sort_id = wp_insert_post(array (
                   'post_type' => 'sort',
                   'post_title' => $post_title,
                   'post_status' => 'publish',
                ));

                // $response->post_id = $_POST['data']['study_id']; 

                if ($sort_id) {
                    // insert post meta
                    add_post_meta($sort_id, 'study_id', $_POST['data']['study_id'], true);
                    add_post_meta($sort_id, 'piles', json_encode($_POST['data']['piles']), true);
                    add_post_meta($sort_id, 'questions', json_encode($_POST['data']['questions']), true);

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

function anthrohack_delete_sorts_callback() {

    $response = (object) array();
   //check if its an ajax POST, exit if not
    $log = [];
    if($_POST){

        //check if its an ajax POST, exit if not
        // if (!isset($_SERVER['HTTP_X_POSTED_WITH']) AND strtolower($_SERVER['HTTP_X_POSTED_WITH']) != 'xmlhttpPOST') {
        //     die();
        // }
        //check $_POST vars are set, exit if any missing
        if (!isset($_POST['data']['sorts_to_delete'])) {
            die();
        }

        // $response->ids = json_encode($_POST['data']['sorts_to_delete']);
        // echo json_encode($response);
        // wp_die();//needed to return a valid response;

        //enclose any tasks in a try/catch because any PHP errors throw a CORS exception in AJAX
        try {

            $sort_ids = $_POST['data']['sorts_to_delete'];  

            // array_push($log, $sort_ids);

            //check if study exists
            if($sort_ids){

                $i = 0;
                foreach ($sort_ids as $sort_id) {

                   $success = wp_delete_post($sort_id, true);

                   array_push($log, $sort_id);

                   if(!$success){
                        if ($i == 0)
                            $fail_message .= "Failed to delete submissions: ";   
                        $i ++; 
                        $fail_message .= $sort_id . ", ";
                   }
               }

                if (NULL != $fail_message) {
                    
                    //send success response
                    $response->message = $fail_message;
                }else{
                    $response->success = true;
                    $response->message = "Success! - all submissions deleted.";
                }

            }else{
                $response->message = "Error - no sort ids";
            } //end if
   

        }catch (exception $e) {

            $response->message = $e->getMessage();
            $response->code = $e->getCode();
        }
        
    }else{
        $response->message = "There was an error";
    }
    $response->log = $log;
    echo json_encode($response);
    wp_die();//needed to return a valid response;
}
