import '../css/admin-style.scss'

/* global Choices */

/**
 * @param {string} elementId
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
 * @param e
 * @param mediaUploader
 * @param {Element} inputField
 */
const handleUploadButtonClick = (e, mediaUploader, inputField) => {
  e.preventDefault()

  if (mediaUploader) {
    mediaUploader.open()
    return
  }

  mediaUploader = wp.media.frames.file_frame = wp.media({
    multiple: false,
  })

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
