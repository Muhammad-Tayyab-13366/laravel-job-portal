@extends('front.layouts.app')
@section('content')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">My Jobs</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Jobs Applied</h3>
                            </div>
                          
                            
                        </div>
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Applied at</th>
                                        <th scope="col">Applicants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @if($jobs_applications->count() == 0)
                                        <tr>
                                            <td colspan="5" class="text-center">No jobs found.</td>
                                        </tr>
                                    @else
                                        @foreach($jobs_applications as $jobs_application)
                                            <tr class="active">
                                                <td>
                                                    <div class="job-name fw-500">{{ $jobs_application->job->title }}</div>
                                                    <div class="info1">{{ $jobs_application->job->category->name }} . {{ $jobs_application->job->location }}</div>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($jobs_application->applied_at)->format('d M, Y')  }}</td>
                                                <td>{{ $jobs_application->job->applications->count() }} Applications</td>
                                                <td>
                                                    @if($jobs_application->job->status == 1) 
                                                        <div class="job-status text-capitalize">Active</div>
                                                    @else 
                                                        <div class="job-status text-capitalize">Block</div>
                                                    @endif
                                                        
                                                   
                                                </td>
                                                <td>
                                                    <div class="action-dots float-end">
                                                        <a href="#" class="" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="{{ route('job.detail', $jobs_application->job_id)}}"> <i class="fa fa-eye" aria-hidden="true"></i> View</a></li>
                                                            <!-- <li><a class="dropdown-item" href=""><i class="fa fa-edit" aria-hidden="true"></i> Edit</a></li>
                                                            <li><a class="dropdown-item" href="javascript:void(0);" onclick=""><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li> -->
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    
                                </tbody>
                                
                            </table>
                        </div>
                        <div>
                            {{ $jobs_applications->links() }}
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</section>
@endsection


@section('customJs')
<script>
    $("#job_create_form").submit(function(e){
        e.preventDefault();

        // Remove old errors (works for all fields)
        $(".form-control").removeClass("is-invalid");
        $(".invalid-feedback").remove();

        $.ajax({
            url: '{{ route("account.job.create") }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response){

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
                    
                    window.location.href = "{{ route('account.job.my-jobs') }}";
                }
            }
        });
    });

    function deleteJob(jobId){
        if(confirm("Are you sure you want to delete this job?")){
            $.ajax({
                url: '{{ route("account.job.delete") }}',
                type: 'post',
                data: { job_id: jobId },
                dataType: 'json',
                success: function(response){
                    if(response.status === true){
                        
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    }




</script>
@endsection