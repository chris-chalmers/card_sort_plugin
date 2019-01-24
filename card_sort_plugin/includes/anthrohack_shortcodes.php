<?php 


// class_wrap shortcode
if ( ! function_exists( 'anthrohack_shortcode_class_wrap' ) ) :
    add_filter('widget_text', 'do_shortcode');
    function anthrohack_shortcode_class_wrap($atts, $content = null) {
        //this wraps the content in colored number bubble

        //example usage: [anthrohack_class_wrap class="purple" element="span"]<insert content>[/anthrohack_class_wrap]
         extract(shortcode_atts(array(
            'class' => '',
            'element' => 'div'
        ), $atts));
        
        return '<'.$element.' class="class_wrap '.$class.'" >'.$content.'</'.$element.'>';
        
    }

    add_shortcode("anthrohack_class_wrap", "anthrohack_shortcode_class_wrap");
endif; // end class_wrap shortcode

// study shortcode
if ( ! function_exists( 'anthrohack_shortcode_study' ) ) :
    add_filter('widget_text', 'do_shortcode');
    function anthrohack_shortcode_study($atts, $output = null) {

        //example usage: [card_sort_study id="n"]
         extract(shortcode_atts(array(
            'id' => '',
            'class' => '',
        ), $atts));         

        if($id != ""){
            $loop = new WP_Query( array( 'post_type' => 'study', 'posts_per_page' => 1, 'id' => $id ) );                        
            while ( $loop->have_posts() ) : $loop->the_post(); 

                global $post; 
                if ( file_exists( plugin_base_path() . '/templates/content-study.php' ) ) {
                                                

                    $path = plugin_base_path() . '/templates/content-study.php';
                    
                    // ob_start();
                    require $path;
                    // echo ob_get_clean();
                }
            endwhile;

        } //end if
    }
    add_shortcode("card_sort_study", "anthrohack_shortcode_study");
endif; //end study
