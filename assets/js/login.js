document.addEventListener('DOMContentLoaded', function(){
    // Update footer year
    const year = document.getElementById('year');
    if(year) year.textContent = new Date().getFullYear();

    // Login Form Handler
    const loginForm = document.getElementById('loginForm');
    if(loginForm){
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Simple validation logic
            if(username && password) {
                // Set session token for app.js to read later
                localStorage.setItem('pivo_session', 'active');
                
                // Redirect to Dashboard (Page 03)
                window.location.href = 'home.html'; 
            } else {
                alert("Please enter both username and password.");
            }
        });
    }
});