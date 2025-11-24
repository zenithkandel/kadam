/**
 * Student Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    checkAuth('student');
    loadStudentProfile();
    loadRecentTasks();
});

async function checkAuth(requiredRole) {
    const token = localStorage.getItem('auth_token');
    const role = localStorage.getItem('user_role');

    if (!token || role !== requiredRole) {
        window.location.href = '../../auth/login.html';
    }
}

async function loadStudentProfile() {
    try {
        const user = JSON.parse(localStorage.getItem('user_data'));
        if (user) {
            const nameElements = document.querySelectorAll('.user-name');
            nameElements.forEach(el => el.textContent = user.name);
            
            // Load more details from API if needed
            // const profile = await ApiHandler.get('student/profile.php');
            // updateUI(profile);
        }
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

async function loadRecentTasks() {
    // Implementation for loading tasks
    // const tasks = await ApiHandler.get('tasks/list.php');
    // renderTasks(tasks);
}
