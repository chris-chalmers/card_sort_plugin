// console.log("funnel!");

if(!window.jQuery)
{
   var script = document.createElement('script');
   script.type = "text/javascript";
   script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
   document.getElementsByTagName('head')[0].appendChild(script);
}

(function($){

	$(document).ready(function(){

		load_anthrohack_form();
		window_resize();

		function window_resize(){
			adjust_form_height();
		}
		
	}); //end doc ready

	function load_anthrohack_form(){
		if($("#anthrohack_form").length > 0){
			var fetch_url = $("#anthrohack_form").data("form_url");
			// console.log(fetch_url);
			// var fetch_url = "https://interactivefunnelforms.com/wp-json/wp/v2/form_embed/" + $("#anthrohack_form").data("form_id");
			// var fetch_url = "https://futurewebstudio.com/wp-json/wp/v2/form_embed/" + $("#anthrohack_form").data("form_id");
			if(fetch_url != undefined){
				// console.log("fetch!");
				$.get( 
					fetch_url, 
					function( data ) {
				  		
						// console.log(data);
						// load css into head
						if(data.css_url != undefined && data.css_url != false){
							load_head_style(data.css_url);
						}

						if(data.anthrohack_styles != undefined && data.anthrohack_styles != false){
							$.each(data.anthrohack_styles, function(i, style_url){
								load_head_style(style_url);
							});
						} //end if

						//load form html into target div
						if(data.form_content != undefined && data.form_content != false){
							$("#anthrohack_form").html(htmlDecode(data.form_content));
							var input_type = $("#anthrohack_form").data("input_type");
							if(input_type == undefined)
								input_type = 'radio';
							setTimeout(function() {
								handle_form_inputs(input_type);
								adjust_form_height();
							}, 500);

							if(data.form_style != undefined){
								console.log(data.form_style);
								$("#anthrohack_form").prepend(data.form_style);
							}
							
						}

						if(data.target_url != undefined && data.target_url != false){
							$("#anthrohack_form").data('target_url', data.target_url);
						}

						//load caldera scripts
						if(data.anthrohack_scripts != undefined && data.anthrohack_scripts != false){
							//skip specified scripts
							// var skip_array = array( "editor", "pro");
							$.each(data.anthrohack_scripts, function(i, script_url){
								// if(! inArray(i, skip_array))
								load_head_script(script_url);
							});
						} //end if

					}
				);
			}
		}else{
			//run the form init anyway :p
			adjust_form_height();
			handle_form_inputs();
		}
	}

	function inArray(needle, haystack) {
	    var length = haystack.length;
	    for(var i = 0; i < length; i++) {
	        if(haystack[i] == needle) return true;
	    }
	    return false;
	}

	function load_head_style(css_url){
		var link = document.createElement( "link" );
		link.href = css_url;
		link.type = "text/css";
		link.rel = "stylesheet";
		document.getElementsByTagName( "head" )[0].appendChild( link );
		// console.log(link);
	}

	function load_head_script(js_url){
		var head = document.getElementsByTagName('head')[0];
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = js_url;
		head.appendChild(script);
		// console.log(link);
	}

	function adjust_form_height(){
		$.each($(".caldera-grid"), function(i, _form){
			var height = 0;
			$.each($(_form).find('.caldera-form-page'), function(j, _page){

				if(height < $(_page).height())
					height = $(_page).height();
			});
			$(_form).height(height);
			$("#anthrohack_form").height(height);
		});
	}

	/***********************************************************************************
	* Bind specific form interactions 
	* Trigger_type defines the type of input that triggers movement to the next page.
	* Submit sends form data to URL defined in form meta
	************************************************************************************/
	function handle_form_inputs(trigger_type){
		
		$.each($('.caldera_forms_form'), function(i, _form){
			
			//hide any submit button that's within the same 
			var $cf_row = $(_form).parents("div.row");
			if( $cf_row.length > 0){
				$cf_row.find("a.elButton").hide();
			}

			//first disable the next buttons' usual action
			$(_form).find('.cf-page-btn-next').unbind().click(function(e){
				e.preventDefault();
				e.stopImmediatePropagation();
				trigger_page_move(_form, this);	
			});

			

			//handle interactions
			switch(trigger_type){

				case "radio":
				default:
					/** When radio field is selected go to next page */
					$(_form).find('.radio label').on('click', function () {
						// console.log($(this).attr('checked'));
						trigger_page_move(_form, $(this).find("input"));				
					});
					break;

			}

			//handle sliders
			$.each($(".caldera-grid input[type=range]"), function(i, _this){

				$(_this).mouseup(function(){
					var $slider = $(_this);
				    var _parent = $slider.parents('.caldera-form-page');
				    if(_parent.length > 0){
				    	var $input = $(_parent[0]).find('input[type=number]');
				    	$input.val($slider.val());
				    }
				});
			});

			//handle inputs
			$.each($(".caldera-grid input[type=number]"), function(i, _this){

				$(_this).keyup(function(){
					var $input = $(_this);
				    var _parent = $input.parents('.caldera-form-page');
				    if(_parent.length > 0){
				    	var $slider = $(_parent[0]).find('input[type=range]');
				    	$slider.val($input.val());
				    }
				});
			});

			//handle states and counties
			$.each($(".caldera-grid select[name=state]"), function(i, _this){
				var _page = $(_this).parents('.caldera-form-page');
				var $others = $(_page[0]).find('select').not(_this);
				// console.log($others);

				$.each($others, function(i, _other){
					$.each($(_other).find("option"), function(j, _option){
						$(_option).attr("value", j);
					});
					$(_other).hide();
				});

				$(_this).change(function(){
					var $state = $(_this).val().toLowerCase();
					// console.log($state);
				    if(_page.length > 0){
				    	var $county = $(_page[0]).find('select[name=' + $state + ']');
				    	if($county.length > 0){
				    		$others.hide().removeClass("selected");
				    		$county.fadeIn().addClass("selected");
				    		$county.change(function(){
				    			$(_page[0]).find('input[type="submit"]').removeClass("disabled");
				    		});
				    	}
				    	
				    }
				});
			});

			//handle submit
			// $(_form).find( 'input[type="submit"]' ).addClass("disabled");
			$(_form).find( 'input[type="submit"]' ).click(function(e){
				// e.preventDefault();
				// e.stopImmediatePropagation();

				// console.log( $(this).is(".disabled") );

				// console.log("woot");
				// var target_url = $("#anthrohack_form").data("target_url");
				// if(target_url != undefined){
				// 	// console.log(target_url);
				// 	window.location.href = target_url;
				// }	

				trigger_page_move(_form, this);				
	
			});

			/** Hide next page buttons and submit buttons*/
			// $(_form).find('.cf-page-btn-next, input[type="submit"]').hide().attr('aria-hidden', true);

		}); //end each _form

	}//end handle_form_inputs

	function anthrohack_serialize_form_data(_form){
		var out = "";
		var is_new = true;
		if($(_form).length > 0){
			
			$.each($(_form).find("input, select"), function(i, _this){

				if($(_this).attr('type') == 'radio' && $(_this).attr('checked')){
					out += (is_new)? "?" : "&";
					out += $(_this).attr('name') + "=" + $(_this).val();
					is_new = false;
				}

				if($(_this).attr('type') == 'number'){
					out += (is_new)? "?" : "&";
					out += $(_this).attr('name') + "=" + $(_this).val();
					is_new = false;
				}

				if($(_this).is('select') && $(_this).attr('name') == "state"){
					out += (is_new)? "?" : "&";
					out += "state" + "=" + $(_this).val();	
					is_new = false;					
				}else if($(_this).is('select') && $(_this).is(".selected")){
					out += (is_new)? "?" : "&";
					out += "county" + "=" + $(_this).val();
					is_new = false;
				}
			});
		}
		return out;
	}

	function trigger_page_move(_form, input){
		//find curent page
		$current_page = $(input).parents('.caldera-form-page');

		if($current_page.length > 0){

			//find highest page
			var highest_page = 1;
			$(_form).find('.caldera-form-page' ).each( function () {
				highest_page = $(this).data('formpage');
			});

			var current_page_number = $current_page.data('formpage');
			// console.log(current_page_number);

			//check if we're on the last page
			if ( highest_page == current_page_number ) {

				$('progress[data-target="anthrohack_form_embed"]').attr("value", 100);

				$(_form).find( 'input[type="submit"]' ).unbind().trigger( 'click');
				//trigger any submit button that's within the same clickfunnels row
				var $cf_row = $(_form).parents("div.row");
				if( $cf_row.length > 0){
					var href = $cf_row.find("a.elButton").attr("href");
					console.log(href);
					if(href != undefined)
						window.location = href;
				}

			}else{

				//update progress bar to show % complete
			    new_val = (current_page_number / highest_page) * 100;
			    // console.log(new_val);
			    // console.log($('progress[data-target="anthrohack_form_embed"]'));

			    $('progress[data-target="anthrohack_form_embed"]').attr("value", new_val);

				anthrohack_animate_to_form_page(_form, current_page_number+1);
			}
		}
	}

	function anthrohack_animate_to_form_page(_form, to_page, time = 500){
		var $pages = $(_form).find('.caldera-form-page');
		// subtract 1 from 'to' page number to get proper zero based index
		to_index = to_page - 1;
		if($pages.length >= to_page){

			var $current_page = $(_form).find('.caldera-form-page[aria-hidden="false"]');
			
			// subtract 1 from 'from' page number to get proper zero based index
			var from_index = $current_page.data('formpage')-1;
			var page_width = $($pages[from_index]).width(); 

			// console.log("moving from " + from_index + " " + page_width + " to " + to_index);

			if(from_index < to_index){
				//move 'to' page to the left of 'from' page
				$($pages[to_index]).css('right', page_width/2).css('left', 'unset').css('opacity', '0').show().attr('aria-hidden', "false");
				// $($pages[to_index]).css('background-color', "red");

				$($pages[to_index]).animate({
			        right: 0,
			        opacity: 1, 
			    }, time);

				$($pages[from_index]).css('left', 0).css('right', 'unset');
				// $($pages[from_index]).css('background-color', "green");

			    $($pages[from_index]).animate({
			        left: page_width,
			        opacity: 0,
			    }, time).attr('aria-hidden', "true");

			    //handle visibility

			}else if(from_index > to_index){
				//move to the left
			}else{
				//do nothing
			}
		}
	}

	function htmlDecode(input){
	  var doc = new DOMParser().parseFromString(input, "text/html");
	  return doc.documentElement.textContent;
	}

	

})(jQuery)