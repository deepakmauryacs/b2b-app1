<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8" />
    <title>Register | Deal24hours</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="h-100">
<div class="d-flex flex-column h-100 p-3">
    <div class="d-flex flex-column flex-grow-1">
        <div class="row h-100">
            <div class="col-xxl-7">
                <div class="row justify-content-center h-100">
                    <div class="col-lg-6 py-lg-5">
                        <div class="d-flex flex-column h-100 justify-content-center">
                            <div class="auth-logo mb-2">
                                <a href="{{ url('/') }}" class="logo-dark">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" height="34" alt="logo dark" />
                                </a>
                            </div>

                            <h2 class="fw-bold fs-24">Create Account</h2>

                            <div id="form-messages"></div>

                            <form id="registerForm"  action="{{ url('user/register') }}" autocomplete="off">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label d-block">Register As</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="vendor" value="vendor" checked />
                                        <label class="form-check-label" for="vendor">Vendor</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="buyer" value="buyer" />
                                        <label class="form-check-label" for="buyer">Buyer</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="name">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" autocomplete="off" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" autocomplete="off" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" autocomplete="off" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm password" autocomplete="new-password" />
                                </div>

                                {{-- CAPTCHA --}}
                                <div class="mb-3 d-flex align-items-center">
                                    <label class="form-label me-2 mb-0" for="captcha">What is</label>
                                    <strong id="math-question">{{ session('captcha_question') ?? '5 + 4' }}</strong>
                                    <button type="button" id="refresh-captcha" class="btn btn-sm btn-outline-secondary ms-2" title="Refresh"><i class="bi bi-arrow-clockwise"></i></button>
                                </div>
                                <div class="mb-3">
                                 <input type="text" name="captcha" id="captcha" class="form-control" placeholder="Enter answer"  autocomplete="off"/>
                                </div>

                                <div class="mb-1 text-center d-grid">
                                    <button class="btn btn-soft-primary" type="submit">Register</button>
                                </div>
                            </form>

                            <p class="text-dark text-center mt-3">
                                Already have an account?
                                <a href="{{ route('login') }}" class="fw-bold text-primary ms-1">Sign In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-xxl-5 d-none d-xxl-flex">
                <div class="card h-100 mb-0 overflow-hidden">
                    <div class="d-flex flex-column h-100 justify-content-center align-items-center p-4">
                        <h3 class="fw-bold mb-3">Welcome to Deal24hours</h3>
                        <p class="fs-5 text-center">Join our platform to grow your business as a Vendor or get the best deals as a Buyer.</p>
                    </div>
                </div>
            </div> -->

            <div class="col-xxl-5 d-none d-xxl-flex">
                <div class="card h-100 mb-0 overflow-hidden">
                    <div class="d-flex flex-column h-100 justify-content-center align-items-center p-4">
                        <h3 class="fw-bold mb-3">Create your Deal24hours account</h3>
                        <p class="fs-5 text-center">Register as a Vendor to showcase your products or as a Buyer to explore exclusive deals.</p>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
<script src="{{ asset('assets/js/vendor.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        // Cache jQuery selectors
        const $form = $("#registerForm");
        const $formMessages = $("#form-messages");
        const $refreshCaptcha = $("#refresh-captcha");
        
        // Set up CSRF token for AJAX requests (for Laravel/PHP frameworks)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Prevent double submissions
        let isSubmitting = false;

        // Helper functions
        function showError(input, message) {
            const inputId = input.attr('id');
            input.addClass("is-invalid")
                 .attr("aria-invalid", "true")
                 .attr("aria-describedby", inputId + "-error");
            input.next(".invalid-feedback").remove();
            input.after(`<div id="${inputId}-error" class="invalid-feedback d-block">${message}</div>`);
        }

        function clearError(input) {
            input.removeClass("is-invalid")
                 .removeAttr("aria-invalid")
                 .removeAttr("aria-describedby");
            input.next(".invalid-feedback").remove();
        }

        function validateField(input, conditions) {
            for (const [condition, message] of conditions) {
                if (condition) {
                    showError(input, message);
                    return false;
                }
            }
            clearError(input);
            return true;
        }

        // Form submission handler
        $form.on("submit", function (e) {
            e.preventDefault();
            
            if (isSubmitting) return false;
            isSubmitting = true;

            // Clear previous errors
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            $formMessages.html("");

            let valid = true;

            // Validate role (radio)
            valid = validateField($('input[name="role"]').last(), [
                [!$('input[name="role"]:checked').val(), "Please select a role."]
            ]) && valid;

            // Validate name
            const $name = $("#name");
            const nameVal = $name.val().trim();
            valid = validateField($name, [
                [!nameVal, "Please enter your full name."],
                [nameVal.length < 3, "Name must be at least 3 characters."]
            ]) && valid;

            // Validate email
            const $email = $("#email");
            const emailVal = $email.val().trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            valid = validateField($email, [
                [!emailVal, "Please enter your email."],
                [!emailPattern.test(emailVal), "Please enter a valid email address."]
            ]) && valid;

            // Validate phone
            const $phone = $("#phone");
            const phoneVal = $phone.val().trim();
            const phonePattern = /^\d{10,15}$/;
            valid = validateField($phone, [
                [!phoneVal, "Please enter your phone number."],
                [!phonePattern.test(phoneVal), "Phone number must be 10-15 digits."]
            ]) && valid;

            // Validate password
            const $password = $("#password");
            const passwordVal = $password.val();
            valid = validateField($password, [
                [!passwordVal, "Please enter a password."],
                [passwordVal.length < 6, "Password must be at least 6 characters."]
            ]) && valid;

            // Validate confirm password
            const $passwordConfirmation = $("#password_confirmation");
            valid = validateField($passwordConfirmation, [
                [!$passwordConfirmation.val(), "Please confirm your password."],
                [$passwordConfirmation.val() !== $password.val(), "Passwords do not match."]
            ]) && valid;

            // Validate captcha
            const $captcha = $("#captcha");
            const captchaVal = $captcha.val().trim();
            valid = validateField($captcha, [
                [!captchaVal, "Please answer the captcha."],
                [!/^\d+$/.test(captchaVal), "Captcha answer must be a number."]
            ]) && valid;

            // If all fields are valid, proceed with AJAX
            if (valid) {
                const formData = $form.serialize();

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.status) {
                            $formMessages.html(`<div class="alert alert-success">${response.message}</div>`);
                            if (response.redirect) {
                                setTimeout(() => (window.location.href = response.redirect), 2000);
                            }
                        } else {
                            $formMessages.html(`<div class="alert alert-danger">${response.message}</div>`);
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Something went wrong. Try again later.";
                        
                        if (xhr.status === 0) {
                            errorMsg = "Network connection error.";
                        } else if (xhr.status === 422 && xhr.responseJSON) {
                            const res = xhr.responseJSON;
                            errorMsg = res.message || errorMsg;
                            
                            // Show field errors
                            if (res.errors) {
                                $.each(res.errors, function (key, messages) {
                                    const input = $(`[name="${key}"]`);
                                    showError(input, messages[0]);
                                });
                            }
                        } else if (xhr.status === 500) {
                            errorMsg = "Server error occurred.";
                        }
                        
                        $formMessages.html(`<div class="alert alert-danger">${errorMsg}</div>`);
                    },
                    complete: function() {
                        isSubmitting = false;
                    }
                });
            } else {
                isSubmitting = false;
                // Focus on first invalid field
                $(".is-invalid").first().focus();
            }
        });

        // Refresh CAPTCHA
        $refreshCaptcha.on("click", function (e) {
            e.preventDefault();
            $.get("{{ url('/refresh-math-captcha') }}", function (data) {
                $("#math-question").text(data.question);
            });
        });
    });
</script>
</body>
</html>
