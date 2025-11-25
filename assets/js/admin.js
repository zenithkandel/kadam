/**
 * Admin Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    checkAuth('admin');
    loadAdminProfile();
});

async function checkAuth(requiredRole) {
    try {
        const response = await ApiHandler.get('auth/me.php');
        if (response.success) {
            const user = response.data;
            if (user.role !== requiredRole) {
                 window.location.href = '../../auth/login.html';
            }
            // Update UI with user data
            const nameElements = document.querySelectorAll('.user-name');
            nameElements.forEach(el => el.textContent = user.name);
        } else {
             window.location.href = '../../auth/login.html';
        }
    } catch (error) {
        window.location.href = '../../auth/login.html';
    }
}

async function loadAdminProfile() {
    // Profile data is loaded in checkAuth
}
