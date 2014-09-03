/**
 * Callback to update a section's image upload
 */
function updateSectionImage(data, originator) {
	$('.image-preview').each(function(e) {
		if ($(this).data('type') == data.meta.imageType && $(this).data('section') == data.meta.section) {
			$('image-preview').html(data.html).removeClass('image-preview-empty');
		}
	});
}

$(document).ready(function() {

});