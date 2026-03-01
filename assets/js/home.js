document.addEventListener('DOMContentLoaded', function(){
    
    // 1. Update Footer Year
    const yearElement = document.getElementById('year');
    if(yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }

    // 2. Logout Logic
    const logoutBtn = document.getElementById('logout');
    if(logoutBtn){
        logoutBtn.addEventListener('click', function(){
            // Optional: Clear session if you are using one
            localStorage.removeItem('pivo_session'); 
            
            // Redirect to Login Page
            window.location.href = 'login.html';
        });
    }

    // 3. Highlight "Active" Navigation Link
    // This makes the "Dashboard" link blue when you are on the dashboard
    const navLinks = document.querySelectorAll('.dash-nav a');
    const currentPath = window.location.pathname.split("/").pop();
    
    navLinks.forEach(link => {
        // If the href matches the file name (e.g. "home.html")
        if(link.getAttribute('href') === currentPath || (currentPath === '' && link.getAttribute('href') === 'home.html')) {
            link.classList.add('active');
        }
    });

});