const params = new URLSearchParams(window.location.search);
const message = document.getElementById("message");
const loginView = document.getElementById("loginView");
const landingView = document.getElementById("landingView");

if (params.get("error")) {
  message.textContent = params.get("error");
  message.className = "message error";
} else if (params.get("registered")) {
  message.textContent = "Registration successful. Please log in.";
  message.className = "message success";
}

fetch("register.php?status=1").then(function (response) {
  if (!response.ok) {
    document.body.className = "auth-body";
    loginView.hidden = false;
    return;
  }
  return response.text().then(function (name) {
    document.body.className = "landing-body";
    document.getElementById("welcome").textContent = "Welcome, " + name;
    landingView.hidden = false;
  });
});

const reflectedForm = document.getElementById("reflectedForm");
const reflectedInput = document.getElementById("reflectedInput");
const reflectedOutput = document.getElementById("reflectedOutput");

reflectedForm.addEventListener("submit", function (event) {
  event.preventDefault();
  reflectedOutput.innerHTML = "You entered: " + reflectedInput.value;
});

const domInput = document.getElementById("domInput");
const domButton = document.getElementById("domButton");
const domOutput = document.getElementById("domOutput");

domButton.addEventListener("click", function () {
  domOutput.innerHTML = domInput.value;
});

const safeInput = document.getElementById("safeInput");
const safeButton = document.getElementById("safeButton");
const safeOutput = document.getElementById("safeOutput");

safeButton.addEventListener("click", function () {
  safeOutput.textContent = safeInput.value;
});