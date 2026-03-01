(function(){
  'use strict';

  document.addEventListener('DOMContentLoaded', function(){
    // Update footer year
    var year = document.getElementById('year');
    if(year) year.textContent = new Date().getFullYear();

    // Session check for access control
    try{
      var session = localStorage.getItem('pivo_session');
      if(!session){
        window.location.href = 'login.html';
      }
    }catch(e){
      window.location.href = 'login.html';
    }

    // Logout button
    var logoutBtn = document.getElementById('logout');
    if(logoutBtn){
      logoutBtn.addEventListener('click', function(){
        localStorage.removeItem('pivo_session');
        window.location.href = 'login.html';
      });
    }
  });
})();