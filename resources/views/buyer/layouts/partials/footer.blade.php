    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.ajax').on('submit', function(e){
                e.preventDefault();
                var form = $(this);
                var valid = true;
                form.find('[required]').each(function(){
                    if(!$(this).val()){
                        valid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                var dateInput = form.find('.date-picker');
                if(dateInput.length && !/^\d{2}-\d{2}-\d{4}$/.test(dateInput.val())){
                    valid = false;
                    dateInput.addClass('is-invalid');
                }
                if(!valid){
                    return;
                }
                $.post(form.attr('action'), form.serialize())
                    .done(function(res){
                        alert(res.message || 'Success');
                        form[0].reset();
                    })
                    .fail(function(xhr){
                        if(xhr.status === 422 && xhr.responseJSON.errors){
                            alert(Object.values(xhr.responseJSON.errors).join('\n'));
                        } else {
                            alert('An error occurred');
                        }
                    });
            });

            if(typeof flatpickr !== 'undefined') {
                $('.date-picker').flatpickr({ dateFormat: 'd-m-Y' });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
