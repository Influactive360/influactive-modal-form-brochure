document.addEventListener("DOMContentLoaded", function () {
  const addFieldButton = document.querySelector("#add-field")

  addFieldButton.addEventListener("click", function () {
    const fieldsContainer = document.querySelector("#form-fields")

    const newField = document.createElement("div")
    newField.className = "field"

    const typeLabel = document.createElement("label")
    typeLabel.setAttribute("for", "type")
    typeLabel.textContent = "Type:"
    newField.appendChild(typeLabel)

    const typeInput = document.createElement("input")
    typeInput.setAttribute("id", "type")
    typeInput.setAttribute("type", "text")
    typeInput.setAttribute("name", "modal_form_fields[field_type][]")
    newField.appendChild(typeInput)

    const labelLabel = document.createElement("label")
    labelLabel.setAttribute("for", "label")
    labelLabel.textContent = "Label:"
    newField.appendChild(labelLabel)

    const labelInput = document.createElement("input")
    labelInput.setAttribute("id", "label")
    labelInput.setAttribute("type", "text")
    labelInput.setAttribute("name", "modal_form_fields[field_label][]")
    newField.appendChild(labelInput)

    const nameLabel = document.createElement("label")
    nameLabel.setAttribute("for", "name")
    nameLabel.textContent = "Name:"
    newField.appendChild(nameLabel)

    const nameInput = document.createElement("input")
    nameInput.setAttribute("id", "name")
    nameInput.setAttribute("type", "text")
    nameInput.setAttribute("name", "modal_form_fields[field_name][]")
    newField.appendChild(nameInput)

    const deleteButton = document.createElement("button")
    deleteButton.className = "delete-field"
    deleteButton.setAttribute("type", "button")
    deleteButton.textContent = "Delete"
    deleteButton.addEventListener("click", function () {
      console.log("delete")
      newField.remove()
    })
    newField.appendChild(deleteButton)

    fieldsContainer.appendChild(newField)
  })

  // bind deletes buttons
  const deleteButtons = document.querySelectorAll(".delete-field")
  deleteButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      console.log("delete")
      button.parentElement.remove()
    })
  })
})
