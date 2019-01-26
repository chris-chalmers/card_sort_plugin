
//load jQuery if not loaded already :)
// if(!window.jQuery){
//    var script = document.createElement('script');
//    script.type = "text/javascript";
//    script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
//    document.getElementsByTagName('head')[0].appendChild(script);
// }

/* Update Sort Items 
** this function is in the global scope and called both here and in anthrohack_draggable.js */
var anthrohack_update_sort_items;
/*/*/

(function($){

	$(document).ready(function(){
		// console.log("ready");
		
		handle_accordions();
		bind_buttons();
		window_resize();

		$(window).scroll(function(){
		});

		$(window).resize(function(){
			window_resize();
		});
		
	}); //end doc ready

	function window_resize(){
		resize_modal();
	}

	//this function is in the global scope and called both here and in anthrohack_draggable.js
	//gathers all the card sort data into a single json as well as updating the pile descriptions in the modal
	anthrohack_update_sort_items = function(isreturn){
		var piles = [];
		$.each($(".board-column.pile"), function(i, _pile){

			var cards = []; 
			$.each($(_pile).find(".board-item.card"), function(j, _card){
				// push card object to cards array
				cards.push({
					slug: $(_card).attr("id"),
					id_number: $(_card).data("id"),
					title: $(_card).find(".title").text(),
				});
			}); //end each pile > card

			//push pile object to piles array
			piles.push({
				slug: $(_pile).attr("id"),
				id_number: $(_pile).data("id"),
				title: $(_pile).find(".title").text(),
				cards: cards,
			});

		}); //end each pile

		//concat data
		var sort_data = {
			study_id: $("#card_sort_study").data('study_id'),
			study_slug: $("#card_sort_study").data('study_slug'),
			piles: piles,
		};

		console.log(sort_data);
		if(isreturn){

			//add questions and pile description data
			var questions = [];
			$.each($(".modal-questions .question"), function(i, _question){

				//push question object to questions array
				questions.push({
					slug: $(_question).attr("id"),
					id_number: $(_question).data("id"),
					title: $(_question).find(".description").text(),
					answer: $(_question).find(".description").val(),
				});

			}); //end each question
			sort_data.questions = questions;

			$.each($(".modal-questions .question"), function(i, _question){

				//push question object to questions array
				questions.push({
					slug: $(_question).attr("id"),
					id_number: $(_question).data("id"),
					title: $(_question).find(".description").text(),
					answer: $(_question).find(".description").val(),
				});

			}); //end each question

			return sort_data;
		}else{
			//if not return, update modal piles
			$.each(sort_data.piles, function(i, _pile){

			});
		}
	} //end update sort items

	function resize_modal(){
		$("#study_modal").width($(window).width()).height(window.innerHeight);
		$("#study_modal .modal-dialog").css("max-height", (window.innerHeight - 100) );
	}

	function bind_buttons(){

		//bind modal 
		$("#study_modal").find(".close-button, .cancel, #study_modal:not(.modal-dialog)").click(function(){
			$("#study_modal").fadeOut();
		});

		//submit buttons
		$(".study-finished").click(function(e){
			$("#study_modal").fadeIn();
		});

		$(".study-submit").click(function(e){
			e.preventDefault();
			e.stopImmediatePropagation();
		
			//validate study
			//required questions
			//number of cards per pile
			var sort_data = anthrohack_update_sort_items();

			// send via ajax
			// This does the ajax request
		    $.ajax({
		        url: anthrohack_ajax_object.ajax_url, // or example_ajax_obj.ajaxurl if using on frontend
		        data: {
		            'action': 'save_sort',
		            'data' : sort_data,
		        },
		        success:function(data) {
		            // This outputs the result of the ajax request
		            console.log(data);
		        },
		        error: function(errorThrown){
		            console.log(errorThrown);
		        }
		    });

			// confirmation
		});
	}

	function handle_accordions(){
		if($(".accordion-wrapper").length > 0){
			$.each($(".accordion-wrapper"), function(i, _this){
				$(_this).find(".accordion-btn").click(function(e){
					e.preventDefault();
					e.stopImmediatePropagation();

					if($(_this).hasClass('collapsed')){
						$(_this).removeClass('collapsed');
					}else{
						$(_this).addClass('collapsed');
					}
					
				});
			});
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

	function htmlDecode(input){
	  var doc = new DOMParser().parseFromString(input, "text/html");
	  return doc.documentElement.textContent;
	}

})(jQuery)