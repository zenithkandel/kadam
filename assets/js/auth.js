/**
 * Auth Scripts
 * Handles Login, Signup, Password Reset
 */

document.addEventListener('DOMContentLoaded', () => {
    // Login Form Handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            
            try {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Logging in...';
                
                const formData = new FormData(loginForm);
                const data = Object.fromEntries(formData.entries());
                
                const response = await ApiHandler.post('auth/login.php', data);
                
                if (response.success) {
                    const { token, user } = response.data;
                    
                    localStorage.setItem('auth_token', token);
                    localStorage.setItem('user_role', user.role);
                    localStorage.setItem('user_data', JSON.stringify(user));
                    
                    // Redirect based on role
                    if (user.role === 'student') {
                        window.location.href = '../dashboard/student/dashboard.html';
                    } else if (user.role === 'employer') {
                        window.location.href = '../dashboard/employer/dashboard.html';
                    } else if (user.role === 'admin') {
                        window.location.href = '../dashboard/admin/dashboard.html';
                    }
                }
            } catch (error) {
                alert(error.message || 'Login failed');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        });
    }

    // Signup Form Handler
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = signupForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;

            try {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Creating Account...';

                const formData = new FormData(signupForm);
                const data = Object.fromEntries(formData.entries());

                const response = await ApiHandler.post('auth/signup.php', data);

                if (response.success) {
                    alert('Account created successfully! Please login.');
                    window.location.href = 'login.html';
                }
            } catch (error) {
                alert(error.message || 'Signup failed');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        });
    }
});
