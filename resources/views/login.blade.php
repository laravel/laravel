<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Make it Easy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #124191 0%, #136d96 50%, #018b8d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
      font-family: 'Mulish', 'Helvetica Neue', Arial, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 48px;
            width: 100%;
            max-width: 460px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #124191, #018b8d);
        }

        .logo-section {
            margin-bottom: 32px;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 16px;
            border: 3px solid #f8f9fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            color: #124191;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #3a3a3c;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #3a3a3c;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #018b8d;
            box-shadow: 0 0 0 3px rgba(1, 139, 141, 0.1);
        }

        .form-input.error {
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
        }

        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 6px;
            display: none;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            box-sizing: border-box;
            margin-bottom: 16px;
        }

        .btn-primary {
            background: #e85170;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #d73e5e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(232, 81, 112, 0.3);
        }

        .btn-primary:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #9c27af;
            color: white;
        }

        .btn-secondary:hover {
            background: #8e24aa;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(156, 39, 175, 0.3);
        }

        .success-message {
            background: #71b35d;
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: none;
            text-align: center;
        }

        .countdown {
            font-weight: 600;
            font-size: 18px;
        }

        .resend-info {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            color: #666;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            padding: 0 16px;
        }

        .corporate-section {
            margin-top: 24px;
        }

        .corporate-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s ease;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 32px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .close {
            position: absolute;
            right: 16px;
            top: 16px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
            line-height: 1;
        }

        .close:hover {
            color: #000;
        }

        .modal-title {
            color: #124191;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-right: 40px;
        }

        .modal-text {
            color: #3a3a3c;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .contact-success {
            background: #71b35d;
            color: white;
            padding: 24px;
            border-radius: 8px;
            text-align: center;
            display: none;
        }

        .contact-success h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
        }

        .contact-success p {
            margin: 0;
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.3s ease-in-out;
        }

        @media (max-width: 640px) {
            .login-container {
                margin: 20px;
                padding: 32px 24px;
                max-width: none;
            }

            .login-title {
                font-size: 24px;
            }

            .modal-content {
                margin: 10% 20px;
                padding: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="{{ asset('assets/images/MIELogo.jpg') }}" alt="Make it Easy Logo" class="logo-img">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Enter your email to receive a secure magic link for instant access</p>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="success-message">
            <div>âœ¨ Magic link sent successfully!</div>
            <div class="countdown">Resend available in: <span id="countdownTimer">60</span>s</div>
            <div class="resend-info">Check your email for the secure login link</div>
        </div>

        <!-- Login Form -->
        <form id="loginForm">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    class="form-input" 
                    placeholder="Enter your email address"
                    required
                >
                <div id="emailError" class="error-message">Please enter a valid email address</div>
            </div>

            <button type="submit" id="sendMagicLink" class="btn btn-primary">
                Send Magic Link
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="corporate-section">
            <p class="corporate-text">Need access for your organisation?</p>
            <button id="createCorporateBtn" class="btn btn-secondary">
                Create Corporate Account
            </button>
        </div>
    </div>

    <!-- Corporate Account Modal -->
    <div id="corporateModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 class="modal-title">Create Corporate Account</h2>
            <p class="modal-text">
                Let us help you set up a corporate account for your organisation. 
                Fill out the form below and our team will contact you within a few hours to get you started.
            </p>

            <!-- Contact Success Message -->
            <div id="contactSuccess" class="contact-success">
                <h3>&#127881; Request Submitted Successfully!</h3>
                <p>Someone from the Make it Easy team will contact you in the next few hours to organise your account.</p>
            </div>

            <!-- Contact Form -->
            <form id="corporateForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName" class="form-label">First Name *</label>
                        <input type="text" id="firstName" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="form-label">Last Name *</label>
                        <input type="text" id="lastName" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="workEmail" class="form-label">Work Email *</label>
                    <input type="email" id="workEmail" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="company" class="form-label">Company Name *</label>
                    <input type="text" id="company" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" class="form-input" placeholder="+61 4XX XXX XXX">
                </div>

                <div class="form-group">
                    <label for="teamSize" class="form-label">Approximate Team Size</label>
                    <select id="teamSize" class="form-input">
                        <option value="">Select team size</option>
                        <option value="1-10">1-10 people</option>
                        <option value="11-50">11-50 people</option>
                        <option value="51-200">51-200 people</option>
                        <option value="201-500">201-500 people</option>
                        <option value="500+">500+ people</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message (Optional)</label>
                    <textarea 
                        id="message" 
                        class="form-input" 
                        rows="3" 
                        placeholder="Tell us about your specific needs or requirements"
                        style="resize: vertical; min-height: 80px;"
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let countdownInterval;
        let countdownActive = false;

        // DOM elements
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const sendButton = document.getElementById('sendMagicLink');
        const successMessage = document.getElementById('successMessage');
        const countdownTimer = document.getElementById('countdownTimer');
        const corporateBtn = document.getElementById('createCorporateBtn');
        const corporateModal = document.getElementById('corporateModal');
        const closeModal = document.getElementById('closeModal');
        const corporateForm = document.getElementById('corporateForm');
        const contactSuccess = document.getElementById('contactSuccess');

        // Email validation
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Show error message
        function showError(input, errorElement, message) {
            input.classList.add('error', 'shake');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            setTimeout(() => {
                input.classList.remove('shake');
            }, 300);
        }

        // Hide error message
        function hideError(input, errorElement) {
            input.classList.remove('error');
            errorElement.style.display = 'none';
        }

        // Start countdown timer
        function startCountdown() {
            let timeLeft = 60;
            countdownActive = true;
            sendButton.disabled = true;
            sendButton.textContent = 'Magic Link Sent';
            
            countdownTimer.textContent = timeLeft;
            successMessage.style.display = 'block';
            
            countdownInterval = setInterval(() => {
                timeLeft--;
                countdownTimer.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    countdownActive = false;
                    sendButton.disabled = false;
                    sendButton.textContent = 'Send Magic Link';
                    successMessage.style.display = 'none';
                }
            }, 1000);
        }

        // Handle magic link form submission
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (countdownActive) return;
            
            const email = emailInput.value.trim();
            
            // Validate email
            if (!email) {
                showError(emailInput, emailError, 'Please enter your email address');
                return;
            }
            
            if (!validateEmail(email)) {
                showError(emailInput, emailError, 'Please enter a valid email address');
                return;
            }
            
            hideError(emailInput, emailError);
            
            // Here you would typically send the magic link request to your backend
            console.log('Sending magic link to:', email);
            
            // Start countdown and show success message
            startCountdown();
        });

        // Clear error on input
        emailInput.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                hideError(this, emailError);
            }
        });

        // Corporate modal functionality
        corporateBtn.addEventListener('click', function() {
            corporateModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        closeModal.addEventListener('click', function() {
            corporateModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === corporateModal) {
                corporateModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Handle corporate form submission
        corporateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                firstName: document.getElementById('firstName').value.trim(),
                lastName: document.getElementById('lastName').value.trim(),
                workEmail: document.getElementById('workEmail').value.trim(),
                company: document.getElementById('company').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                teamSize: document.getElementById('teamSize').value,
                message: document.getElementById('message').value.trim()
            };
            
            // Basic validation
            if (!formData.firstName || !formData.lastName || !formData.workEmail || !formData.company) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (!validateEmail(formData.workEmail)) {
                alert('Please enter a valid work email address');
                return;
            }
            
            // Here you would typically send the data to your backend
            console.log('Corporate account request:', formData);
            
            // Show success message and hide form
            corporateForm.style.display = 'none';
            contactSuccess.style.display = 'block';
            
            // Auto-close modal after 3 seconds
            setTimeout(() => {
                corporateModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Reset form and show it again
                setTimeout(() => {
                    corporateForm.style.display = 'block';
                    contactSuccess.style.display = 'none';
                    corporateForm.reset();
                }, 500);
            }, 3000);
        });

        // Handle Enter key in email field
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loginForm.dispatchEvent(new Event('submit'));
            }
        });

        // Focus email input on page load
        window.addEventListener('load', function() {
            emailInput.focus();
        });
    </script>
</body>
</html> 

