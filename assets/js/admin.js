import '../css/admin-style.scss'

/* global Choices, wp */

/**
 * Initializes the Choices library for a given HTML select element.
 *
 * @param {string} elementId - The ID of the HTML select element.
 */
const initializeChoicesFor = (elementId) => {
  const selectElement = document.getElementById(elementId)
  if (selectElement && typeof Choices !== 'undefined') {
    new Choices(selectElement, {
      removeItemButton: true,
      searchEnabled: true,
    })
  }
}

/**
 * Function to handle upload button click event.
 *
 * @param {Object} e - The event object.
 * @param {Object} mediaParam - The media uploader object.
 * @param {Object} inputFieldParam - The input field object.
 */
const handleUploadButtonClick = (e, mediaParam, inputFieldParam) => {
  e.preventDefault()
  let mediaUploader = mediaParam
  const inputField = { ...inputFieldParam }

  if (mediaUploader) {
    mediaUploader.open()
    return
  }

  const media = wp.media({
    multiple: false,
  })

  mediaUploader = media

  wp.media.frames.file_frame = media

  mediaUploader.on('select', () => {
    const attachment = mediaUploader.state().get('selection').first().toJSON()
    inputField.value = attachment.id
  })

  mediaUploader.open()
}

document.addEventListener('DOMContentLoaded', () => {
  const selectElements = ['modal_form_pages', 'modal_form_posts']

  selectElements.forEach(initializeChoicesFor)

  let mediaUploader

  if (typeof wp !== 'undefined' && wp.media !== 'undefined') {
    const uploadButton = document.querySelector('#upload-button')
    const inputField = document.querySelector('#modal_form_file_select')

    if (uploadButton !== null && inputField !== null) {
      uploadButton.addEventListener('click', (e) => handleUploadButtonClick(e, mediaUploader, inputField))
    }
  }
})
