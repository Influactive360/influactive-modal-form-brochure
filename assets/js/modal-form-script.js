import '../css/modal-form-style.scss'

/* global grecaptcha, ajaxObject */

/**
 * Sends a form submission via AJAX.
 *
 * @param {Element} messageDivParam - The element to display the result message.
 * @param {HTMLFormElement} form - The form element to submit.
 * @param {File} file - The file to be uploaded (optional).
 * @param {string} recaptchaResponse - The response from reCAPTCHA (optional).
 */
const submitForm = (messageDivParam, form, file, recaptchaResponse) => {
  let message
  const messageDiv = messageDivParam
  const xhr = new XMLHttpRequest()
  const formData = new FormData(form)
  formData.append('action', 'send_email')

  if (recaptchaResponse) {
    formData.append('recaptcha_response', recaptchaResponse)
  }

  if (file) {
    formData.delete('brochure')
    formData.append('brochure', file)
  }

  xhr.open('POST', ajaxObject.ajaxurl, true)

  xhr.onload = function xhrOnLoad() {
    if (xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText)
        if (response.data) {
          message = response.data.message
          form.reset()
        } else {
          message = 'An error occurred while processing the response'
        }
      } catch (error) {
        message = 'An error occurred while parsing the response'
      }
      messageDiv.textContent = message
    } else {
      message = 'An error occurred with the AJAX request'
      messageDiv.textContent = message
    }
  }

  xhr.onerror = () => {
    message = 'An error occurred while making the AJAX request'
    messageDiv.textContent = message
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
