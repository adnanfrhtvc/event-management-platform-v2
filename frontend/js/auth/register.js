var registerPage = {
    init: function () {
        const form = document.getElementById('registerForm');
        const messageDiv = document.getElementById('registerMessage');

        if (!form) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                name: form.name.value.trim(),
                email: form.email.value.trim(),
                password: form.password.value
            };

            if (!formData.name || !formData.email || !formData.password) {
                showMessage('All fields are required', 'danger');
                return;
            }

            try {
                const response = await fetch('http://localhost/event-management-platform-v2/backend/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) throw new Error(data.error || 'Registration failed');

                showMessage('Registration successful! Redirecting...', 'success');
                setTimeout(() => window.location.hash = '#login', 2000);

            } catch (error) {
                showMessage(error.message, 'danger');
                console.error('Registration error:', error);
            }
        });

        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = `alert alert-${type} d-block`;
        }

        console.log("Register page initialized.");
    }
};
