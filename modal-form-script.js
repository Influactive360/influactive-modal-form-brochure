window.addEventListener('load', function () {
  const brochureLinks = document.querySelectorAll('a[href="#brochure"]')
  const modalForm = document.querySelector('#modal-form')
  const modalFormClose = document.querySelector('#modal-form-close')

  if (brochureLinks && modalForm && modalFormClose) {
    brochureLinks.forEach(function (element) {
      element.addEventListener('click', function (e) {
        e.preventDefault()
        modalForm.style.display = 'block'
        document.body.style.overflow = 'hidden'
      })
    })

    modalFormClose.addEventListener('click', function () {
      modalForm.style.display = 'none'
      document.body.style.overflow = 'auto'
    })

    window.addEventListener('click', function (e) {
      if (e.target === modalForm) {
        modalForm.style.display = 'none'
        document.body.style.overflow = 'auto'
      }
    })
  }

  // on form submit
  const form = document.querySelector('#modal-form form')
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault()
      const formData = new FormData(form)

      let xhr = new XMLHttpRequest();
      xhr.open('POST', form.action, true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const html = xhr.responseText;
          console.log(html);
          modalForm.querySelector('.message').innerHTML = html;
        }
      };

      xhr.send(formData);
    })
  }
})
