document.addEventListener('DOMContentLoaded', function(){
    // Update footer year
    const year = document.getElementById('year');
    if(year) year.textContent = new Date().getFullYear();

    // Sign Up Form Handler
    const signupForm = document.getElementById('signupForm');
    if(signupForm){
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;

            // Password matching validation
            if(password !== confirm) {
                alert("Passwords do not match!");
                return;
            }

            // --- CHANGED LOGIC HERE ---
            
            // 1. We do NOT set 'pivo_session' here, so they are not logged in yet.
            
            // 2. Alert the user they need to log in
            alert("Account created successfully! Please log in.");
            
            // 3. Redirect to Login Page instead of Index
            window.location.href = 'login.html'; 
        });
    }
});