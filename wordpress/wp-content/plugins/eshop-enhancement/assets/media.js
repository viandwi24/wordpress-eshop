jQuery(document).ready(function($) {
	jQuery('input#eshop_enhancement_media_manager').click(function(e) {
		e.preventDefault();
		var image_frame;
		if(image_frame){
			image_frame.open();
		}
		// Define image_frame as wp.media object
		image_frame = wp.media({
			title: 'Select Media',
			multiple : false,
			library : {
				type : 'image',
			}
		});

		image_frame.on('close',function() {
			// On close, get selections and save to the hidden input
			// plus other AJAX stuff to refresh the image preview
			var selection =  image_frame.state().get('selection');
			var gallery_ids = new Array();
			var my_index = 0;
			selection.each(function(attachment) {
				gallery_ids[my_index] = attachment['id'];
				my_index++;
				jQuery('#shop_image').val(attachment.attributes.url);
				jQuery('#shop-preview-image').attr('src', attachment.attributes.url);
			});
			// var ids = gallery_ids.join(",");
			// jQuery('input#myprefix_image_id').val(ids);
			// Refresh_Image(ids);
		});

		image_frame.on('open',function() {
			// On open, get the id from the hidden input
			// and select the appropiate images in the media manager
			var selection =  image_frame.state().get('selection');
			const el = jQuery('input#shop_image');
			if (el.length > 0) {
				var ids = el.val().split(',');
				ids.forEach(function(id) {
					var attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				});
			}
		});
		image_frame.open();
	});
});