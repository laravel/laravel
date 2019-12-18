<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,700" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="/css/app.css">

        <!-- Scripts -->
        <script src="/js/app.js" defer></script>
    </head>
    <body>
    <form id="messages_form" action="{{ route('api.messages.store') }}" method="POST" class="needs-validation" novalidate>
        <div class="text-center mb-4">
            <h1 class="h3 mb-3 font-weight-normal">New Message</h1>
        </div>

        <div class="alert alert-success alert-dismissible" role="alert" id="alert">
        </div>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" class="form-control" required="" autofocus=""/>
            <div class="invalid-feedback" id="name_error" data-message="Name is required">
                Name is required
            </div>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" class="form-control" pattern="^\d{3}-\d{3}-\d{4}$" required="" onkeypress="validatePhone(event)"/>
            <small id="emailHelp" class="form-text text-muted">Allowed format: xxx-xxx-xxxx.</small>
            <div class="invalid-feedback" id="phone_error" data-message="Valid phone is required">
                Valid phone is required
            </div>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" class="form-control w-100" rows="5" required=""></textarea>
            <div class="invalid-feedback" id="message_error" data-message="Message is required">
                Message is required
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-primary ml-2" type="submit">Save</button>
        </div>
    </form>
    <script>
        'use strict';
        window.addEventListener('load', function() {
            let form = document.getElementById('messages_form');

            // Add custom form validation
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                form.classList.add('was-validated');
                if (form.checkValidity()) {

                    let url = form.getAttribute('action');
                    let name = document.getElementById('name');
                    let phone = document.getElementById('phone');
                    let message = document.getElementById('message');

                    // Send AJAX request
                    window.axios.post(url, {
                        name: name.value,
                        phone: phone.value,
                        message: message.value,
                    })
                        .then(function (response) {
                            // Clear form and show message
                            name.value = '';
                            phone.value = '';
                            message.value = '';
                            form.classList.remove('was-validated');
                            $('#alert')
                                .text('Thank you, ' + response.data.name + ', we will process your request soon')
                                .show();
                        })
                        .catch(function (error) {
                            if (error.response) {
                                if (error.response.data.errors) {
                                    let errors = error.response.data.errors;
                                    Object.keys(errors).forEach((field) => {
                                        document.getElementById(field).classList.add('is-invalid');
                                        document.getElementById(field+'_error').textContent = errors[field][0];
                                    })
                                }
                            } else if (error.request) {
                                console.log(error.request);
                            } else {
                                console.log('Error', error.message);
                            }
                        });
                }
            }, false);
        }, false);

        // Allow only digits and "-" for phone number
        // P.S. Doesn't work for 'Paste'
        function validatePhone(e) {
            let key = e.keyCode || e.which;
            key = String.fromCharCode(key);
            let regex = /[0-9]|-/;
            if( !regex.test(key) ) {
                event.returnValue = false;
                if(event.preventDefault) event.preventDefault();
            }
        }
    </script>
    </body>
</html>
