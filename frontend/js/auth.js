const signupForm = document.getElementById("signupForm");

if (signupForm) {

    signupForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        const message = document.getElementById("message");

        message.textContent = "Creating account...";

        try {

            const response = await fetch(
                "../api/auth/signup.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        name: name,
                        email: email,
                        password: password
                    })
                }
            );

            const result = await response.json();

            if (result.success) {

                message.style.color = "green";
                message.textContent = result.message;

                signupForm.reset();

            } else {

                message.style.color = "red";
                message.textContent = result.message;
            }

        } catch (error) {

            console.error(error);

            message.style.color = "red";
            message.textContent =
                "Unable to connect to the server.";
        }

    });

}

const loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        const email = document.getElementById("loginEmail").value.trim();
        const password = document.getElementById("loginPassword").value;

        const message = document.getElementById("loginMessage");

        message.style.color = "#333";
        message.textContent = "Logging in...";

        try {

            const response = await fetch(
                "../api/auth/login.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                }
            );

            const result = await response.json();

            if (result.success) {

                // Store JWT token
                localStorage.setItem(
                    "authToken",
                    result.token
                );

                // Store basic user information
                localStorage.setItem(
                    "user",
                    JSON.stringify(result.user)
                );

                message.style.color = "green";
                message.textContent = result.message;

                // Redirect to dashboard
                setTimeout(function () {

                    window.location.href = "dashboard.html";

                }, 500);

            } else {

                message.style.color = "red";
                message.textContent = result.message;

            }

        } catch (error) {

            console.error(error);

            message.style.color = "red";
            message.textContent =
                "Unable to connect to the server.";

        }

    });

}