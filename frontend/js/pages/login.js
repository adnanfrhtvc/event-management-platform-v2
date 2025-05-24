export default {
  init: function () {
    console.log("Login script initialized");

    const form = document.querySelector("form");
    const emailInput = document.getElementById("form2Example17");
    const passwordInput = document.getElementById("form2Example27");

    form.addEventListener("submit", async function (e) {
      e.preventDefault();
      const email = emailInput.value;
      const password = passwordInput.value;

      try {
        const response = await fetch("http://localhost/event-management-platform-v2/backend/auth/login", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email, password })
        });

        if (!response.ok) throw new Error("Login failed");

        const result = await response.json();

        if (response.ok) {
          localStorage.setItem("jwt", result.data.token);
          localStorage.setItem("role", result.data.role);
          localStorage.setItem("name", result.data.name)

          // Redirect to #home
          window.location.hash = "#home";

          if (window.auth && typeof window.auth.updateNavbar === "function") {
              window.auth.updateNavbar(); 
              }
            }

      } catch (error) {
        console.error("Login error:", error);
        alert("Login failed: " + error.message);
      }
    });
  }
};
