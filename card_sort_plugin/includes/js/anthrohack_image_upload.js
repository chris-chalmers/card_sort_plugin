(function($){

$(document).ready(function(){

    //upload image v1
    upload_image_button('.upload_image_button');
    handle_checkboxes();
    do_video_thumbs();

});

function handle_checkboxes(){
	jQuery('.anthrohack_checkbox').click(function(){
		// console.log(jQuery(this).siblings('.anthrohack_hidden'));
	    jQuery(this).siblings('.anthrohack_hidden').val( this.checked ? 'True' : '' );
	});
}

function do_video_thumbs(){

	$.each($(".anthrohack_metabox_option.video"), function(i,_vid){ 
		$(_vid).find("input[name=video_id]").keyup(function(){
			var vid_id = $(this).val();
			if(vid_id.length > 0){

				$.ajax({
				    url: 'https://vimeo.com/api/v2/video/' + vid_id + '.json', 
				    data: {},
				    type: 'get',
				    error: function(XMLHttpRequest, textStatus, errorThrown){
				    	$(_vid).find(".preview-image").attr('src' , '').fadeOut();
				        console.log('status:' + XMLHttpRequest.status + ', status text: ' + XMLHttpRequest.statusText);
				    },
				    success: function(data){
				    	var vid_info = {};
				    	$.each(Array("thumbnail_small", "thumbnail_medium", "thumbnail_large"), function(){
				    		vid_info[this] = data[0][this];
				    	});
				    	console.log(vid_info);

				    	$(_vid).find("input#video_id_info").val(JSON.stringify(vid_info)).trigger('change');
						if(data[0]['thumbnail_small'].length > 0){
							$(_vid).find(".preview-image").attr('src' , data[0]['thumbnail_large']).fadeIn();
						}else if(data[0]['thumbnail_medium'].length > 0){
							$(_vid).find(".preview-image").attr('src' , data[0]['thumbnail_large']).fadeIn();
						}
				    }
				});

			}
		});
	});
}

function upload_image_button(selector){
	jQuery.each(jQuery(selector),function(){
		
		jQuery(this).siblings(".remove-image").click(function(){
			console.log(jQuery(this).siblings(".hidden-input").attr("value"));
			console.log(jQuery(this).siblings(".preview-image").attr("src"));

			jQuery(this).siblings(".hidden-input").attr("value", "").trigger('change');
			jQuery(this).siblings("img.preview-image").attr("src", "");
			jQuery(this).siblings(".preview-image").css('background', 'url()').css("height", "0");
			jQuery(this).siblings(".filename").html("");
    	});

		jQuery(this).click(function(event){
			event.preventDefault();
			//get input and image placeholder id's
			var inputID = jQuery(this).siblings(".hidden-input").attr("id");
			var imageID = jQuery(this).siblings(".preview-image").attr("id");
			var filenameID = jQuery(this).siblings(".filename").attr("id");
			console.log(inputID);
			console.log(imageID);

		    // If the media frame already exists, reopen it.
		    if ( file_frame ) {
		      file_frame.open();
		      return;
		    }

		    // Create the media frame.
		    var file_frame = wp.media.frames.file_frame = wp.media({
		      title: "Select Image",//jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: "Choose", //jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: false  // Set to true to allow multiple files to be selected
		    });

		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {

		      	// We set multiple to false so only get one image from the uploader
		      	attachment = file_frame.state().get('selection').first().toJSON();
		      	// console.log(attachment.url);

			    jQuery("#"+inputID).val(attachment.url).trigger('change');
		        // jQuery("img#"+imageID).attr('src',attachment.url);
		        jQuery("#"+imageID).css('background', 'url('+attachment.url+')').css("height", "100px");
		        jQuery("#"+filenameID).html(attachment.url);
		        // console.log( jQuery("image#"+imageID));
		    });

		    // Finally, open the modal
		    file_frame.open();
		});
	});
}

})(jQuery)