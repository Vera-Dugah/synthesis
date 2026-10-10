const form = document.getElementById("borrow-form");
const warning = document.getElementById("warning");
let warned = false;

function warningText(control) {
  if (control.validity.valueMissing && control.id === "borrower") {
    return "Enter your name. A name is required.";
  }
  if (control.validity.valueMissing && control.id === "email") {
    return "Enter your email. An email is required.";
  }
  if (control.validity.typeMismatch) {
    return "That email is not valid. Include an @.";
  }
  if (control.validity.valueMissing && control.id === "book") {
    return "Choose a book. The blank first choice does not count.";
  }
  return "Check the rules and try again.";
}

form.querySelector("button[type='submit']").addEventListener("click", function () {
  warned = false;
});

form.addEventListener("invalid", function (event) {
  event.preventDefault();
  if (warned) return;
  warned = true;
  warning.hidden = false;
  warning.textContent = warningText(event.target);
}, true);

form.addEventListener("input", function () {
  const controls = Array.from(form.elements);
  const allValid = controls.every(function (control) {
    return !control.willValidate || control.validity.valid;
  });
  if (allValid) warning.hidden = true;
});

const message = document.getElementById("received-text");
if (message.textContent.trim() !== "") {
  document.getElementById("received").showModal();
}
