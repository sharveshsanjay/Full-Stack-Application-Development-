const form = document.getElementById("loginForm");

const username = document.getElementById("username");
const password = document.getElementById("password");

const usernameError = document.getElementById("usernameError");
const passwordError = document.getElementById("passwordError");

username.addEventListener("input", function () {

    if (username.value.trim() === "") {
        usernameError.textContent = "Username is required.";
    } else {
        usernameError.textContent = "";
    }

});

password.addEventListener("input", function () {

    if (password.value.trim() === "") {
        passwordError.textContent = "Password is required.";
    } else if (password.value.length < 6) {
        passwordError.textContent =
            "Password must contain at least 6 characters.";
    } else {
        passwordError.textContent = "";
    }

});

form.addEventListener("submit", function (event) {

    let valid = true;

    usernameError.textContent = "";
    passwordError.textContent = "";

    if (username.value.trim() === "") {

        usernameError.textContent =
            "Username is required.";

        valid = false;
    }

    if (password.value.trim() === "") {

        passwordError.textContent =
            "Password is required.";

        valid = false;

    } else if (password.value.length < 6) {

        passwordError.textContent =
            "Password must contain at least 6 characters.";

        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }

});