<?php 
$post_ID = $_GET["id"];
?>

//load jQuery if not loaded already :)
// if(!window.jQuery){
//    var script = document.createElement('script');
//    script.type = "text/javascript";
//    script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
//    document.getElementsByTagName('head')[0].appendChild(script);
// }

<?php //this //<script> tag is just to fool sublime into code highlighting ;) It's commented out to hide it from actual javascript parsing?>
//<script>
(function($){
	$(document).ready(function(){
		// console.log('anthrohack js ready');

		var me = document.currentScript;
		load_embed_form();
	});

		function load_embed_form(){
		if($("#anthrohack_<?php echo $post_ID; ?>_embed").length > 0){
			var wrapper_div = $("#anthrohack_<?php echo $post_ID; ?>_embed");

			$(wrapper_div).html('<img src="'+$(wrapper_div).data("img_url")+'" style="margin:auto; display: block; width:100px; height:auto;" /></br><span style="display:block; margin:auto; color:#fff; text-align: center;">Loading...</span>')
			var fetch_url = $(wrapper_div).data("form_url");
			// console.log(fetch_url);
			if(fetch_url != undefined){
				console.log(fetch_url);
				$.ajax({ 
					url: fetch_url, 
					type : "GET",
					success: function( data ) {
				  		
						console.log(data);
						// load css into head
						if(data.css_url != undefined && data.css_url != false){
							load_head_style(data.css_url);
						}
						// load js into head
						if(data.js_url != undefined && data.js_url != false){
							load_head_script(data.js_url);
						}

						//load form html into target div
						if(data.form_content != undefined && data.form_content != false){
							$(wrapper_div).html(htmlDecode(data.form_content));							
						}
					},
					error: function(data) {
						console.log( data );
					}
				});//end ajax
			}
		}
	} //end load form

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
