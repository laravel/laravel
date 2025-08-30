document.getElementById('show-register').addEventListener('click', function() {
    document.getElementById('login-form-container').classList.remove('active');
    document.getElementById('register-form-container').classList.add('active');
});

document.getElementById('show-login').addEventListener('click', function() {
    document.getElementById('register-form-container').classList.remove('active');
    document.getElementById('login-form-container').classList.add('active');
});