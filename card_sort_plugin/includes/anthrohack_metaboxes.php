<?php 

// Create 'top' sections for metaboxes and move that to the top
add_action('edit_form_after_title', function() {
  global $post, $wp_meta_boxes;
  do_meta_boxes(get_current_screen(), 'top', $post);
  unset($wp_meta_boxes[get_post_type($post)]['top']);
});

global $background_colors; //set in anthrohack.php


function anthrohack_meta_boxes() {  

    add_meta_box(  
        'anthrohack_study_options',
        'Study options',
        'anthrohack_study_options_callback',
        'study',
        'top'
    );

    add_meta_box(  
        'anthrohack_advanced_options',
        'Default card appearance',
        'anthrohack_advanced_options_callback',
        'study',
        'top'
    );

    add_meta_box(  
        'anthrohack_study_questions',
        'Survey questions',
        'anthrohack_study_questions_callback',
        'study',
        'top'
    );

    add_meta_box(  
        'anthrohack_study_piles',
        'Piles',
        'anthrohack_study_piles_callback',
        'study',
        'top'
    );

    add_meta_box(  
        'anthrohack_study_cards',
        'Cards',
        'anthrohack_study_cards_callback',
        'study',
        'top'
    );

}  
add_action( 'add_meta_boxes', 'anthrohack_meta_boxes' ); 

function anthrohack_study_options_callback ( $post )  {
    wp_nonce_field( basename( __FILE__ ), 'anthrohack_nonce' );
    $anthrohack_settings = get_option( 'anthrohack_settings' );  

        $page_options = anthrohack_get_page_names_and_ids();

        $anthrohack_field_array = array(
            array(
                "name" => "description",
                "title" => __( "Research protocol", '_anthrohack_' ),
                "type" => "editor",
                "description" => "Description of the study, goals and methodology.",
                "default_content" => ""
            ),
            array(
                "name" => "constrained",
                "title" => __( "Constrained?", '_anthrohack_' ),
                "type" => "checkbox",
                "description" => "Choose if study is constrained. Constrained studies require researcher to create and name piles.",
                "default_content" => ""
            ),
            array(
                "name" => "min_piles",
                "title" => __( "Minimum number of piles", '_anthrohack_' ),
                "type" => "text",
                "description" => "Minimum number of piles sorter is allowed to create (if study is unconstrained).",
                "default_content" => "2"
            ),
            array(
                "name" => "min_cards",
                "title" => __( "Minimum cards per pile", '_anthrohack_' ),
                "type" => "text",
                "description" => "Minimum number of cards that sorter is allowed to put in each pile.",
                "default_content" => "2"
            ),
            array(
                "name" => "cards_instructions",
                "title" => __( "Cards instructions", '_anthrohack_' ),
                "type" => "textarea",
                "description" => "Short description do go in the cards window",
                "default_content" => "Drag each card onto a pile."
            ),
            array(
                "name" => "thank_you_page",
                "title" => __( "Thank you page", '_anthrohack_' ),
                "type" => "select",
                'select_options' => $page_options,
                "description" => "Select a page to redirect to after sort submission. If none selected, page will simply refresh.",
                "default_content" => "none"
            ),
            array(
                "name" => "notify_researcher",
                "title" => __( "Email notifications", '_anthrohack_' ),
                "type" => "checkbox",
                "description" => "Select this box to enable email notifications when a new sort is submitted",
                "default_content" => "off"
            ),
            array(
                "name" => "notification_email",
                "title" => __( "Researcher notification email", '_anthrohack_' ),
                "type" => "textarea",
                "description" => "A comma delimited list of email addresses to notify when new sort is made. <sup>*</sup>Optional - if left blank, site admin will be notified.",
                "default_content" => ""
            ),
            array(
                "name" => "modal_title",
                "title" => __( 'Modal Title', '_anthrohack_' ),
                "type" => "text",
                "description" => 'Title of modal window where sorter answers final questions.',
                "default_content" => "Almost there",
                "class" => ''
            ),
            array(
                "name" => "modal_subtitle",
                "title" => __( 'Modal Sub Title', '_anthrohack_' ),
                "type" => "text",
                "description" => 'Sub Title of modal window where sorter answers final questions.',
                "default_content" => "Just a few more questions",
                "class" => ''
            ),
            array(
                "name" => "modal_description",
                "title" => __( 'Instructions for pile descriptions', '_anthrohack_' ),
                "type" => "editor",
                "description" => 'Additional instructions or explanation for the sorter as they finish the question modal.',
                "default_content" => "Please add a description for each pile and your reasoning for sorting cards into it the way you did.",
                "class" => ''
            ),
        ); //end field_array
  
    echo "Shortcode = [card_sort_study id=" . $post->ID . "]";

    foreach ($anthrohack_field_array as $field_array) {
        echo anthrohack_render_meta_field($field_array);
    }
    
}//end study options callback
function anthrohack_advanced_options_callback ( $post )  {
    wp_nonce_field( basename( __FILE__ ), 'anthrohack_nonce' );
    $anthrohack_settings = get_option( 'anthrohack_settings' );  

        $anthrohack_field_array = array(
            array(
                "name" => "card_text_alignment",
                "title" => __( "Default card text alignment", '_anthrohack_' ),
                "type" => "select",
                'select_options' => array(
                    'center' => "center",
                    'left' => 'left',
                    'right' => "right",
                ),
                "description" => "Alignment for title and description on cards.",
                "default_content" => "center"
            ),
            array(
                "name" => "color_scheme",
                "title" => __( 'Default card text color', '_anthrohack_' ),
                "type" => "select",
                "select_options" => array(
                    "Light" => "light",
                    "Theme Default" => "default",
                    "Dark" => "dark",
                    ),
                "description" => "Choose light or dark text color (For best readability, choose light for darker background colors/images and vice versa)",
                "default_content" => "default",
                "class" => ''
            ),
            array(
                "name" => "padding",
                "title" => __( 'Default card Padding', '_anthrohack_' ),
                "type" => "select",
                "select_options" => array(
                    "0 pixels" => "0",
                    "15 pixels" => "15",
                    "30 pixels" => "30",
                    "50 pixels" => "50",
                    "100 pixels" => "100",
                    "200 pixels" => "200",
                    ),
                "description" => "Padding top and bottom for card (The amount of space between edge and beginning/end of content).",
                "default_content" => "30",
                "class" => ''
            ),
            array(
                "name" => "hero_image",
                "title" => __( 'Default card background image', '_anthrohack_' ),
                "type" => "image",
                "description" => "This image will be used as a full-bleed background. It will appear behind the main color if transparency is set.",
                "default_content" => "",
                "class" => ''
            ),
            array(
                "name" => "background_position",
                "title" => __( 'Default card background Position', '_anthrohack_' ),
                "type" => "select",
                "select_options" => array(
                    "Center" => "center",
                    "Top" => "top",
                    "Bottom" => "bottom",
                    ),
                "description" => "Align the background to the top center or bottom of the screen",
                "default_content" => "center",
                "class" => ''
            ),
            array(
                "name" => "background_color",
                "title" => __( 'Default card background color', '_anthrohack_' ),
                "type" => "color_selector",
                "description" => "Optional - Choose a background color for this card. If no color selected, background image will be used.",
                "default_content" => "",
                "class" => ''
            ),
            array(
                "name" => "background_color2",
                "title" => __( 'Default card background color 2', '_anthrohack_' ),
                "type" => "color_selector",
                "description" => "Optional - Choose a secondary overlay color to create a gradient. If selected, gradient will appear over background image.",
                "default_content" => "",
            ),
            array(
                "name" => "gradient_angle",
                "title" => __( 'Default gradient angle', '_anthrohack_' ),
                "type" => "text",
                "description" => 'Choose an angle 0 - 360, Zero being  vertical (Top -> Bottom)',
                "default_content" => "",
                "class" => ''
            ),
            array(
                "name" => "background_transparency",
                "title" => __( 'Default background transparency (%)', '_anthrohack_' ),
                "type" => "slider",
                "range" => array(0,100),
                "description" => "Choose the percentage transparency (0-100) for the background color of this card. Video or image will appear behind color.",
                "default_content" => "100",
                "class" => ''
            ),
        ); //end field_array
  
    echo "Shortcode = [card_sort_study id=" . $post->ID . "]";

    foreach ($anthrohack_field_array as $field_array) {
        echo anthrohack_render_meta_field($field_array);
    }
    
}//end advanced options callback

// utility for rendering a button for adding sections in front or back end
function buttons($hidden = false, $type = "section"){ 
    if(!$hidden) $hidden = "";
    ?>
    <div class="buttons <?php echo $hidden; ?>" >
        <a type="button" class="add-section button" data-type="<?php echo $type; ?>"><i class="fa fa-plus" aria-hidden="true"></i> Add <?php echo $type; ?></a>
    </div>
<?php }

/**
* questions!
* Draggable sections for questions 
**/
function anthrohack_study_questions_callback ( $post )  {
    wp_nonce_field( basename( __FILE__ ), 'anthrohack_nonce' );
    $anthrohack_stored_meta = get_post_meta( $post->ID ); 
    $post_slug = $post->post_name; 
    $anthrohack_questions = anthrohack_check_meta_var($anthrohack_stored_meta, "anthrohack_questions");
    ?>

    <div class="questions section-wrap">
        <?php buttons(false, "question"); ?>
        <input type="hidden" class="hidden-input" name="anthrohack_questions" id="anthrohack_questions" value='<?php echo $anthrohack_questions; ?>' >
        <div id="anthrohack_questions_fields" class="layout-sections meta-box-sortables ui-sortable" data-id="<?php echo $post->ID; ?>">

                <?php 
                
                $anthrohack_questions_object = json_decode($anthrohack_questions);

                //both anthrohack_check_meta_var and json_decode return false if no questions are present or string is not JSON serializabel.
                if($anthrohack_questions && $anthrohack_questions_object){

                    //sort questions by order
                    // function cmp($a, $b){
                    //     return strcmp($a->question_order, $b->question_order);
                    // }
                    // usort($anthrohack_questions, "cmp");

                    foreach ($anthrohack_questions_object as  $question) { 
                        anthrohack_question_meta_template(get_object_vars($question));
                    } //end foreach ?>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                        <?php buttons(false, "question"); 
                }else{ ?>
                        <div class="note">No questions yet. click button to add one!</div>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                    <?php buttons("hidden", "question");
                 } ?>
    </div><!-- .wrap -->
    <div id="anthrohack_question_template" class="hidden">
        <?php echo anthrohack_question_meta_template(); //this is written as a function because it's re-used ?>
    </div>

<?php }//end draggable callback

//Questions fields
function anthrohack_question_meta_template($question = Null){ 
    $anthrohack_settings = get_option( 'anthrohack_settings' );  

    $anthrohack_questions_fields = array(
        array(
            "name" => "required",
            "title" => __( 'Required?', '_anthrohack_' ),
            "type" => "checkbox",
            "description" => "Check if this question is required. If checked, sorter will not be allowed to submit a finished sort without answering.",
            "default_content" => "on",
            "class" => ''
        ),
        array(
            "name" => "content",
            "title" => __( 'Additional question text', '_anthrohack_' ),
            "type" => "editor",
            "description" => "<sup>*</sup>Optional - This text will replace the title when it's shown on the front end.",
            "default_content" => "",
            "class" => ""
        ),
        array(
            "name" => "field_type",
            "title" => __( 'Field type', '_anthrohack_' ),
            "type" => "select",
            "select_options" => array(
                "text" => "text",
                "textarea" => "textarea",
                "select" => "select",
                "checkbox" => "checkbox",
                "radio" => "radio",
                "datepicker" => "datepicker",
                "locationpicker" => "locationpicker",
            ),
            "default_content" => "text",
            "class" => ""
        ),
         
    );
     
    if($question == Null){
        $question = array(
            "section_title" => "questions_template",
            "section_slug" => "questions_template",
            "section_type" => "question",
        );
    }
     
    anthrohack_render_section($anthrohack_questions_fields, $question);
}
//end Questions

/**
* piles!
* Draggable sections for piles 
**/
function anthrohack_study_piles_callback ( $post )  {
    wp_nonce_field( basename( __FILE__ ), 'anthrohack_nonce' );
    $anthrohack_stored_meta = get_post_meta( $post->ID ); 
    $post_slug = $post->post_name; 
    $anthrohack_piles = anthrohack_check_meta_var($anthrohack_stored_meta, "anthrohack_piles"); ?>

    <div class="piles section-wrap">
        <?php buttons(false, "pile"); ?>
        <input type="hidden" class="hidden-input" name="anthrohack_piles" id="anthrohack_piles" value='<?php echo $anthrohack_piles; ?>' >
        <div id="anthrohack_piles_fields" class="meta-box-sortables ui-sortable layout-sections" data-id="<?php echo $post->ID; ?>">

                <?php $anthrohack_piles_object = json_decode($anthrohack_piles);
                //NOTE both anthrohack_check_meta_var and json_decode return false if no piles are present or string is not JSON serializabel.
                if($anthrohack_piles && $anthrohack_piles_object){
                    foreach ($anthrohack_piles_object as  $pile) { 
                        anthrohack_pile_meta_template(get_object_vars($pile));
                    } //end foreach ?>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                        <?php buttons(false, "pile"); 
                }else{ ?>
                        <div class="note">No piles yet. click button to add one!</div>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                    <?php buttons("hidden", "pile");
                 } ?>
    </div><!-- .wrap -->
    <div id="anthrohack_pile_template" class="hidden">
        <?php echo anthrohack_pile_meta_template(); //this is written as a function because it's re-used ?>
    </div>

<?php }//end draggable callback

function anthrohack_pile_meta_template($pile = Null){ 
    $anthrohack_settings = get_option( 'anthrohack_settings' );  

    $anthrohack_piles_fields = array(
        array(
            "name" => "color_scheme",
            "title" => __( 'Color Scheme', '_anthrohack_' ),
            "type" => "select",
            "select_options" => array(
                "Light" => "light",
                "Theme Default" => "default",
                "Dark" => "dark",
                ),
            "description" => "Choose light or dark text color (For best readability, choose light for darker background colors/images and vice versa)",
            "default_content" => "default",
            "class" => ''
        ),
        array(
            "name" => "background_color",
            "title" => __( 'card background color', '_anthrohack_' ),
            "type" => "color_selector",
            "description" => "Optional - Choose a background color for this card. If no color selected, background image will be used.",
            "default_content" => "",
            "class" => ''
        ),
        array(
            "name" => "background_color2",
            "title" => __( 'card background color', '_anthrohack_' ),
            "type" => "color_selector",
            "description" => "Optional - Choose a secondary overlay color to create a gradient. If selected, gradient will appear over background image.",
            "default_content" => anthrohack_check_meta_var($anthrohack_settings, "page_bg_color2", ""),
            "class" => ''
        ),
        array(
            "name" => "gradient_angle",
            "title" => __( 'Gradient angle', '_anthrohack_' ),
            "type" => "text",
            "description" => 'Choose an angle 0 - 360, Zero being  vertical (Top -> Bottom)',
            "default_content" => "",
            "class" => ''
        ),

    );

    if($pile == Null){
        $pile = array(
            "section_title" => "piles_template",
            "section_slug" => "piles_template",
            "section_type" => "pile",
        );
    }
     
    anthrohack_render_section($anthrohack_piles_fields, $pile);
}
//end Piles

/**
* Cards!
* Draggable sections for cards 
**/
function anthrohack_study_cards_callback ( $post )  {
    wp_nonce_field( basename( __FILE__ ), 'anthrohack_nonce' );
    $anthrohack_stored_meta = get_post_meta( $post->ID ); 
    $post_slug = $post->post_name; 
    $anthrohack_cards = anthrohack_check_meta_var($anthrohack_stored_meta, "anthrohack_cards"); ?>

    <div class="cards section-wrap">
        <?php buttons(false, "card"); ?>
        <input type="hidden" class="hidden-input" name="anthrohack_cards" id="anthrohack_cards" value='<?php echo $anthrohack_cards; ?>' >
        <div id="anthrohack_cards_fields" class="meta-box-sortables ui-sortable layout-sections" data-id="<?php echo $post->ID; ?>">

                <?php $anthrohack_cards_object = json_decode($anthrohack_cards);
                //NOTE both anthrohack_check_meta_var and json_decode return false if no cards are present or string is not JSON serializabel.
                if($anthrohack_cards && $anthrohack_cards_object){
                    foreach ($anthrohack_cards_object as  $card) { 
                        anthrohack_card_meta_template(get_object_vars($card));
                    } //end foreach ?>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                        <?php buttons(false, "card"); 
                }else{ ?>
                        <div class="note">No Cards yet. click button to add one!</div>
                    </div><!-- .meta-box-sortables.ui-sortable-->
                    <?php buttons("hidden", "card");
                 } ?>
    </div><!-- .wrap -->
    <div id="anthrohack_card_template" class="hidden">
        <?php echo anthrohack_card_meta_template(); //this is written as a function because it's re-used ?>
    </div>

<?php }//end draggable callback

function anthrohack_card_meta_template($card = Null){ 
    $anthrohack_settings = get_option( 'anthrohack_settings' );  

    $anthrohack_cards_fields = array(
         array(
            "name" => "color_scheme",
            "title" => __( 'Color Scheme', '_anthrohack_' ),
            "type" => "select",
            "select_options" => array(
                "Light" => "light",
                "Theme Default" => "default",
                "Dark" => "dark",
                ),
            "description" => "Choose light or dark text color (For best readability, choose light for darker background colors/images and vice versa)",
            "default_content" => "default",
            "class" => ''
        ),
        array(
            "name" => "padding",
            "title" => __( 'card Padding', '_anthrohack_' ),
            "type" => "select",
            "select_options" => array(
                "0 pixels" => "0",
                "15 pixels" => "15",
                "30 pixels" => "30",
                "50 pixels" => "50",
                "100 pixels" => "100",
                "200 pixels" => "200",
                ),
            "description" => "Padding top and bottom for card (The amount of space between edge and beginning/end of content).",
            "default_content" => "30",
            "class" => ''
        ),
        array(
            "name" => "hero_image",
            "title" => __( 'Background image', '_anthrohack_' ),
            "type" => "image",
            "description" => "This image will be used as a full-bleed background. It will appear behind the main color if transparency is set.",
            "default_content" => "",
            "class" => ''
        ),
        array(
            "name" => "background_position",
            "title" => __( 'Background Position', '_anthrohack_' ),
            "type" => "select",
            "select_options" => array(
                "Center" => "center",
                "Top" => "top",
                "Bottom" => "bottom",
                ),
            "description" => "Align the background to the top center or bottom of the screen",
            "default_content" => "center",
            "class" => ''
        ),
        array(
            "name" => "background_color",
            "title" => __( 'card background color', '_anthrohack_' ),
            "type" => "color_selector",
            "description" => "Optional - Choose a background color for this card. If no color selected, background image will be used.",
            "default_content" => "",
            "class" => ''
        ),
        array(
            "name" => "background_color2",
            "title" => __( 'card background color', '_anthrohack_' ),
            "type" => "color_selector",
            "description" => "Optional - Choose a secondary overlay color to create a gradient. If selected, gradient will appear over background image.",
            "default_content" => anthrohack_check_meta_var($anthrohack_settings, "page_bg_color2", ""),
            "class" => ''
        ),
        array(
            "name" => "gradient_angle",
            "title" => __( 'Gradient angle', '_anthrohack_' ),
            "type" => "text",
            "description" => 'Choose an angle 0 - 360, Zero being  vertical (Top -> Bottom)',
            "default_content" => "",
            "class" => ''
        ),
        array(
            "name" => "background_transparency",
            "title" => __( 'Background transparency (%)', '_anthrohack_' ),
            "type" => "slider",
            "range" => array(0,100),
            "description" => "Choose the percentage transparency (0-100) for the background color of this card. Video or image will appear behind color.",
            "default_content" => "100",
            "class" => ''
        ),
    );

    if($card == Null){
        $card = array(
            "section_title" => "cards_template",
            "section_slug" => "cards_template",
            "section_type" => "card"
        );
    }
     
    anthrohack_render_section($anthrohack_cards_fields, $card);
}
//end Cards

//general function for rendering sections (used by cards, piles and questions)
function anthrohack_render_section($fields, $section){

    $type = anthrohack_check_meta_var($section, "section_type", "");
    $title = anthrohack_check_meta_var($section, "section_title", "");
    $id_number = anthrohack_check_meta_var($section, "section_id_number", "none");
    $slug = anthrohack_check_meta_var($section, "section_slug", strtolower(str_replace(' ', '_', $title)), "");
    $order = anthrohack_check_meta_var($section, "section_order", "");
    $disabled = anthrohack_check_meta_var($section, "section_disabled", "");
    $closed = anthrohack_check_meta_var($section, "section_closed", "closed");
    $color_class = anthrohack_check_meta_var($section, $slug."_color_scheme", "");
    $color_field = anthrohack_check_meta_var($section, $slug . "_background_color");
    $color_field2 = anthrohack_check_meta_var($section, $slug . "_background_color2");
    $color_angle = anthrohack_check_meta_var($section, $slug . "_gradient_angle", "90");
    $transparency_field = anthrohack_check_meta_var($section, $slug . "_background_transparency");

    $handle_style = "";
    if($color_field){
        //if there's one color
        $handle_style .= 'style="background-color: '.$color_field.'"';
        
        //overwrite with gradient code if there's two.
        if($color_field2) //not .= but = because we're over-writing
            $handle_style = 'style="background-color: transparent; background-image: linear-gradient('.$color_angle.'deg, '.$color_field.', '.$color_field2.');"';
    }


    // $section is an object containing all the params for that section ?>
    <div id="<?php echo str_replace(' ', '_', strtolower($title)); ?>" data-id="<?php echo $id_number; ?>" class="postbox layout-section <?php echo $color_class; ?> <?php echo $closed; ?> <?php echo $disabled; ?>">
        <button class="handlediv toggle" aria-expanded="true"><span class="screen-reader-text">Toggle section</span><span class="toggle-indicator" aria-hidden="true"></span></button>
        <!-- <div class="postbox-button disable" alt="Disable"><span class="screen-reader-text">Disable section</span><i class="fa fa-eye-slash" aria-hidden="true"></i><i class="fa fa-eye" aria-hidden="true"></i></i></div> -->
        <div class="postbox-button delete" alt="Delete"><span class="screen-reader-text">Delete section</span><i class="fa fa-trash" aria-hidden="true"></i></div>
        <div class="postbox-button copy" alt="Copy"><span class="screen-reader-text">Copy section</span><i class="fa fa-copy" aria-hidden="true"></i></div>
        <div class="postbox-button rename" alt="Rename"><span class="screen-reader-text">Rename section</span><i class="fa fa-pencil"></i></div>
        <h3 class="hndle title heading" <?php echo $handle_style; ?> >
            <span class="text"><?php echo anthrohack_check_meta_var($section, "section_title", ""); ?></span>
            <span class="disabled-label"> - Disabled </span>
        </h3>
        <input type="hidden" class="hidden-input" name="section_title" id="section_title" value="<?php echo $title;?>" >
        <input type="hidden" class="hidden-input" name="section_slug" id="section_slug" value="<?php echo $slug;?>" >
        <input type="hidden" class="hidden-input" name="section_order" id="section_order" value="<?php echo $order;?>" >
        <input type="hidden" class="hidden-input" name="section_disabled" id="section_disabled" value="<?php echo $disabled;?>" >
        <input type="hidden" class="hidden-input" name="section_closed" id="section_closed" value="<?php echo $closed;?>" >

            <div class="container">
                    <div class="section-slug"><?php echo $type; ?> slug = <span class="slug"><?php echo $slug; ?></slug></div> 
                    <div class="section-id"><?php echo $type; ?> ID# = <span class="number"><?php echo $id_number; ?></slug></div> 
                    <?php //render section settings
                    
                    foreach ($fields as $revision_field_array) {
                        echo anthrohack_render_meta_field($revision_field_array, $section);
                    } 
                ?>
                <div class="clearfix"></div>
            </div>
            
    </div><!-- .postbox --> 
<?php } //end section

//NOTE buttons() function is found in main plugin file (card_sort_plugin.php)

function anthrohack_render_meta_field( $atts , $anthrohack_stored_meta = False) {

    if($anthrohack_stored_meta === False){ //if content is not supplied in the params (ie: is not an existing Card)
        global $post; 
        $anthrohack_stored_meta = get_post_meta( $post->ID );
        $card = False;
        //set content to value stored in db or fallback to default
    }else{
        $card = True;
    }

    if(!array_key_exists("default_content", $atts))
       $atts['default_content'] = "";

    //handle optional params
    foreach(array("description", "default_content", "class") as $param){
        if(!array_key_exists( $param, $atts ))
            $atts[$param] = "";
    }

    //concat classes for field wrapper
    $classes = "";
    if(array_key_exists( "type", $atts ))
        $classes .= " ".$atts["type"];
    if(array_key_exists( "name", $atts ))
        $classes .= " "."field-".$atts["name"];
    if(array_key_exists( "required", $atts ))
        $classes .= " ".$atts["required"];
    if(array_key_exists( "class", $atts ))
        $classes .= " ".$atts["class"];
    if(array_key_exists( "width", $atts ))
        $classes .= " ".$atts["width"];

    //if this is a card, prefix the "name" and "id" params with the section title (to prevent js and formconflcits)
    if($card)
        // var_dump($anthrohack_stored_meta);
        $atts['name'] = $anthrohack_stored_meta["section_slug"] . "_" . $atts['name'];

    // var_dump($atts);

    if($atts["type"] == "slider"){
        if(array_key_exists( "range", $atts ) && is_array($atts["range"])){ 
            $data_maxmin = ' data-min="'.$atts["range"][0].'" data-max="'.$atts["range"][1].'"'; 
        }else{
            $data_maxmin = ' ERROR:must specify range for sliders';
        }
    }else{
        $data_maxmin = '';
    }

    //open wrapper div
    echo '<div  class="anthrohack_metabox_option '.$classes.'">';

    $content =  anthrohack_check_meta_var($anthrohack_stored_meta, $atts['name'], $atts['default_content']);

    switch ($atts["type"]) {

        case "text":
        case "slider":
        case "date": 
                $type = ($atts["type"] == "date")? "date" : "text";?>

                <label for="<?php echo $atts['name']; ?>" class="title"><strong><?php echo $atts['title']; ?></strong></label>
                <span class="option-description"><?php echo $atts['description']; ?></span>
                <input type="<?php echo $type; ?>" name="<?php echo $atts['name']; ?>" id="<?php echo $atts['name']; ?>" <?php echo $data_maxmin; ?> value="<?php echo $content; ?>" />

        <?php break;

        case "textarea": ?>

                <label for="<?php echo $atts['name']; ?>" class="title"><strong><?php echo $atts['title']; ?></strong></label>
                <span class="option-description"><?php echo $atts['description']; ?></span>
                <textarea class="field " rows="3" style="width:100%;" name="<?php echo $atts['name']; ?>" id="<?php echo $atts['name']; ?>" placeholder="Enter <?php echo $atts['name']; ?> here"><?php echo $content ?></textarea>

        <?php break;

        case "image": 
            $height = ($content != "")? "100" : "0"; ?>

            <label for="<?php echo $atts['name']; ?>" class="title"><strong><?php echo $atts['title']; ?></strong></label>
            <span class="option-description"><?php echo $atts['description']; ?></span>
            <div style="width:100px; height:<?php echo $height; ?>px; background: url(<?php echo $content; ?>) repeat;" class="preview-image" id="<?php echo $atts['name']; ?>_preview"></div>
            <input type="hidden" class="hidden-input" name="<?php echo $atts['name']; ?>" id="<?php echo $atts['name']; ?>" value="<?php echo $content; ?>" >
            <input type="button" id="<?php echo $atts['name']; ?>_visible" name="Upload" class="upload_image_button button-secondary" value="Attach Image" /><br>
            <input class="remove-image button-secondary" value="Remove Image" />
        
        <?php break;

        case "file_attachment": ?>

            <label for="<?php echo $atts['name']; ?>"><strong><?php echo $atts['title']; ?></strong></label>  
            <span class="option-description"><?php echo $atts['description']; ?></span>
            <input type="hidden" class="hidden-input" name="<?php echo $atts['name']; ?>" id="<?php echo $atts['name']; ?>" value="<?php echo $content; ?>" >
            <span id="<?php echo $atts['name']; ?>_filename" class="filename"><?php echo $content; ?></span>
            <input type="submit" id="<?php echo $atts['name']; ?>_visible" name="Upload" class="upload_image_button button-secondary" value="Attach File" /><br>
            <input class="remove-image button-secondary" value="Remove Attachment" />
        
        <?php break;
        
        case "color_selector": 
            global $background_colors; ?>

            <h4 class="title"><?php echo $atts['title']; ?></h4>
            <span class="option-description"><?php echo $atts['description']; ?></span>
            <div class="colors">
            <?php //echo var_dump($content); ?>
            <input class="anthrohack-color-picker" type="text" name="<?php echo $atts['name']; ?>" value="<?php echo $content; ?>">
            <p class="caption">Click a "Select a Color" or choose from the presets below.</p>
            <?php foreach ($background_colors as $key => $value) { 
                $color = ($value == "" || $value == "#fff")? "#333" : "#fff"; ?>
                <span class="color" style="background-color:<?php echo $value; ?>; color:<?php echo $color; ?>;">
                    <label class="anthrohack-row-title">
                        <input type="radio"  name="color_dummy" value="<?php echo $value; ?>" <?php if ($content == $value) echo 'checked'; ?> >
                        
                        <?php echo $key; ?>
                    </label>
                </span>
            <?php } ?>
            <div class="clearfix"></div>
            </div>
        <?php break;
        
        case "editor":
            if($card) $content =  base64_decode($content); ?>

            <h4 class="title"><?php echo $atts['title']; ?></h4>
            <span class="option-description"><?php echo $atts['description']; ?></span>

           <?php 
           $settings = array(
                "media_buttons" => true,
                "textarea_rows" => 20,
                "teeny" => false
            );
            wp_editor( $content, $atts['name'], $settings ); 
            
            break;

        case "select": ?>

            <label for="<?php echo $atts['name']; ?>" class="title"><strong><?php echo $atts['title']; ?></strong></label>
            <span class="option-description"><?php echo $atts['description']; ?></span>
            <div style="width:100px; height:0px; background-repeat: repeat; background-size: initial!important;" class="preview-image"></div>
            <?php if(array_key_exists("select_options",$atts) && is_array($atts["select_options"])){ ?>

                <select name="<?php echo $atts['name']; ?>" id="<?php echo $atts['name']; ?>" >
                   <?php foreach($atts["select_options"] as $value => $key){
                        //if "selected" field exists and matches current item...
                        if($content == $key || $content == strtolower($value)){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                        echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                    } ?>
                </select>

            <?php }else{ ?>
                <span>Error: Enter select items in anthrohack_metaboxes.php</span>
            <?php }

            break;

        case "checkbox": //var_dump($content); 
            // if( $atts['name'] == "logo_header") $content = "";
            if($content == "no" || $content == "") $content = "off";
            if($content == "on") $content = "yes"; ?>
            <label for="<?php echo $atts['name']; ?>" class="title"><strong><?php echo $atts['title']; ?></strong>
                <input type="checkbox" class="anthrohack_checkbox" id="<?php echo $atts['name']; ?>" <?php checked( $content, 'yes' ); ?> />
                <input type="hidden" id="<?php echo $atts['name']; ?>_hidden" class="anthrohack_checkbox-hidden" name="<?php echo $atts['name']; ?>" value="<?php echo $content; ?>" />

            </label>
            <span class="option-description"><?php echo $atts['description']; ?></span>


        <?php break;


    }//end switch
    echo '</div>'; //close anthrohack_metabox_option div
}

function anthrohack_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'anthrohack_nonce' ] ) && wp_verify_nonce( $_POST[ 'anthrohack_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    //save fields
    // this is an array of all the fields names to save. card fields (saved in json) are exempt
    $revision_field_array = array(
        "description",
        "constrained",
        "min_piles",
        "min_cards",
        "email",
        "cards_instructions",
        "anthrohack_questions",
        "anthrohack_cards",
        "anthrohack_piles",
        "card_text_alignment",
        "modal_title",
        "modal_subtitle",
        "modal_description",
        "thank_you_page",
        "notify_researcher",
        "notification_email",
    );

    foreach($revision_field_array as $field){
        if( isset( $_POST[ $field ] )){
            update_post_meta( $post_id, $field, $_POST[ $field ] );
        }
    }

    //checked by default
    $chx_field_array1 = Array("show_title", "show_sharing");
    foreach($chx_field_array1 as $chx_field1){

        // Checks for checkbox input and saves
        if(isset( $_POST[ $chx_field1 ])){
            if( $_POST[ $chx_field1] != 'on' && $_POST[ $chx_field1] != 'yes' ) {
                update_post_meta( $post_id, $chx_field1, '' );
            }else if( $_POST[ $chx_field1] == 'on' || $_POST[ $chx_field1] == 'yes' ) {
                update_post_meta( $post_id, $chx_field1, 'yes' );
            }else{
                update_post_meta( $post_id, $chx_field1, '' );
            }
        }else{
            update_post_meta( $post_id, $chx_field1, '' );
        }
    }

    //unchecked by default
    $chx_field_array2 = Array('anthrohack_hide_help', "fixed_bg", "show_logo", "transparent_header", "logo_header");
    foreach($chx_field_array2 as $chx_field2){
 
        // Checks for checkbox input and saves
        if( isset( $_POST[ $chx_field2 ] ) ) {
            update_post_meta( $post_id, $chx_field2, 'yes' );
        } else {
            update_post_meta( $post_id, $chx_field2, 'off' );
        }
    }
     
}
add_action( 'save_post', 'anthrohack_meta_save' );


function anthrohack_access_protected_param($obj, $prop) {
  $reflection = new ReflectionClass($obj);
  $property = $reflection->getProperty($prop);
  $property->setAccessible(true);
  return $property->getValue($obj);
}

