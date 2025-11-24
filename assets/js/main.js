/**
 * Main Site Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in to update navigation
    const token = localStorage.getItem('auth_token');
    const role = localStorage.getItem('user_role');
    
    if (token && role) {
        updateNavForLoggedInUser(role);
    }
});

function updateNavForLoggedInUser(role) {
    const authLinks = document.querySelector('.auth-buttons'); // Adjust selector based on actual HTML
    if (authLinks) {
        authLinks.innerHTML = `
            <a href="dashboard/${role}/dashboard.html" class="btn btn-primary">Dashboard</a>
            <a href="#" onclick="logout()" class="btn btn-outline">Logout</a>
        `;
    }
}

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_data');
    window.location.href = 'index.html';
}
