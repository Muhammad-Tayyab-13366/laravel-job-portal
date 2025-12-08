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
                <form action="" id="job_create_form">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow mb-4 ">
                        <div class="card-body card-form p-4">
                            <h3 class="fs-4 mb-1">Job Details</h3>
                            <input type="hidden" id="job_id" value="{{ $job->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="" class="mb-2">Title<span class="req">*</span></label>
                                    <input type="text" placeholder="Job Title" id="title" name="title" name="title" class="form-control" value="{{ $job->title }}">
                                </div>
                                <div class="col-md-6  mb-4">
                                    <label for="" class="mb-2">Category<span class="req">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select a Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id}}" @if($category->id == $job->category_id) selected @endif >{{ $category->name}}</option>
                                        @endforeach
                                       
                                       
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="" class="mb-2">Job Nature<span class="req">*</span></label>
                                    <select class="form-select form-control" name="job_type_id" id="job_type_id">
                                        <option value="">Select Job Type</option>
                                       @foreach($jobtypes as $jobtype)
                                            <option value="{{ $jobtype->id}}" @if($jobtype->id == $job->job_type_id) selected @endif>{{ $jobtype->name}}</option>
                                       @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6  mb-4">
                                    <label for="" class="mb-2">Vacancy<span class="req">*</span></label>
                                    <input type="number" min="1" placeholder="Vacancy" id="vacancies" name="vacancies" class="form-control" value="{{ $job->vacancies }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label for="" class="mb-2">Salary</label>
                                    <input type="text" placeholder="Salary" id="salary" name="salary" class="form-control" value="{{ $job->salary }}">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label for="" class="mb-2">Location<span class="req">*</span></label>
                                    <input type="text" placeholder="location" id="location" name="location" class="form-control" value="{{ $job->location }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="" class="mb-2">Description<span class="req">*</span></label>
                                <textarea class="form-control froamt_textarea" name="description" id="description" cols="5" rows="5" placeholder="Description">{!! $job->description !!}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Benefits</label>
                                <textarea class="form-control froamt_textarea" name="benefits" id="benefits" cols="5" rows="5" placeholder="Benefits">{!! $job->benefits !!}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Responsibility</label>
                                <textarea class="form-control froamt_textarea" name="responsibilities" id="responsibilities" cols="5" rows="5" placeholder="Responsibility">{!! $job->responsibilities !!}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Qualifications</label>
                                <textarea class="form-control froamt_textarea" name="qualifications" id="qualifications" cols="5" rows="5" placeholder="Qualifications">{!! $job->qualifications !!}</textarea>
                            </div>
                            
                            

                            <div class="mb-4">
                                <label for="" class="mb-2">Keywords</label>
                                <input type="text" placeholder="keywords" id="keywords" name="keywords" class="form-control" value="{{ $job->keywords }}">
                            </div>

                            <div class="mb-4">
                                <label for="" class="mb-2">Experience</label>
                                
                                <select name="experience" id="experience"  class="form-control">
                                    <option value="0">Select Experience</option>
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i}}" @if($job->experience == $i) selected @endif> {{ $i}} Years</option>
                                    @endfor
                                    <option value="10+"  @if($job->experience == "10+") selected @endif>10+ Years</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="" class="mb-2">Is Feature</label>
                                
                                <select name="is_featured" id="is_featured"  class="form-control">
                                    <option value="1" @if($job->is_featured == 1) selected @endif>Yes</option>
                                    <option value="0" @if($job->is_featured == 0) selected @endif>No</option>
                                </select>
                            </div>

                            
                            <h3 class="fs-4 mb-1 mt-5 border-top pt-5">Company Details</h3>

                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label for="" class="mb-2">Name<span class="req">*</span></label>
                                    <input type="text" placeholder="Company Name" id="company_name" name="company_name" class="form-control" value="{{ $job->company_name }}">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label for="" class="mb-2">Location</label>
                                    <input type="text" placeholder="Location" id="company_location" name="company_location" class="form-control" value="{{ $job->company_location }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="" class="mb-2">Website</label>
                                <input type="text" placeholder="Website" id="company_website" name="company_website" class="form-control" value="{{ $job->company_website }}">
                            </div>
                        </div> 
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update Job</button>
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
    $('.froamt_textarea').trumbowyg();

    $("#job_create_form").submit(function(e){
        $("button[type='submit']").prop('disabled', true);
        e.preventDefault();
        let jobId = $("#job_id").val();
        // Remove old errors (works for all fields)
        $(".form-control").removeClass("is-invalid");
        $(".invalid-feedback").remove();

        $.ajax({
            url: "{{ route('admin.jobs.update', ':id') }}".replace(':id', jobId),
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response){

                $("button[type='submit']").prop('disabled', false);
                if(response.status === false){
                    let errors = response.errors;

                    $.each(errors, function(field, messages){

                        let input = $("#" + field);

                        input.addClass("is-invalid");

                        // Insert error message AFTER the input
                        input.after(
                            `<div class="invalid-feedback d-block">${messages[0]}</div>`
                        );
                    });
                }
                else {
                    
                    window.location.href = "{{ route('admin.jobs') }}";
                }
            }
        });
    });
    
</script>
@endsection
