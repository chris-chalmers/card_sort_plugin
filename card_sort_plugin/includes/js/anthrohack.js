
//load jQuery if not loaded already :)
if(!window.jQuery){
   var script = document.createElement('script');
   script.type = "text/javascript";
   script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
   document.getElementsByTagName('head')[0].appendChild(script);
}

(function($){

	$(document).ready(function(){
		handle_accordions();
		
		
	}); //end doc ready

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