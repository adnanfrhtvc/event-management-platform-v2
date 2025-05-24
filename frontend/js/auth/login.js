var loginPage = {
    init: function () {
        const form = document.getElementById("loginForm");
        const messageDiv = document.getElementById("loginMessage");

        if (!form) return;

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const email = document.getElementById("loginEmail").value.trim();
            const password = document.getElementById("loginPassword").value;

            if (!email || !password) {
                showMessage("Please fill in both fields.", "danger");
                return;
            }

            try {
                const response = await fetch("http://localhost/event-management-platform-v2/backend/auth/login", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (!response.ok) throw new Error(result.error || "Login failed");

                // Store token and user info in localStorage
                localStorage.setItem("token", result.data.token);
                localStorage.setItem("user", JSON.stringify(result.data));

                showMessage("Login successful! Redirecting...", "success");

                // Redirect and update navbar
                setTimeout(() => {
                    window.location.hash = "#home";
                    auth.updateNavbar();
                }, 1000);
                
            } catch (error) {
                showMessage(error.message, "danger");
                console.error("Login error:", error);
            }
        });

        function showMessage(message, type) {
            messageDiv.textContent = message;
            messageDiv.className = `alert alert-${type} mt-2`;
            messageDiv.classList.remove("d-none");
        }

        console.log("Login script initialized");
    }
};
