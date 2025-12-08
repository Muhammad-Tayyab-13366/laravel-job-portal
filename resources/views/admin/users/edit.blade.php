@extends('front.layouts.app')
@section('content')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('admin.sidebar')
            </div>
            <div class="col-lg-9">
            <form action="" method="post" id="user_info_form">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body  p-4">
                            <div id="alert-profile">

                            </div>
                            <h3 class="fs-4 mb-1">User Profile</h3>
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
                            <input type="hidden" name="user_id" value="{{ $user->id }}">                   
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
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
            url: '{{ route("admin.users.update") }}',
            type: 'post',
            data: $("#user_info_form").serializeArray(),
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
                    window.location.href = '{{ route("admin.users") }}';    

                }
            }
        })
    })

</script>
@endsection