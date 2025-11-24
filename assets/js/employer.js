/**
 * Employer Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    checkAuth('employer');
    loadEmployerProfile();
});

async function checkAuth(requiredRole) {
    const token = localStorage.getItem('auth_token');
    const role = localStorage.getItem('user_role');

    if (!token || role !== requiredRole) {
        window.location.href = '../../auth/login.html';
    }
}

async function loadEmployerProfile() {
    try {
        const user = JSON.parse(localStorage.getItem('user_data'));
        if (user) {
            const nameElements = document.querySelectorAll('.user-name');
            nameElements.forEach(el => el.textContent = user.name);
        }
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}
