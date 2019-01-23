<?php
add_action( 'admin_menu', 'anthrohack_add_admin_menu' );
add_action( 'admin_init', 'anthrohack_settings_init' );

function anthrohack_settings_init(  ) { 
	register_setting( 'anthrohackPluginPage', 'anthrohack_settings' );

	$anthrohack_options_sections = array(
		array(
			"section_name" => "General settings",
			"section_description" => "",			
			"section_slug" => "general",	
			"section_options" => array(
				(object) array(
					"name" => "description",
					"title" => __( "Research porotocol", '_anthrohack_' ),
					"type" => "editor",
					"description" => "Description of the study, goals and methodology.",
					"default_content" => ""
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
}

//sets the options page item in the admin menu
function anthrohack_add_admin_menu(  ) { 
	$theme_name = get_bloginfo( 'name' );
	$saved_settings = get_option( 'anthrohack_settings' );
	$icon_url = (isset ($saved_settings["anthrohack_favicvon"]))? $saved_settings["anthrohack_favicvon"] : "dashicons-admin-generic";	
	$theme_name = get_bloginfo( 'name' );
	add_menu_page( 
        $theme_name . ' Options',
        $theme_name . ' Options',
		'manage_options', 
		'anthrohack_options', 
		'anthrohack_options_page',
        $icon_url,
		2 
	);
}

//add stndard WP save button to each section
function anthrohack_settings_section_callback($atts) { 
	submit_button();
}

class anthrohack_Settings_Renderer{

	public function render($option_array, $section_slug){
		//set up variables we'll pass to the callback
		$saved_settings = get_option( 'anthrohack_settings' );  
		$id = $option_array->name;
		$content = ($saved_settings && array_key_exists( $id, $saved_settings ))? $saved_settings[$id] : $option_array->default_content;	
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