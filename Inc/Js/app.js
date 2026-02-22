(function(){
  'use strict';

  function setYear(){
    var y = document.getElementById('year');
    if(y) y.textContent = new Date().getFullYear();
  }

  function getUsers(){
    try{
      var raw = localStorage.getItem('pivo_users');
      return raw ? JSON.parse(raw) : [];
    }catch(e){ return []; }
  }

  function saveUsers(users){
    localStorage.setItem('pivo_users', JSON.stringify(users));
  }

  function findUserByIdentifier(identifier){
    var users = getUsers();
    return users.find(function(u){
      return u.username === identifier || u.email === identifier;
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    setYear();

    var loginForm = document.getElementById('login-form');
    if(loginForm){
      loginForm.addEventListener('submit', function(e){
        e.preventDefault();
        var ident = (document.getElementById('login-username')||{}).value||'';
        var pwd = (document.getElementById('login-password')||{}).value||'';
        ident = ident.trim();
        if(!ident || !pwd){
          alert('Please fill both fields.');
          return;
        }
        var user = findUserByIdentifier(ident);
        if(!user || user.password !== pwd){
          alert('Invalid username/email or password.');
          return;
        }
        localStorage.setItem('pivo_session', JSON.stringify({username:user.username, email:user.email}));
        window.location.href = 'dashboard.html';
      });
    }

    var signupForm = document.getElementById('signup-form');
    if(signupForm){
      signupForm.addEventListener('submit', function(e){
        e.preventDefault();
        var username = (document.getElementById('signup-username')||{}).value||'';
        var email = (document.getElementById('signup-email')||{}).value||'';
        var pwd = (document.getElementById('signup-password')||{}).value||'';
        var pwd2 = (document.getElementById('signup-password2')||{}).value||'';
        username = username.trim();
        email = email.trim();
        if(!username || !email || !pwd || !pwd2){
          alert('Please complete all fields.');
          return;
        }
        if(pwd !== pwd2){
          alert('Passwords do not match.');
          return;
        }
        var users = getUsers();
        var exists = users.some(function(u){ return u.username === username || u.email === email; });
        if(exists){
          alert('An account with this username or email already exists.');
          return;
        }
        users.push({username: username, email: email, password: pwd});
        saveUsers(users);
        alert('Account created successfully. Redirecting to login.');
        window.location.href = 'login.html';
      });
    }

    var logoutBtns = document.querySelectorAll('#logout');
    if(logoutBtns && logoutBtns.length){
      logoutBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
          localStorage.removeItem('pivo_session');
          window.location.href = 'login.html';
        });
      });
    }

    // Dashboard access protection + personalize
    if(window.location.pathname.endsWith('dashboard.html') || window.location.pathname.endsWith('/dashboard.html')){
      try{
        var session = localStorage.getItem('pivo_session');
        if(!session){
          window.location.href = 'login.html';
        } else {
          session = JSON.parse(session);
          var welcome = document.getElementById('welcome-name');
          if(welcome) welcome.textContent = session.username;
          var avatarBtns = document.querySelectorAll('.avatar');
          avatarBtns.forEach(function(a){ a.textContent = (session.username||'U').charAt(0).toUpperCase(); });
        }
      }catch(e){
        window.location.href = 'login.html';
      }
    }

    var forgot = document.getElementById('forgot-password');
    if(forgot){
      forgot.addEventListener('click', function(e){
        e.preventDefault();
        alert('This demo stores users locally. To reset a password, delete the account in localStorage or create a new account.');
      });
    }

  });
})();