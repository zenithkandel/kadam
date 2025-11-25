/**
 * Student Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    checkAuth('student');
    loadStudentProfile();
    loadRecentTasks();
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

async function loadStudentProfile() {
    // Profile data is loaded in checkAuth or can be fetched separately
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
