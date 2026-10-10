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

const params = new URLSearchParams(window.location.search);
const name = params.get("name");
const email = params.get("email");
const book = params.get("book");

if (name && email && email.includes("@") && book) {
  const message = document.getElementById("received-text");
  message.textContent = name + ", your request for " + book + " was received. A note can be sent to " + email + ".";
  document.getElementById("received").showModal();
}
