<?php

add_action( 'wp_ajax_save_sort', 'anthrohack_save_sort_callback' );
add_action( 'wp_ajax_nopriv_save_sort', 'anthrohack_save_sort_callback' );

function anthrohack_save_sort_callback() {

    $response = (object) array();
   //check if its an ajax request, exit if not

    if($_POST){

        //check if its an ajax request, exit if not
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            die();
        }
        //check $_POST vars are set, exit if any missing
        if (!isset($_POST["sort_data"])) {
            die();
        }

        //Sanitize input data using PHP filter_var().
        $user_Email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $first_Name = filter_var($_POST["first_name"], FILTER_SANITIZE_EMAIL);
        $last_Name = filter_var($_POST["last_name"], FILTER_SANITIZE_EMAIL);

        //save new sort and associate with study

        //send success response
        $response->message = "Success!";
        
    }else{
        $response->message = "There was an error";
    }

    echo json_encode($response);
    wp_die();//needed to return a valid response;
}