document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const closeSidebar = document.getElementById('closeSidebar');
    const navLinks = document.querySelectorAll('.nav-link[data-page]');
    const contentFrame = document.getElementById('contentFrame');
    const pageTitle = document.getElementById('pageTitle');
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = themeToggle.querySelector('i');

    // Sidebar Toggle
    menuToggle.addEventListener('click', () => {
        sidebar.classList.add('active');
    });

    closeSidebar.addEventListener('click', () => {
        sidebar.classList.remove('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !menuToggle.contains(e.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    // Navigation Logic
    function loadPage(pageUrl, title, linkElement) {
        contentFrame.src = pageUrl;
        pageTitle.textContent = title;
        
        // Update active state
        navLinks.forEach(link => link.classList.remove('active'));
        if (linkElement) {
            linkElement.classList.add('active');
        }

        // Close sidebar on mobile after selection
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }

        // Save state
        sessionStorage.setItem('admin_activePage', pageUrl);
        sessionStorage.setItem('admin_pageTitle', title);
        
        // Sync theme with new iframe page
        contentFrame.onload = () => {
            syncThemeToIframe();
        };
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            const title = link.querySelector('span').textContent;
            loadPage(page, title, link);
        });
    });

    // Restore State
    const savedPage = sessionStorage.getItem('admin_activePage');
    const savedTitle = sessionStorage.getItem('admin_pageTitle');

    if (savedPage && savedTitle) {
        // Find the link that matches the saved page
        const activeLink = Array.from(navLinks).find(link => link.getAttribute('data-page') === savedPage);
        loadPage(savedPage, savedTitle, activeLink);
    } else {
        // Default to first page
        loadPage('overview.html', 'Overview', navLinks[0]);
    }

    // Theme Toggle Logic
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }

        syncThemeToIframe();
    }

    function syncThemeToIframe() {
        try {
            const iframeDoc = contentFrame.contentDocument || contentFrame.contentWindow.document;
            if (iframeDoc) {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                iframeDoc.documentElement.setAttribute('data-theme', currentTheme);
            }
        } catch (e) {
            console.log('Cannot access iframe content yet');
        }
    }

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    // Initialize Theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
});