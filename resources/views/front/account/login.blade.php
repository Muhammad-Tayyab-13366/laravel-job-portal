@extends('front.layouts.app')
@section('content')
<section class="section-5">
    <div class="container my-5">
        <div class="py-lg-2">&nbsp;</div>
        
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                @if(Session::has('success'))
                    <div class="alert alert-success"><p>{{ Session::get('success') }}</p></div>
                @endif
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Login</h1>
                    <form action="" method="post" name="loginForm" id="loginForm">
                        <div class="mb-3">
                            <label for="" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="example@example.com">
                            <p></p>
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">Password*</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password">
                            <p></p>
                        </div> 
                        <div class="justify-content-between d-flex">
                        <button class="btn btn-primary mt-2">Login</button>
                            <a href="forgot-password.html" class="mt-3">Forgot Password?</a>
                        </div>
                    </form>                    
                </div>
                <div class="mt-4 text-center">
                    <p>Do not have an account? <a  href="{{ route('account.registeration') }}">Register</a></p>
                </div>
            </div>
        </div>
        <div class="py-lg-5">&nbsp;</div>
    </div>
</section>
@endsection


@section('customJs')
<script>
    $("#loginForm").submit(function(e){
        e.preventDefault();

        $.ajax({
            url: '{{ route("account.process-login") }}',
            type: 'post',
            data: $("#loginForm").serializeArray(),
            dataType: 'json',
            success: function(response){
                

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
                   window.location.href = '{{ route("account.profile") }}'
                }
            }
        })
    })
</script>
@endsection