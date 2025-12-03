@extends('front.layouts.app')
@section('content')
<section class="section-5">
    <div class="container my-5">
        <div class="py-lg-2">&nbsp;</div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Register</h1>
                    <form action="{{ route('account.process-registeration') }}" method="post" name="registeration_form" id="registeration_form">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="mb-2">Name*</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                            <p></p>
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email">
                            <p></p>
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">Password*</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password">
                            <p></p>
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">Confirm Password*</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Enter Confirm Password">
                            <p></p>
                        </div> 
                        <button class="btn btn-primary mt-2">Register</button>
                    </form>                    
                </div>
                <div class="mt-4 text-center">
                    <p>Have an account? <a  href="{{ route('account.login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJs')
<script>
    $("#registeration_form").submit(function(e){
        e.preventDefault();


        $("#name").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#email").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#password").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#confirm_password").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $.ajax({
            url: '{{ route("account.process-registeration") }}',
            type: 'post',
            data: $("#registeration_form").serializeArray(),
            dataType: 'json',
            success: function(response){
                console.log(response)

                if(response.status == false){

                    

                    var errors = response.errors;
                    
                    for (let field in errors) {
                            let message = errors[field][0]; // first error
                            console.log(field)
                            if(field){
                               
                                $("#"+field)
                                .addClass("is-invalid")
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(message)
                            }else{
                                $("#"+field)
                                .removeClass("is-invalid")
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html('')
                            }
                    }
                }
                else {
                    window.location.href = '{{ route("account.login") }}'
                }
            }
        })
    })
    
    
     
</script>
@endsection