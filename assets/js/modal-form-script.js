window.addEventListener('load', function () {
  const brochureLinks = document.querySelectorAll('a[href*="#brochure"]')
  const modalForm = document.querySelector('#modal-form')
  const modalFormClose = document.querySelector('#modal-form-close')
  let file // variable for storing the file URL

  if (brochureLinks && modalForm && modalFormClose) {
    brochureLinks.forEach(function (element) {
      element.addEventListener('click', function (e) {
        e.preventDefault()

        // Retrieve the full href including query parameters
        const href = element.getAttribute('href')

        // Check if href contains '?'
        if (href.includes('?')) {
          // Split the href on the '?' character to get the parameters
          const parts = href.split('?')

          // Check if we have parameters
          if (parts.length > 1) {
            // Split the parameters on the '=' character to get the file URL
            const params = parts[1].split('=')

            // Check if we have a file parameter
            if (params[0] === 'file') {
              file = params[1]
            }
          }
        }

        modalForm.style.display = 'block'
        document.body.style.overflow = 'hidden'
      })
    })

    modalFormClose.addEventListener('click', function () {
      modalForm.style.display = 'none'
      document.body.style.overflow = 'auto'
      document.body.style.overflowX = 'hidden'
    })

    window.addEventListener('click', function (e) {
      if (e.target === modalForm) {
        modalForm.style.display = 'none'
        document.body.style.overflow = 'auto'
        document.body.style.overflowX = 'hidden'
      }
    })
  }

  // on form submit
  const form = document.querySelector('#modal-form form')
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault()
      const formData = new FormData(form)

      // Add the file URL to the form data
      if (file) {
        formData.append('file', file)
      }

      const xhr = new XMLHttpRequest()
      xhr.open('POST', form.action, true)

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          modalForm.querySelector('.message').innerHTML = xhr.responseText
        }
      }

      xhr.send(formData)
    })
  }
})
