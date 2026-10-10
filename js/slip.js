const FEE_PER_DAY = 2;
const exampleDays = [1, 2, 3];

function lateFee(days) {
  return days * FEE_PER_DAY;
}

const examples = document.getElementById("examples");

for (let i = 0; i < exampleDays.length; i++) {
  const days = exampleDays[i];
  const item = document.createElement("li");
  const word = days === 1 ? "day" : "days";
  item.textContent = days + " " + word + ": " + lateFee(days) + " cedis";
  examples.appendChild(item);
}

const daysInput = document.getElementById("days");
const feeOutput = document.getElementById("fee");

function showFee() {
  const days = Number(daysInput.value);

  if (daysInput.value === "" || Number.isNaN(days) || days < 0) {
    feeOutput.textContent = "Enter the number of days late.";
    return;
  }

  feeOutput.textContent = "Fee: " + lateFee(days).toFixed(2) + " cedis";
}

daysInput.addEventListener("input", showFee);
document.getElementById("fee-form").addEventListener("submit", function (event) {
  event.preventDefault();
  showFee();
});

showFee();
