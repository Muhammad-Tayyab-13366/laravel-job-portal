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
                @if(Session::has('error'))
                    <div class="alert alert-danger"><p>{{ Session::get('error') }}</p></div>
                @endif
                
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Reset Password</h1>
                    <form action="{{ route('account.reset-password-save') }}" method="post" name="password_reset_form" id="password-reset-form">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="example@example.com" value="{{ $email}}" disabled>
                            <p></p>
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">New Password*</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password">
                            @error('password')
                                <p class="text-danger invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div> 
                        <div class="mb-3">
                            <label for="" class="mb-2">Confirm New Password*</label>
                            <input type="password" name="confirm_password" id="password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Enter Password">
                            @error('confirm_password')
                                <p class="text-danger invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div> 
                        <input type="hidden" name="token" value="{{ $token }}">
                        <button class="btn btn-primary mt-2">Reset Password</button>
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
    
</script>
@endsection