(function($){
	var select_types = ".field-hero_type select, .field-video_type select, .field-section_type select, .field-columns select, .field-grid_type select";

	$(document).ready(function(){

		handle_draggable_sections('questions');
		handle_draggable_sections('cards');
		handle_color_picker();
		// handle_sliders();
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

	function handle_draggable_sections(section){
		//function to handle sortable page layout sections
		var section_id = "#anthrohack_study_" + section; 
		var template_id = "#" + section + "_template"; 
		var fields_id = "#anthrohack_" + section + "_fields"; 
		var input_id = "#anthrohack_" + section; //the ID of the hidden input where the json will be stored

		 $(fields_id).sortable({
			 opacity: 0.6,
			 revert: true,
			 cursor: 'move',
			 handle: '.hndle',
			 update: function(){
				reorder_sections();
			 }
		 });

		//bind fields in existing sections 
		 $.each($(fields_id).find(".layout-section"), function(i,_this){
			// bind_section(_this);
		 });

		 // button to create new layout sections
		 $.each($(section_id).find("a.add-section.button"), function(i,_this){
			$(_this).click(function(){

				var title = prompt("choose a title");
				var slug = title.replace(/\s+/g, '_').replace(/['!"#$%&\\'()\*+,\-\.\/:;<=>?@\[\\\]\^`{|}~']/g,"").toLowerCase();
				if (slug === null || slug.trim() == "") {
					return; //break out of the function early
				}else{
					// console.log(check_slug_against_exising_sections(slug, fields_id));
					if(check_slug_against_exising_sections(slug, fields_id) == true){
						alert("That title is already in use");
						return; //break out of the function early
					}

					var target = $(fields_id);
					var post_id = $(target).data("id");
					var order = (i == 0)? "0" : $(target).find(".postbox").length; //order at the beginning or end depending on button loc
					$(target).find(".note").remove();
					$(section_id).find(".buttons").removeClass("hidden");

					var old_template = $(template_id);

					//disable template's tinymce editors (so can be copied)
					$.each($(old_template).find(".anthrohack_metabox_option.editor"), function(i, _editor){
						var ed_id = String($(_editor).find("textarea").attr("id"));
						tinymce.EditorManager.execCommand('mceRemoveEditor',true, ed_id);
					});

					//add new section from template
					var template = old_template.clone(true, true);

					// console.log(template);

					if(i == 0){
						$(target).prepend(template);
					}else{
						$(target).append(template);
					}
					
					//prepend section name to template fields (anti-ambiguety)
					$(template).attr("id", slug);
					$(template).find("input#section_order").val(order);
					$(template).find(".hndle.title .text").html(title);
					$(template).find("input#section_title").val(title);
					$(template).find("input#section_slug").val(slug);
					$(template).find(".section-slug .slug").html(slug);

					//update all field names to new section name
					$.each($(template).find("input, select, textarea"), function(i, _element){
						
						var oldname = $(_element).attr("name");
						if(oldname != undefined)
							$(_element).attr("name", oldname.replace("template", slug));
						var oldname = $(_element).attr("id");
						if(oldname != undefined)
							$(_element).attr("id", oldname.replace("template", slug));
					});

					//destroy and re-build template sliders 
					$.each($(template).find(".anthrohack_metabox_option.slider"), function(i, _this){
						
						var name = $(_this).find("input[type=text]").attr("name");
						var title = $(_this).find("label").text().trim();
						var desc = $(_this).find("span.option-description").text().trim();
						var value = $(_this).find("input[type=text]").val();
						var min = $(_this).find("input[type=text]").data("min");
						var max = $(_this).find("input[type=text]").data("max");

						var new_slider = '<label for="'+name+'" class="title"><strong>'+title+'</strong></label>' 
										+ '<span class="option-description">'+desc+'</span>'
										+ '<input type="text" name="'+name+'" id="'+name+'" value="'+value+'" />';

						$(_this).html(new_slider);
						$(_this).find("input[type=text]").bootstrapSlider({
							"min": min,
							"max":max,
							"value": parseFloat(value)
						});
						
					});
					
					//fix ids in media buttons in new editor
					$.each($(template).find(".anthrohack_metabox_option.editor"), function(i, _editor){
						// $(_editor).find("#wp-template_section_content-media-buttons").remove();
						//init new editors
						var ed_id = String($(_editor).find("textarea").attr("id"));
						tinymce.EditorManager.execCommand('mceAddEditor',true, ed_id);
						quicktags({id : ed_id});
					});
					bind_page_section($(template));

					//re-enable template's editors (so can be copied)
					$.each($(old_template).find(".anthrohack_metabox_option.editor"), function(i, _editor){
						var ed_id = String($(_editor).find("textarea").attr("id"));
						tinymce.EditorManager.execCommand('mceAddEditor',true, ed_id);
					});

					//init colorpicker
					$(template).find(".anthrohack-color-picker").wpColorPicker({
						change : function(){
							update_layout_section_json(input_id);
						}
					});

					//update json field with new section
					// console.log("new section");
					update_layout_section_json(input_id);
				}
				
			});
		 });
	}//end handle_draggable_sections

	function bind_page_section(_this, fields_id){
		// console.log(_this);
	}//end bind_page_section

	function reorder_sections(){
		$.each($(".section-wrap"), function(i, _this){
			var sections = $(_this).find(".layout-section");
			$.each(sections, function(j,_section){
				console.log( $(_section).attr("id") + j );
				$(_section).find("input#section_order").val(j); 
			});
		});
		console.log("reordered sections");
	}

	//update layoutsections json in hidden field\
	function update_layout_section_json(section_id){
	// 	var json_all = [];

	// 	$.each($(section_id + "_fields .postbox"), function(i, _pb){

	// 		// console.log(i);
	// 		//push serialized section fields to array of all sections
	// 		var section_serialized = $(_pb).find("select, textarea, input").serializeArray();
	// 		var section_json = serialized_to_json(section_serialized);
			
	// 		//checkbox helper
	// 		var checkbox_array = $(_pb).find("input[type=checkbox]");
	// 		$.each(checkbox_array, function(i, _this){
	// 			if(section_json[_this.id] == undefined)
	// 				section_json[_this.id] = "no";
	// 		});

	// 		//colorbox helper
	// 		var color_array = $(_pb).find("input.anthrohack-color-picker");
	// 		$.each(color_array, function(i, _this){
	// 			// console.log(typeof $(_this).val());
	// 			if(typeof $(_this).val() == "string"){
	// 				var cleaned_content = $(_this).val().replace(/'/g, "").replace(/"/g, "").trim();
	// 				section_json[$(_this).attr("id")] = cleaned_content;
	// 			}
	// 		});

	// 		//open/closed helper
	// 		if($(_pb).is(".closed")){
	// 			section_json["section_closed"] = "closed";
	// 		}else{
	// 			section_json["section_closed"] = "open";
	// 		}

	// 		//tinymce editor helper
	// 		var editor_array = $(_pb).find(".anthrohack_metabox_option.editor");
	// 		$.each(editor_array, function(i, _this){
	// 			var ed_id = $(_this).find("textarea").attr("id");
	// 			if(tinymce.get(ed_id) != undefined){
	// 				var esc_content = Base64.encode(tinymce.get(ed_id).getContent());
	// 				section_json[ed_id] = esc_content;
	// 			}
	// 		});
	// 		section_json['section_title'] = $(_pb).find(".hndle.title .text").text();

	// 		json_all.push(section_json);
	// 	});
	// 	// console.log(json_all);

	// 	// update hidden field with stringified array
	// 	$("input" + section_id).val(JSON.stringify(json_all));
	} //end update section json

	function check_slug_against_exising_sections(slug, _parent){
		//returns true if slug exists in existing sections, false of not
		var sections = $(_parent).find(".layout-sections");
		var result = false;
		if(_parent == undefined) _parent = "";

		//first check if slug matches parent ID - we are allowed to change back!
		if($(_parent).find("#section_slug").val() != slug){
			//if not, check through sections for previous slug use
			if(sections.length > 0 || sections.val() == ""){
				try {
					var sections_json =  JSON.parse(sections.val());
				} catch (e) {
					result =  false;
				}

				$.each(sections_json, function(i,_section){
					if(_section["section_slug"] === slug){
						result =  true;
					}				
				});
			}
		}
		//if none of the other conditions are met:
		return result;
	} 

	function serialized_to_json(serialized){
		var indexed_array = {};
		$.map(serialized, function(n, i){
			indexed_array[n['name']] = n['value'];
		});
		return indexed_array;
	}

	function isJson(item) { //thank you http://stackoverflow.com/questions/9804777/how-to-test-if-a-string-is-json-or-not
		item = typeof item !== "string"
			? JSON.stringify(item)
			: item;

		try {
			item = JSON.parse(item);
		} catch (e) {
			return false;
		}

		if (typeof item === "object" && item !== null) {
			return item;
		}

		return false;
	}

	/**
	*
	*  Base64 encode / decode
	*  http://www.webtoolkit.info/
	* thank you https://www.coditty.com/code/utf-base64-encode-in-php-and-decode-in-javascript
	**/

	var Base64 = {

		// private property
		_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

		// public method for encoding
		encode : function (input) {
		  var output = "";
		  var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		  var i = 0;

		  input = Base64._utf8_encode(input);

		  while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
			  enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
			  enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		  }

		  return output;
		},

		// public method for decoding
		decode : function (input) {
		  var output = "";
		  var chr1, chr2, chr3;
		  var enc1, enc2, enc3, enc4;
		  var i = 0;

		  input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		  while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
			  output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
			  output = output + String.fromCharCode(chr3);
			}

		  }

		  output = Base64._utf8_decode(output);

		  return output;

		},

		// private method for UTF-8 encoding
		_utf8_encode : function (string) {
		  string = string.replace(/\r\n/g,"\n");
		  var utftext = "";

		  for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
			  utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
			  utftext += String.fromCharCode((c >> 6) | 192);
			  utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
			  utftext += String.fromCharCode((c >> 12) | 224);
			  utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			  utftext += String.fromCharCode((c & 63) | 128);
			}

		  }

		  return utftext;
		},

		// private method for UTF-8 decoding
		_utf8_decode : function (utftext) {
		  var string = "";
		  var i = 0;
		  var c = c1 = c2 = 0;

		  while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
			  string += String.fromCharCode(c);
			  i++;
			}
			else if((c > 191) && (c < 224)) {
			  c2 = utftext.charCodeAt(i+1);
			  string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			  i += 2;
			}
			else {
			  c2 = utftext.charCodeAt(i+1);
			  c3 = utftext.charCodeAt(i+2);
			  string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			  i += 3;
			}

		  }

		  return string;
		}

	} //end BASE64

	function ucwords(str){
		str = str.split(" ");

		for (var i = 0, x = str.length; i < x; i++) {
			str[i] = str[i][0].toUpperCase() + str[i].substr(1);
		}

		return str.join(" ");
	}


})(jQuery)