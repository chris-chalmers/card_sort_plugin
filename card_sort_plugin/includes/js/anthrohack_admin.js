(function($){
	var select_types = ".field-hero_type select, .field-video_type select, .field-section_type select, .field-columns select, .field-grid_type select";

	$(document).ready(function(){

		handle_color_picker();
		handle_sliders();
		handle_checkboxes();
		
	}); //end doc ready

	/**
	 * Calls the given callback function n times with the given interval. 
	 * To brealk out early, make the callback return false.
	 */
	function wait( times, interval, callback ) {
		setTimeout( function() {

			if ( times-- ) {
				wait( times, interval, callback );
			} else if ( callback ) {
				callback();
			}
		}, interval );
	}

	function handle_checkboxes(){
		$.each($(".anthrohack_checkbox"), function(i, _this){
			var id = "#" + $(_this).attr("id") + "_hidden";
			$(_this).change(function(){
				if($(_this).prop("checked")){
					$(id).val("on");
				}else{
					$(id).val("off");
				}
			});
		});
	}

	function strip_whitespace(str){
		return str.replace(/(^\s+|\s+$)/g,'');
	}
	//copy to clipboard
	//thank you https://hackernoon.com/copying-text-to-clipboard-with-javascript-df4d4988697f
	function copy_text_to_clipboard(str){
		var el = document.createElement('textarea');
		el.value = str;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		alert('copied!');
	}

	function handle_color_picker(){
		$(".anthrohack-color-picker").wpColorPicker();

		$.each($(".color-selector"), function(i, _this){
			$(_this).find("input[type=radio]").change(function(_that){
				// console.log($(_that).val());
				$(_this).find("input[type=hidden]").val($(_that).val());
			});
		});

		// trigger colorpicker and vice versa
		$(".anthrohack_metabox_option.color_selector input[type=radio]").change(function(){
			var _parent = $(this).parents(".anthrohack_metabox_option");
			// console.log($(_parent));
			$(_parent).find(".anthrohack-color-picker").val($(this).val());
			$(_parent).find(".anthrohack-color-picker").change();
		});
	}

	function handle_sliders(){
		$.each($(".anthrohack_metabox_option.slider input"), function(i, _this){
			$(_this).bootstrapSlider({
				"min": $(_this).data("min"),
				"max":$(_this).data("max"),
				"value": parseFloat($(_this).val())
			});
		});
	}

	function add_validation(){
		//add required class
		if($("p.anthrohack_metabox_option.required").length > 0){
			$.each($("p.anthrohack_metabox_option.required"), function(i, _this){
				$(_this).find("input, select").addClass("required");
			});
		}
		$('input[name="post_title"]').addClass("required");
		$("#post").validate();
	}

})(jQuery)