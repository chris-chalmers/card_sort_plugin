<?php

//sets the options page item in the admin menu
function anthrohack_add_admin_menu(  ) { 
	$theme_name = get_bloginfo( 'name' );
	$anthrohack_settings = get_option( 'anthrohack_settings' );
	$icon_url = (isset ($anthrohack_settings["anthrohack_favicvon"]))? $anthrohack_settings["anthrohack_favicvon"] : "dashicons-admin-generic";	
	$theme_name = get_bloginfo( 'name' );

	add_submenu_page( 
		'edit.php?post_type=study', 
		'Study Submissions', 
		'Study Submissions', 
		'manage_options', 
		'anthrohack_submissions', 
		'anthrohack_submissions_page'
	);

	add_submenu_page( 
		'edit.php?post_type=study', 
        'Study Options',
        'Study Options',
		'manage_options', 
		'anthrohack_options', 
		'anthrohack_options_page',
        $icon_url
	);

}
add_action( 'admin_menu', 'anthrohack_add_admin_menu' );


function anthrohack_submissions_page(){ 
	$studies = anthrohack_get_studies(); 
	$sorts = anthrohack_get_sorts();

	?>
	<div id="submissions">

		<div class="submissions-header">
			<h2 class="title">Study Submissions</h2>

			<p class="instructions">
				Select the submissions below and click export to downlaod a CSV.
			</p>

			<div class="submission-header-item">
				<label for="select_all_sorts"><input type="checkbox" id="select_all_sorts"> Select All</label>
			</div>

			<div class="submission-header-item">
				<label for="studies_picker">Filter by study</label>
				<select id="studies_picker">
					<option value="all" >All studies</option>
					<?php foreach ($studies as $study) {
						echo '<option value="'.$study->ID.'" >'.$study->post_title.'</option>';
					} ?>
				</select>
			</div>

			<div class="submission-header-item">
				<button type="button" class="download button"><span class="dashicons dashicons-download"></span>Export selected</button>
			</div>

			<div class="submission-header-item">
				<button type="button" class="delete button"><span class="dashicons dashicons-trash"></span>Delete selected</button>
			</div>
		</div>

		<?php foreach ($sorts as $sort) { 
			$sort_meta = get_post_meta( $sort->ID );
			$study = get_post($sort_meta['study_id'][0]);
			$study_edit_link = get_site_url() . '/wp-admin/post.php?post='.$sort_meta['study_id'][0].'&action=edit';
			$study_cards = [];
			if(anthrohack_check_meta_var($sort_meta, 'piles')){
				$piles = json_decode($sort_meta['piles'][0]);
			}else{
				$piles = false;
			}

			if($piles){
			//var_dump($piles); ?>

			<div class="sort" data-id="<?php echo $sort->ID; ?>" data-study_id="<?php echo $sort_meta['study_id'][0]; ?>">		
				
				<h3 class="title sort-title"><?php echo $sort->post_title; ?></h3>
				<div class="submission-meta">

					<div class="submission-select">
						<label for="select_sort_<?php echo $sort->ID; ?>"><input type="checkbox" class="select-sort" id="select_sort_<?php echo $sort->ID; ?>"> Select</label>
					</div>

					<div class="date"><label>Submission date: </label><?php echo get_the_date( 'D M j' , $sort->ID) . " at " . get_the_time("", $sort->ID); ?></div>
					<div class="study"><label>Study: </label><a href="<?php echo $study_edit_link; ?>" target="_blank"><?php echo $study->post_title; ?></a></div>
				</div>

				<?php if(anthrohack_check_meta_var($sort_meta, 'questions')){
						$questions = json_decode($sort_meta['questions'][0]);
				}else{
					$questions = false;
				} 

				if($questions){ ?>

					<div class="submission-questions">
						<h4 class="title">Questions</h4>
						<table id="question-table" >
							<tr>
								<th>Question</th><th>Answer</th>
							</tr>
							<?php foreach ($questions as $question) { ?>
								<tr>
									<td><?php echo anthrohack_check_meta_var($question, 'question_text'); ?></td>
									<td><?php echo anthrohack_check_meta_var($question, 'answer'); ?></td>
								</tr>
							<?php } ?>
						</table>
					</div>

				<?php } //end questions?>
				
				<div class="submission-piles">
					<h4 class="title">Piles</h4>
					<table id="pile-table" >
						<tr>
							<th>Name</th><th>description</th><th>Card ID#s</th>
						</tr>
						<?php foreach ($piles as $pile) { 
							$card_ids = "";
							$first = true;
							foreach ($pile->cards as $card) {
								if(!$first)
									$card_ids .= ", ";
								$card_ids .= $card->id;

								//add card to study array
								$study_cards[] = array(
									'card_id' => $card->id,
									'card_title' => $card->card_title,
								);

								$first = false;
							}
							?>
							<tr>
								<td><?php echo $pile->pile_title ?></td>
								<td><?php echo $pile->sorter_notes ?></td>
								<td><?php echo $card_ids; ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>

				<div class="submission-cards">
					<h4 class="title">Cards</h4>
					<table id="card-table" >
						<tr>
							<th>ID #</th><th>Name</th>
						</tr>
						<?php 
						asort($study_cards);
						foreach ($study_cards as $card) { ?>
							<tr>
								<td><?php echo $card['card_id'] ?></td>
								<td><?php echo $card['card_title'] ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>

			</div>


		<?php }else{ ?>
				<br><br>No piles<br><br>
		<?php } //end questions
	}//end sorts?>

	</div>
<? }

function anthrohack_settings_init(  ) { 
	register_setting( 'anthrohackPluginPage', 'anthrohack_settings' );

	//add first section
	//layout is editable in function anthrohack_instructions_section_callback() below
	add_settings_section(
		'instructions', 
		'Instructions', 
		'anthrohack_instructions_section_callback', 
		'anthrohackPluginPage'
	);

	$anthrohack_options_sections = array(
		array(
			"section_name" => "Study defaults",
			"section_description" => "",			
			"section_slug" => "general",	
			"section_options" => array(
				(object) array(
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
		        (object) array(
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
		        (object) array(
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
		        (object) array(
		            "name" => "hero_image",
		            "title" => __( 'Default card background image', '_anthrohack_' ),
		            "type" => "image",
		            "description" => "This image will be used as a full-bleed background. It will appear behind the main color if transparency is set.",
		            "default_content" => "",
		            "class" => ''
		        ),
		        (object) array(
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
		        (object) array(
		            "name" => "background_color",
		            "title" => __( 'Default card background color', '_anthrohack_' ),
		            "type" => "color_selector",
		            "description" => "Optional - Choose a background color for this card. If no color selected, background image will be used.",
		            "default_content" => "",
		            "class" => ''
		        ),
		        (object) array(
		            "name" => "background_color2",
		            "title" => __( 'Default card background color 2', '_anthrohack_' ),
		            "type" => "color_selector",
		            "description" => "Optional - Choose a secondary overlay color to create a gradient. If selected, gradient will appear over background image.",
		            "default_content" => "",
		        ),
		        (object) array(
		            "name" => "gradient_angle",
		            "title" => __( 'Default gradient angle', '_anthrohack_' ),
		            "type" => "text",
		            "description" => 'Choose an angle 0 - 360, Zero being  vertical (Top -> Bottom)',
		            "default_content" => "",
		            "class" => ''
		        ),
		        (object) array(
		            "name" => "background_transparency",
		            "title" => __( 'Default background transparency (%)', '_anthrohack_' ),
		            "type" => "slider",
		            "range" => array(0,100),
		            "description" => "Choose the percentage transparency (0-100) for the background color of this card. Video or image will appear behind color.",
		            "default_content" => "100",
		            "class" => ''
		        ),
			),
		)
	);

	// var_dump(get_option( 'anthrohack_settings' ));
	if(is_array($anthrohack_options_sections)){
		foreach($anthrohack_options_sections as $section){

			add_settings_section(
				$section["section_slug"], 
				$section["section_name"], 
				'anthrohack_settings_section_callback', 
				'anthrohackPluginPage'
			);

			//$ar = array();
			$renderer = new anthrohack_Settings_Renderer;

			foreach($section["section_options"] as $option){
				$renderer->render($option, $section["section_slug"]);
			}
		}
	}
} //end settings init
add_action( 'admin_init', 'anthrohack_settings_init' );


//add stndard WP save button to each section
function anthrohack_instructions_section_callback($atts) { ?>
	<div class="settings-instructions">
		Write instructions here....
	</div>
<? }//end instructions


//add stndard WP save button to each section
function anthrohack_settings_section_callback($atts) { 
	submit_button();
}

class anthrohack_Settings_Renderer{

	public function render($option_array, $section_slug){
		//set up variables we'll pass to the callback
		$anthrohack_settings = get_option( 'anthrohack_settings' );  
		$id = $option_array->name;
		$content = ($anthrohack_settings && array_key_exists( $id, $anthrohack_settings ))? $anthrohack_settings[$id] : $option_array->default_content;	
		$args     = array (
            'id'	  => $id,
            'options' => $option_array,
            'content' => $content
        );

		// define callback function
		$callback = array ( $this, 'field_render_function' );

		add_settings_field( 
			$id, 
			__( $option_array->title, '_anthrohack_' ), 
			$callback, 
			'anthrohackPluginPage', 
			$section_slug,
			$args //these variables are passed to the callback
		);

	} //end render


	function field_render_function(array $args) { 

		$options   	= $args['options'];
	    $id     	= $args['id'];
	    $content   	= $args['content'];
	    $class 		= "";
	    $rows 		= "5";

	    //concat classes for field wrapper
	    $classes = $options->type;
	    if(property_exists( $options, "class"))
	        $classes .= " ".$options->class;
	    
	    //open field wrapper
		$render = '<div id="field_'.$options->name.'" class="anthrohack_metabox_option '.$classes.'">';
		if(array_key_exists( "description", $options ))
			$render .= "<div class='settings-description'>".$options->description."</div>";

		//parameters
		if(array_key_exists( "required", $options ) && $options->required)
			$class .= "required";
		if(array_key_exists( "class", $options ))
			$class .= " ".$options->class;
		if(array_key_exists( "rows", $options ))
			$rows = $options->rows;

		switch ($options->type) {
			case "editor":

				// $render .= '<div id="wp-anthrohack_settings_editor-wrap" class="wp-core-ui wp-editor-wrap html-active">';
				// $render .= '<link rel="stylesheet" id="dashicons-css" href="'.get_site_url().'/wp-includes/css/dashicons.min.css?ver=4.2.2" type="text/css" media="all">';
				// $render .= '<link rel="stylesheet" id="editor-buttons-css" href="'.get_site_url().'/wp-includes/css/editor.min.css?ver=4.2.2" type="text/css" media="all">';
				// $render .= '<div id="wp-anthrohack_settings-editor-container" class="wp-editor-container"><div id="qt_anthrohack_settings_toolbar" class="quicktags-toolbar"><input type="button" id="qt_anthrohack_settings_strong" class="ed_button button button-small" value="b"><input type="button" id="qt_anthrohack_settings_em" class="ed_button button button-small" value="i"><input type="button" id="qt_anthrohack_settings_link" class="ed_button button button-small" value="link"><input type="button" id="qt_anthrohack_settings_block" class="ed_button button button-small" value="b-quote"><input type="button" id="qt_anthrohack_settings_del" class="ed_button button button-small" value="del"><input type="button" id="qt_anthrohack_settings_ins" class="ed_button button button-small" value="ins"><input type="button" id="qt_anthrohack_settings_img" class="ed_button button button-small" value="img"><input type="button" id="qt_anthrohack_settings_ul" class="ed_button button button-small" value="ul"><input type="button" id="qt_anthrohack_settings_ol" class="ed_button button button-small" value="ol"><input type="button" id="qt_anthrohack_settings_li" class="ed_button button button-small" value="li"><input type="button" id="qt_anthrohack_settings_code" class="ed_button button button-small" value="code"><input type="button" id="qt_anthrohack_settings_more" class="ed_button button button-small" value="more"><input type="button" id="qt_anthrohack_settings_close" class="ed_button button button-small" title="Close all open tags" value="close tags"></div>';
				// $render .= '<textarea class="wp-editor-area" rows="4" cols="40" name="anthrohack_settings['.$id.']" id="anthrohack_settings-textarea">'.$content.'</textarea></div>';
				// $render .= '</div>';

				$settings = array(
					"media_buttons" => true,
					"textarea_rows" => 20,
					"teeny" => false
				);
				ob_start();
				wp_editor( base64_decode($content), $id, $settings );
				$render .= ob_get_clean();

			 	break;

			case "textarea":
				$render .= "<textarea cols='40' rows='".$rows."' class='".$class."' name='anthrohack_settings[".$id."]'>";
				$render .= $content; 
			 	$render .= "</textarea>";
			 	break;

			case "text":
			case "slider":
        	case "date": 
        		if($options->type == "slider"){
			        if(property_exists($options, "range" ) && is_array($options->range)){ 
			            $data_maxmin = ' data-min="'.$options->range[0].'" data-max="'.$options->range[1].'"'; 
			        }else{
			            $data_maxmin = ' ERROR:must specify range for sliders';
			        }
			    }else{
			        $data_maxmin = '';
			    }
				$type = ($options->type == "date")? "date" : "text";
				$render .= '<input type="'.$type.'" id="anthrohack_settings['.$id.']" name="anthrohack_settings['.$id.']" '.$data_maxmin.' value="'.$content.'">';
			 	break;


			case "select":
				if(array_key_exists( "select_options", $options ) && is_array($options->select_options)){

					$render .= '<select class="'.$class.'" name="anthrohack_settings['.$id.']" >';
					$font_picker = false;
					if(property_exists( $options, "class"))
						if($options->class != "")
							$font_picker =  strpos("font-picker", $options->class) !== False;
					
					foreach($options->select_options as $key=>$value){
						//if "selected" field exists and matches current item...
						$font_style = $font_picker? 'style="font-family='.$value.'"' : '';
						$selected = ($content == $value)? "selected":"";
						$render .= '<option '.$font_style.' value="'.$value.'" '.$selected.'>'.$value.'</option>';
				 	}
				 	$render .= '</select>';

				 	if($font_picker){
				 		$render .= '<p class="caption">Select a font from the presets above<br>Or copy the font family name from <a href="https://fonts.google.com">Google Fonts</a> and paste below.</p>';
				 		$render .= '<input class="font-field" style="font-family;'.$content.'" type="text" name="anthrohack_settings['.$id.']" value="'.$content.'">';
				 	}
			 	}
			 	break;

        	case "color_selector": 
            global $background_colors; 
            	// var_dump($content);
                $render .= '<input class="anthrohack-color-picker colorpicker_'.$id.'" type="text" name="anthrohack_settings['.$id.']" value="'.$content.'">';
            	$render .= '<p class="caption">Click a "Select a Color" or choose from the presets below.</p>';
	            foreach ($background_colors as $key => $value) { 
	                $color = ($value == "" || $value == "#fff")? "#333" : "#fff";
	                $render .= '<span class="color" style="background-color:'.$value.'; color:'.$color.';">';
	                $render .= '<label for="'.$options->name.'" class="anthrohack-row-title">';
	                	$checked = ($content == $value)? 'checked' : '';
	                    $render .= '<input type="radio" name="color_dummy" value="'.$value.'" '.$checked.'>';
	                    $render .= $key;
	                $render .= '</label></span>';
	            } 
			break;

			case "image":
				// var_dump($content);
				$height = ($content != "")? "100" : "0";
				$render .= '<div><div style="height:'.$height.'px; background: url(' . $content . '); background-repeat:no-repeat;" class="preview-image" id="'.$options->name.'_preview"></div>';
				$render .= '<span class="image_url">current image:' . $content . "</span>"; 
				$render .= '<input type="hidden" class="hidden-input" id="'.$id.'-input" name="anthrohack_settings['.$id.']" value="'.$content.'" >';
				$render .= '<input type="button" id="'.$id.'-button" name="Upload" class="upload_image_button button-secondary" value="Upload Image" />';
				$render .= '<input type="button" class="remove-image button-secondary" value="Remove Image" /></div>';
				break;

			case "checkbox":
				// var_dump($content);
				$render .= '<label for="'.$id.'-checkbox">';
				$render .= '<input type="checkbox" id="'.$id.'-checkbox" class="anthrohack_checkbox" '.(($content == "on" || $content == "yes")? "checked":"").' />';
				$render .= '<input type="hidden" id="'.$id.'-checkbox_hidden" class="anthrohack_checkbox-hidden" name="anthrohack_settings['.$id.']" value="'.$content.'" />';
				$render .= '&nbsp;'.$options->title.'</label>';

				break;

	 	}

	 	$render .= '</div>'; //close field wrapper
		echo $render;
	}

}

function anthrohack_options_page() { ?>
	<form class="anthrohack-admin-form" action='options.php' method='post'>		
		<?php $page = 'anthrohackPluginPage';
		settings_fields( 'anthrohackPluginPage' );
		do_settings_sections( 'anthrohackPluginPage' ); ?>
	</form>
<?php }