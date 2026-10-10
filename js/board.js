const dialog = document.getElementById("returned");
const message = document.getElementById("returned-text");

if (dialog && message && message.textContent.trim() !== "") {
  dialog.showModal();
}
