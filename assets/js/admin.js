/* global Choices */
/* global wp */

document.addEventListener("DOMContentLoaded", function() {
	const selectElements = ['modal_form_pages', 'modal_form_posts'];

	selectElements.forEach(function(elementId) {
		const selectElement = document.getElementById(elementId);
		if (selectElement) {
			new Choices(selectElement, {
				removeItemButton: true,
				searchEnabled: true,
			});
		}
	});

	let mediaUploader
	const uploadButton = document.querySelector("#upload-button")
	const inputField = document.querySelector("#modal_form_file_select")

	uploadButton.addEventListener("click", function(e) {
		e.preventDefault()

		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open()
			return
		}

		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			multiple: false,
		})

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on("select", function() {
			const attachment = mediaUploader.state().get("selection").first().toJSON()
			inputField.value = attachment.id
		})

		// Open the uploader dialog
		mediaUploader.open()
	})
})
