document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for nav links
    const links = document.querySelectorAll('nav a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Animated counters
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the slower

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    };
    
    const statsSection = document.querySelector('#stats');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        }, { threshold: 0.5 });
        observer.observe(statsSection);
    }


    // Dummy Job Data
    const jobsData = [
        { title: 'Modern Website for a Startup', category: 'Website', description: 'We need a skilled web developer to create a responsive and modern website for our new tech startup. Experience with React is a plus.' },
        { title: 'Logo Design for a Coffee Shop', category: 'Graphics Design', description: 'Looking for a creative graphic designer to create a unique and memorable logo for our new coffee shop brand.' },
        { title: 'Animated Explainer Video', category: 'Videography', description: 'We need an animator to create a 60-second explainer video for our new mobile app. Must have a strong portfolio.' },
        { title: 'Database Optimization for a SaaS', category: 'Software', description: 'Seeking an expert in SQL and database architecture to optimize our production database for performance and scalability.' },
        { title: 'Illustrations for a Children\'s Book', category: 'Art', description: 'We are looking for a talented illustrator to create 20 beautiful illustrations for a new children\'s book.' },
        { title: 'Content Writer for a Tech Blog', category: 'Other', description: 'Hiring a skilled writer to produce high-quality articles on topics related to AI and machine learning.' }
    ];

    // Populate job listings
    function populateJobs(jobs) {
        const jobListings = document.getElementById('job-listings');
        if (jobListings) {
            jobListings.innerHTML = '';
            jobs.forEach(job => {
                const jobElement = document.createElement('div');
                jobElement.classList.add('job');
                jobElement.innerHTML = `
                    <div class="job-content">
                        <h3>${job.title}</h3>
                        <p>${job.description}</p>
                    </div>
                    <div class="job-footer">
                        <button class="btn btn-secondary">View Details & Apply</button>
                    </div>
                `;
                jobListings.appendChild(jobElement);
            });
        }
        
        const featuredJobsContainer = document.querySelector('#featured-jobs .jobs');
        if(featuredJobsContainer) {
            featuredJobsContainer.innerHTML = '';
            jobs.slice(0, 3).forEach(job => {
                 const jobElement = document.createElement('div');
                jobElement.classList.add('job');
                jobElement.innerHTML = `
                    <div class="job-content">
                        <h3>${job.title}</h3>
                        <p>${job.description}</p>
                    </div>
                    <div class="job-footer">
                        <button class="btn btn-secondary">View Details & Apply</button>
                    </div>
                `;
                featuredJobsContainer.appendChild(jobElement);
            });
        }
    }

    // Initial population of jobs
    populateJobs(jobsData);

    // Job filtering
    const filterBtn = document.getElementById('filter-btn');
    if (filterBtn) {
        filterBtn.addEventListener('click', () => {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const category = document.getElementById('category-filter').value;

            const filteredJobs = jobsData.filter(job => {
                const matchesSearch = job.title.toLowerCase().includes(searchTerm) || job.description.toLowerCase().includes(searchTerm);
                const matchesCategory = category ? job.category === category : true;
                return matchesSearch && matchesCategory;
            });

            populateJobs(filteredJobs);
        });
    }
    
    // Contact form submission
    const contactForm = document.getElementById('contact-form');
    if(contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you shortly.');
            this.reset();
        });
    }
});

// Functions for signup/login pages
function showForm(type) {
    document.getElementById('student-form').classList.add('hidden');
    document.getElementById('employer-form').classList.add('hidden');
    document.getElementById(type + '-form').classList.remove('hidden');
}

function showVerification(type) {
    document.getElementById('student-verification').classList.add('hidden');
    document.getElementById('employer-verification').classList.add('hidden');
    document.getElementById(type + '-verification').classList.remove('hidden');
}

function showEmployerType(type) {
    document.getElementById('company-verification').classList.add('hidden');
    document.getElementById('individual-verification').classList.add('hidden');
    if (type) {
        document.getElementById(type + '-verification').classList.remove('hidden');
    }
}

function showDashboard(type) {
    document.getElementById('student-dashboard').classList.add('hidden');
    document.getElementById('employer-dashboard').classList.add('hidden');
    document.getElementById(type + '-dashboard').classList.remove('hidden');
}

function showEmployerDashboard(type) {
    document.getElementById('individual-emp').classList.add('hidden');
    document.getElementById('company-emp').classList.add('hidden');
    if (type) {
        document.getElementById(type + '-emp').classList.remove('hidden');
    }
}