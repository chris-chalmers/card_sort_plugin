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
