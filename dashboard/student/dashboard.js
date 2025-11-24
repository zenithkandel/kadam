document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const contentFrame = document.getElementById('contentFrame');
    const pageTitle = document.getElementById('pageTitle');
    const menuItems = document.querySelectorAll('.menu-item[data-page]');
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const themeIcon = themeToggle.querySelector('i');

    // --- Navigation Logic ---
    function loadPage(pageUrl, title) {
        contentFrame.src = pageUrl;
        pageTitle.textContent = title;
        
        // Update Active State
        menuItems.forEach(item => {
            if (item.dataset.page === pageUrl) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Save to Session Storage
        sessionStorage.setItem('studentLastPage', pageUrl);
        sessionStorage.setItem('studentLastTitle', title);

        // Close mobile sidebar if open
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }
    }

    // Event Listeners for Menu Items
    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const page = item.dataset.page;
            const title = item.querySelector('span').textContent;
            loadPage(page, title);
        });
    });

    // Restore State from Session Storage
    const lastPage = sessionStorage.getItem('studentLastPage');
    const lastTitle = sessionStorage.getItem('studentLastTitle');
    if (lastPage && lastTitle) {
        loadPage(lastPage, lastTitle);
    } else {
        // Default
        loadPage('overview.html', 'Overview');
    }

    // --- Sidebar Toggle ---
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside (Mobile)
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // --- Theme Toggle Logic ---
    // Check local storage or default
    const currentTheme = localStorage.getItem('theme') || 'light';
    setTheme(currentTheme);

    themeToggle.addEventListener('click', () => {
        const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    function setTheme(theme) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update Icon
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Sync with Iframe
        if (contentFrame.contentWindow && contentFrame.contentWindow.document) {
            contentFrame.contentWindow.document.documentElement.setAttribute('data-theme', theme);
        }
    }

    // Listen for Iframe Load to Apply Theme
    contentFrame.addEventListener('load', () => {
        const theme = localStorage.getItem('theme') || 'light';
        const iframeDoc = contentFrame.contentDocument || contentFrame.contentWindow.document;
        if (iframeDoc) {
            iframeDoc.documentElement.setAttribute('data-theme', theme);
            
            // Optional: Inject CSS to ensure iframe background matches
            // iframeDoc.body.style.backgroundColor = 'var(--background-color)';
        }
    });
});
