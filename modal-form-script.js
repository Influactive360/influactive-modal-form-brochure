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

    modalFormClose.addEventListener('click', function (e) {
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
})
