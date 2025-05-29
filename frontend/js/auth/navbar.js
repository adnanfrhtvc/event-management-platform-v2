function updateNavbar() {
    // Safe element references with null checks
    const elements = {
        loginNav: document.getElementById('login-nav'),
        logoutNav: document.getElementById('logout-nav'),
        profileNav: document.getElementById('profile-nav'),
        adminLinks: document.getElementById('admin-links'),
        dashboardNav: document.getElementById('dashboard-nav')
    };

    // Default all elements to safe values
    Object.values(elements).forEach(el => {
        if (el) el.style.display = 'none';
    });

    const token = localStorage.getItem('jwt');
    const role = localStorage.getItem('role');

    if (token) {
        if (elements.logoutNav) elements.logoutNav.style.display = 'block';
        if (elements.profileNav) elements.profileNav.style.display = 'block';

        if (role === 'organizer') {
            if (elements.adminLinks) elements.adminLinks.style.display = 'block';
            if (elements.dashboardNav) elements.dashboardNav.style.display = 'block';
        } else {
            if (elements.dashboardNav) elements.dashboardNav.style.display = 'none';
        }
    } else {
        if (elements.loginNav) elements.loginNav.style.display = 'block';
        if (elements.dashboardNav) elements.dashboardNav.style.display = 'none';
    }
}

function logout() {
    localStorage.removeItem('jwt');
    localStorage.removeItem('role');
    window.location.hash = '#login';
    updateNavbar();
}

document.addEventListener('DOMContentLoaded', updateNavbar);

window.auth = {
    updateNavbar,
    logout
};