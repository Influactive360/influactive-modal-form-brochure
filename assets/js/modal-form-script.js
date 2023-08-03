import '../css/modal-form-style.scss'

/* global grecaptcha, ajaxObject */

/**
 * Submit form data using AJAX request.
 *
 * @param {HTMLElement} messageDivParam - The message div element.
 * @param {HTMLFormElement} form - The form element to submit.
 * @param {File} file - The file to attach to the form data. Optional.
 * @param {string} recaptchaResponse - The reCAPTCHA response. Optional.
 */
const submitForm = (messageDivParam, form, file, recaptchaResponse) => {
  const xhr = new XMLHttpRequest()
  const formData = new FormData(form)
  const messageDiv = { ...messageDivParam } // Clone the object
  formData.append('action', 'send_email')

  if (recaptchaResponse) {
    formData.append('recaptcha_response', recaptchaResponse)
  }

  if (file) {
    formData.delete('brochure')
    formData.append('brochure', file)
  }

  xhr.open('POST', ajaxObject.ajaxurl, true)

  xhr.onload = function xhrOnload() {
    if (xhr.status === 200) {
      const response = JSON.parse(xhr.responseText)
      if (response.data) {
        messageDiv.textContent = response.data.message
        form.reset()
      } else {
        messageDiv.textContent = response.data.message
      }
    } else {
      messageDiv.textContent = 'An error occurred with the AJAX request'
    }
  }

  xhr.send(formData)
}

window.addEventListener('load', () => {
  const brochureLinks = document.querySelectorAll('a[href*="#brochure"]')
  const modalForm = document.querySelector('#modal-form')
  const modalFormClose = document.querySelector('#modal-form-close')
  let file // variable for storing the file URL

  // Check if modalForm is initially in 'block' display
  if (modalForm && getComputedStyle(modalForm).display === 'block') {
    document.body.style.overflow = 'hidden'
  }

  if (brochureLinks && modalForm && modalFormClose) {
    brochureLinks.forEach((element) => {
      element.addEventListener('click', (e) => {
        e.preventDefault()

        // Retrieve the full href including query parameters
        const href = element.getAttribute('href')

        // Check if href contains '?'
        if (href.includes('?')) {
          // Split the href on the '?' character to get the parameters
          const [queryString] = href.split('?')

          // Check if we have parameters
          if (queryString) {
            // Split the parameters on the '=' character to get the file URL
            const [key, value] = queryString.split('=')

            // Check if we have a file parameter
            if (key === 'file') {
              file = value
            }
          }
        }

        modalForm.style.display = 'block'
        document.body.style.overflow = 'hidden'
      })
    })

    modalFormClose.addEventListener('click', () => {
      modalForm.style.display = 'none'
      document.body.style.overflow = 'auto'
      document.body.style.overflowX = 'hidden'
    })

    window.addEventListener('click', (e) => {
      if (e.target === modalForm) {
        modalForm.style.display = 'none'
        document.body.style.overflow = 'auto'
        document.body.style.overflowX = 'hidden'
      }
    })

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        modalForm.style.display = 'none'
        document.body.style.overflow = 'auto'
        document.body.style.overflowX = 'hidden'
      }
    })
  }

  // on form submit
  const form = document.querySelector('#modal-form form')
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault()
      const recaptchaInput = form.querySelector('input[name="recaptchaSiteKey"]')
      const messageDiv = form.querySelector('.influactive-form-message')

      if (recaptchaInput && grecaptcha) {
        const recaptchaSiteKey = recaptchaInput.value
        grecaptcha.ready(() => {
          grecaptcha.execute(recaptchaSiteKey, { action: 'submit' }).then((token) => {
            submitForm(messageDiv, form, file, token)
            setTimeout(() => {
              messageDiv.textContent = ''
            }, 5000)
          })
        })
      } else {
        submitForm(messageDiv, form, file, null)
        setTimeout(() => {
          messageDiv.textContent = ''
        }, 5000)
      }
    })
  }
})
