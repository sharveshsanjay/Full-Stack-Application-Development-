document.addEventListener("DOMContentLoaded", function () {
    const nameInput = document.getElementById("full_name");
    const emailInput = document.getElementById("email");
    const commentsInput = document.getElementById("comments");
    const submitBtn = document.getElementById("submit_btn");
    const feedbackForm = document.getElementById("feedbackForm");

    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const commentsError = document.getElementById("commentsError");
    const clickStatus = document.getElementById("clickStatus");

    let clickTimer = null;

    // --- REUSABLE VALIDATION FUNCTIONS ---

    function validateName(name) {
        if (!name || name.trim().length < 3) {
            return "Name must be at least 3 characters long.";
        }
        const nameRegex = /^[A-Za-z\s]+$/;
        if (!nameRegex.test(name)) {
            return "Name must contain letters and spaces only.";
        }
        return "";
    }

    function validateEmail(email) {
        if (!email) {
            return "Email address is required.";
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return "Please enter a valid email address (e.g. name@domain.com).";
        }
        return "";
    }

    function validateComments(comments) {
        if (!comments || comments.trim().length < 10) {
            return "Comments must be at least 10 characters long.";
        }
        return "";
    }

    // Helper to update field UI state
    function updateFieldState(input, errorElement, errorMessage) {
        if (errorMessage) {
            input.classList.add("invalid");
            input.classList.remove("valid");
            errorElement.textContent = errorMessage;
            errorElement.style.display = "block";
            return false;
        } else {
            input.classList.add("valid");
            input.classList.remove("invalid");
            errorElement.textContent = "";
            errorElement.style.display = "none";
            return true;
        }
    }

    // --- KEYPRESS & INPUT EVENTS (Live Validation) ---

    nameInput.addEventListener("input", function () {
        const error = validateName(nameInput.value);
        updateFieldState(nameInput, nameError, error);
    });

    emailInput.addEventListener("input", function () {
        const error = validateEmail(emailInput.value);
        updateFieldState(emailInput, emailError, error);
    });

    commentsInput.addEventListener("input", function () {
        const error = validateComments(commentsInput.value);
        updateFieldState(commentsInput, commentsError, error);
    });

    // --- MOUSE HOVER EVENTS (Highlight Fields) ---

    const formControls = document.querySelectorAll(".form-control");
    formControls.forEach(function (element) {
        element.addEventListener("mouseenter", function () {
            element.classList.add("hover-highlight");
        });

        element.addEventListener("mouseleave", function () {
            element.classList.remove("hover-highlight");
        });
    });

    // --- DOUBLE-CLICK SUBMIT EVENT ---

    // Prevent accidental single click submit
    submitBtn.addEventListener("click", function (event) {
        // Show hint to user if single clicked
        clickStatus.textContent = "⚡ Please DOUBLE-CLICK the button to submit feedback!";
        clickStatus.className = "click-status warning";

        clearTimeout(clickTimer);
        clickTimer = setTimeout(() => {
            clickStatus.textContent = "Tip: Double-click button to submit.";
            clickStatus.className = "click-status hint";
        }, 3000);

        event.preventDefault();
    });

    // Handle Double Click Submit
    submitBtn.addEventListener("dblclick", function (event) {
        event.preventDefault();

        // Run full validation check
        const isNameValid = updateFieldState(nameInput, nameError, validateName(nameInput.value));
        const isEmailValid = updateFieldState(emailInput, emailError, validateEmail(emailInput.value));
        const isCommentsValid = updateFieldState(commentsInput, commentsError, validateComments(commentsInput.value));

        if (isNameValid && isEmailValid && isCommentsValid) {
            clickStatus.textContent = "✓ Confirmed! Submitting form...";
            clickStatus.className = "click-status success";

            // Submit the form programmatically
            setTimeout(() => {
                feedbackForm.submit();
            }, 400);
        } else {
            clickStatus.textContent = "❌ Submission failed! Fix errors above.";
            clickStatus.className = "click-status error";
        }
    });
});
