
//load jQuery if not loaded already :)
// if(!window.jQuery){
//    var script = document.createElement('script');
//    script.type = "text/javascript";
//    script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
//    document.getElementsByTagName('head')[0].appendChild(script);
// }

/* Update Sort Items 
** this function is in the global scope and called both here and in anthrohack_draggable.js */
var anthrohack_update_sort_items, anthrohack_add_item_to_grid;
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
	anthrohack_update_sort_items = function(is_final){
		var piles = [];
		$.each($(".board-column.pile:not(#pile_template)"), function(i, _pile){

			var cards = []; 
			$.each($(_pile).find(".board-item.card"), function(j, _card){
				// push card object to cards array
				var card_title = $(_card).find(".title").text();

				cards.push({
					id: $(_card).data("id"),
					slug: $(_card).attr("id"),
					card_title: card_title,
				});
			}); //end each pile > card

			var modal_pile = $(".modal-piles .pile[data-id=" + $(_pile).data("id") + "]");
			var pile_title = $(modal_pile).find(".title span").text();

			//push pile object to piles array
			piles.push({
				id: $(_pile).data("id"),
				slug: $(_pile).attr("id"),
				pile_title: pile_title,
				sorter_notes: $(modal_pile).find(".sorter_notes").val(),
				cards: cards,
			});

		}); //end each pile

		//concat data
		var sort_data = {
			study_id: $("#card_sort_study").data('study_id'),
			study_slug: $("#card_sort_study").data('study_slug'),
			piles: piles,
		};		

		if(is_final){

			//add questions and pile description data
			var questions = [];
			$.each($(".modal-questions .question"), function(i, _question){

				//push question object to questions array
				questions.push({
					id: $(_question).data("id"),
					slug: $(_question).attr("id"),
					answer: $(_question).find(".answer").val(),
				});

			}); //end each question
			sort_data.questions = questions;

			// console.log(sort_data);
			return sort_data;

		}else{

			//use sort data to update modal piles
			console.log(sort_data);
			$.each(sort_data.piles, function(i, _pile){
				
				//find pile in modal
				var card_list = $(".modal-piles .pile[data-id="+_pile['id']+"] ul");
				if(card_list.length > 0){

					//first empty card list
					$(card_list).html('');

					//then add all cards from column
					$.each(_pile.cards, function(j , _card){
						$(card_list).append('<li>' + _card['card_title'] + '</ul>');
					});

				}
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

		$("#study_modal").click(function(e){
		   if(e.target == this){ // only if the target itself has been clicked
		       $("#study_modal").fadeOut();
		   }
		});

		//add piles
		$(".board-column.add-pile .btn").click(function(){
			
			var pile_id = $(".board .board-column.pile").length;
			if(undefined == pile_id)
				pile_id	= 0;

			//add new pile to board from template
			var old_template = $("#pile_template");
			var new_pile = old_template.clone(true, true);
			$(new_pile).insertBefore( ".board-column.add-pile" );
			$(new_pile).attr("id", "pile-" + pile_id);
			$(new_pile).attr('data-id', pile_id);
			$(new_pile).find(".title span").html("Pile " + pile_id);
			$(new_pile).fadeIn();

			//add pile to muuli board
			var index = $(".board-column.add-pile").index();
			anthrohack_add_item_to_grid($(new_pile)[0], $(new_pile).find('.board-column-content')[0], index-1);

			//add new modal pile to modal from template
			var old_template = $("#modal_pile_template");
			var modal_template = old_template.clone(true, true);
			$(modal_template).appendTo(".modal-piles");
			$(modal_template).attr("id", "pile-" + pile_id);
			$(modal_template).attr('data-id', pile_id);
			$(modal_template).find(".title span").html("Pile " + pile_id);
			$(modal_template).show();


			//bind remove button 
			$(new_pile).find(".remove-pile").click(function(){

				if($(new_pile).find(".board-item.card").length == 0){
					var pile_id = $(new_pile).data("id");

					//remove element from muuri grid
					anthrohack_remove_item_from_grid($(new_pile)[0]);

					//remove pile element from modal
					$(".modal-piles").find(".pile[data-id=" + pile_id + "]").remove();

				}else{
					alert("Piles must be empty of cards before being removed.")
				}

			});
			

			console.log("added new pile with ID: " + pile_id);

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

			var sort_data = anthrohack_update_sort_items(true);

			if(sort_data && anthrohack_ajax_object != undefined){

				// send via ajax
			    $.ajax({
			        url: anthrohack_ajax_object.ajax_url,
			        data: {
			            'action': 'save_sort',
			            'data' : sort_data,
			        },
			        success:function(response) {
			            // This outputs the result of the ajax request
			            // console.log("SUCCESS");
			            console.log(response);
			        },
			        error: function(errorThrown){
			        	// console.log("ERROR");
			            console.log(errorThrown);
			        }
			    });
			}

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