/**
 * Main Site Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in to update navigation
    const role = localStorage.getItem('user_role');
    
    if (role) {
        updateNavForLoggedInUser(role);
    }
});

function updateNavForLoggedInUser(role) {
    const authLinks = document.querySelector('.auth-buttons'); // Adjust selector based on actual HTML
    if (authLinks) {
        authLinks.innerHTML = `
            <a href="dashboard/${role}/dashboard.html" class="btn btn-primary">Dashboard</a>
            <a href="#" onclick="logout(event)" class="btn btn-outline">Logout</a>
        `;
    }
}

async function logout(event) {
    if(event) event.preventDefault();
    try {
        await ApiHandler.post('auth/logout.php');
    } catch (error) {
        console.error("Logout failed", error);
    }
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_data');
    window.location.href = '/projects/kadam/index.html';
}
