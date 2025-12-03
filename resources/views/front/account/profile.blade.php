@extends('front.layouts.app')
@section('content')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                <form action="" method="post" id="user_info_form">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body  p-4">
                            <div id="alert-profile">

                            </div>
                            <h3 class="fs-4 mb-1">My Profile</h3>
                            <div class="mb-4">
                                <label for="" class="mb-2">Name*</label>
                                <input type="text" placeholder="Enter Name" name="name"   id="name" class="form-control" value="{{ $user->name }}">
                                <p class="error_sms"></p>
                                
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Email*</label>
                                <input type="text" placeholder="Enter Email" class="form-control"  name="email" id="email" value="{{ $user->email }}">
                                <p class="error_sms"></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Designation*</label>
                                <input type="text" placeholder="Designation" class="form-control" name="designation"  id="designation" value="{{ $user->designation }}">
                                <p class="error_sms"></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Mobile*</label>
                                <input type="text" placeholder="Mobile" class="form-control"  id="mobile"  name="mobile" value="{{ $user->mobile }}">
                                <p class="error_sms"></p>
                            </div>                        
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>

                <div class="card border-0 shadow mb-4">
                    <form action="post" id="reset_password_form">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Change Password</h3>
                            <div id="alert-password-reset"></div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Old Password*</label>
                                <input type="password" name="password" id="password"  placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">New Password*</label>
                                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Confirm Password*</label>
                                <input type="password" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm Password" class="form-control">
                                <p></p>
                            </div>                        
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
</section>
@endsection


@section('customJs')
<script>
    $("#user_info_form").submit(function(e){
        e.preventDefault();
        $("#name").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#email").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#designation").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#mobile").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $.ajax({
            url: '{{ route("account.profile.update") }}',
            type: 'post',
            data: $("#user_info_form").serializeArray(),
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

                    alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Profile updated successfully.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`
                    $("#alert-profile").html(alert);
                    setInterval(() => {
                        $(".btn-close").click();
                    }, 2000);

                }
            }
        })
    })


    $("#reset_password_form").submit(function(e){
        e.preventDefault();

        $("#password").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#new_password").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');
        $("#confirm_new_password").removeClass("is-invalid").siblings('p').removeClass('invalid-feedback').html('');

        $.ajax({
            url: '{{ route("account.password.update") }}',
            type: 'post',
            data: $("#reset_password_form").serializeArray(),
            dataType: 'json',
            success: function(response){
                if(response.status == false){
                    var errors = response.errors;
                    
                    for (let field in errors) {
                            let message = errors[field][0]; // first error
                            
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

                    alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Profile updated successfully.
                                <button type="button" class="btn-close-password" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`
                    $("#alert-password-reset").html(alert);
                    setInterval(() => {
                        $(".btn-close-password").click();
                    }, 2000);

                }
            }
        })
    });

    
    $("#profile_pic_form").submit(function(e){
        e.preventDefault();
        $("#img_error").html('');
        var formData = new FormData(this);
       
        $.ajax({
            url: '{{ route("account.profile-pic.update") }}',
            type: 'post',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == false){
                   var errors = response.errors;

                   if(errors.image){
                        $("#img_error").html(errors.image);
                   }
                }
                else {
                    img_path = response.img_path;
                    $("#profil-pic").attr("src", img_path );
                    $("#profile_pic_modal").modal('hide');
                    $('#image').val(''); // clears file input
                }
            }
        })
    });

</script>
@endsection