@extends('buyer.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Subscribe to Newsletter</div>
                <div class="card-body">
                    <div id="form-errors" class="alert alert-danger d-none"></div>
                    <div id="success-message" class="alert alert-success d-none"></div>
                    <form id="newsletter-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subscribe_date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="subscribe_date" name="subscribe_date" placeholder="dd-mm-yyyy" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#newsletter-form').on('submit', function (e) {
            e.preventDefault();
            const formErrors = $('#form-errors').addClass('d-none').empty();
            const successMsg = $('#success-message').addClass('d-none').empty();

            const email = $('#email').val();
            const date = $('#subscribe_date').val();
            const datePattern = /^\d{2}-\d{2}-\d{4}$/;
            let errors = [];

            if (!email) {
                errors.push('Email is required');
            }

            if (!datePattern.test(date)) {
                errors.push('Date must be in dd-mm-yyyy format');
            }

            if (errors.length) {
                formErrors.removeClass('d-none').html(errors.join('<br>'));
                return;
            }

            $.ajax({
                url: '{{ route('newsletter.subscribe') }}',
                method: 'POST',
                data: {email: email, subscribe_date: date},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    successMsg.removeClass('d-none').text(response.message);
                    $('#newsletter-form')[0].reset();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let serverErrors = [];
                        $.each(xhr.responseJSON.errors, function (k, msgs) {
                            serverErrors.push(msgs.join('<br>'));
                        });
                        formErrors.removeClass('d-none').html(serverErrors.join('<br>'));
                    } else {
                        formErrors.removeClass('d-none').text('An error occurred.');
                    }
                }
            });
        });
    });
</script>
@endpush
