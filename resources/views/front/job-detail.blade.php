@extends('front.layouts.app')
@section('content')
<section class="section-4 bg-2">    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('jobs') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                    </ol>
                </nav>
            </div>
        </div> 
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                
                                <div class="jobs_conetent">
                                    <a href="#">
                                        <h4>{{ $job->title }}</h4>
                                    </a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-map-marker"></i> {{ $job->location }}</p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-clock-o"></i> {{ $job->jobType->name }}</p>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="jobs_right">
                                <div class="apply_now">
                                    @if(Auth::check())
                                        <a class="heart_mark @if($is_already_saved > 0) saved-job @endif" 
                                        href="javascript:void(0);" 
                                        onclick="saveJob({{ $job->id }})"> 
                                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if(session()->has('success'))
                            <div class="alert alert-success m-3">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger m-3">
                                {{ session()->get('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                            <h4>Job description</h4>
                            <p>{!! nl2br($job->description) !!}</p>
                        </div>
                        @if($job->responsibilities !='')
                            <div class="single_wrap">
                                <h4>Responsibility</h4>
                                {!! nl2br($job->responsibilities) !!}
                            </div>
                        @endif

                        @if($job->qualifications != '')
                            <div class="single_wrap">
                                <h4>Qualifications</h4>
                                <p>{!! nl2br($job->qualifications) !!}</p>
                            </div>
                        @endif

                        @if($job->benefits != '')
                            <div class="single_wrap">
                                <h4>Benefits</h4>
                                <p>{!! nl2br( $job->benefits) !!}</p>
                            </div>
                        @endif
                        <div class="border-bottom"></div>
                        <div class="pt-3 text-end">
                            @if(Auth::check())
                            <a href="javascript:void(0);" class="btn btn-secondary  @if($is_already_saved>0) disabled @endif " id="saveJobBtn" onclick="saveJob({{ $job->id }})" {{ ($is_already_saved) > 0 ? "disabled" : "" }}>{{ ($is_already_saved) > 0 ? "Saved" : "Save" }}</a>
                            @else 
                            <a href="javascript:void(0);" class="btn btn-secondary disabled">Login to Save</a>
                            @endif
                            @if(Auth::check())
                            <a href="javascript:void(0);" id="applyOnJobBtn"
                                @if(!$is_alrady_applied) onclick="applyOnJob({{ $job->id }})" @endif 
                                class="btn btn-primary @if($is_alrady_applied) disabled @endif">@if(!$is_alrady_applied) Apply @else Already Applied @endif</a>
                            @else
                            <a href="javascript:void(0);" class="btn btn-primary disabled">Login to Apply</a>
                            @endif
                        </div>
                    </div>
                   
                </div>

                <!--  -->
                @if(auth()->user()->id == $job->posted_by)
                <div class="card shadow border-0 mt-4">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                
                                <div class="jobs_conetent">
                                    <h4>Applicstions</h4>
                                </div>
                               
                            </div>
                            
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="bg-light">
                                    <tr>
                                        <td>Job Title</td>
                                        <td>Email</td>
                                        <td>Applied Date</td>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @foreach($job_application as $application)
                                    <tr>    
                                        <td>{{ $application->user->name }}</td>
                                        <td>{{ $application->user->email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($application->applied_at)->format('d M, Y')}}</td>
                                    </tr>
                                    @endforeach 
                                    @if($job_application->count() == 0)
                                    <tr>
                                        <td colspan="3" class="text-center">No applications found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                <!--  -->
            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Job Summery</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Published on: <span>{{ \Carbon\Carbon::parse($job->created_at)->format('d M, Y')}}</span></li>
                                <li>Vacancy: <span>2 {{ $job->vacancies }}</span></li>
                                <li>Salary: <span>{{ $job->salary }}</span></li>
                                <li>Location: <span>{{ $job->location }}</span></li>
                                <li>Job Nature: <span> {{ $job->jobType->name }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card shadow border-0 my-4">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Name: <span>{{ $job->company_name }}</span></li>
                                <li>Locaion: <span>{{ $job->company_location != '' ? $job->company_location : 'N/A' }}</span></li>
                                <li>Webite: 
                                    <span>
                                        @if($job->company_website == '')
                                        N/A
                                        @else
                                        <a href="{{ $job->company_website }}"> {{ $job->company_website }}</a>
                                        @endif
                                  </span>
                                </li>
                            </ul>
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
    
   function applyOnJob(jobId){
        $("#applyOnJobBtn").attr("disabled", true);
        $.ajax({
            url: "{{ route('account.job.apply') }}",
            type: "POST",
            data: {
                job_id: jobId
            },
            success: function(response){
                window.location.reload();
                
            },
            error: function(xhr){
                $("#applyOnJobBtn").attr("disabled", false);
            }
        });
   }

    function saveJob(jobId){
        $("#saveJobBtn").attr("disabled", true);
        $.ajax({
            url: "{{ route('account.job.save') }}",
            type: "POST",
            data: {
                job_id: jobId
            },
            success: function(response){
                window.location.reload();
                
            },
            error: function(xhr){
                $("#saveJobBtn").attr("disabled", false);
            }
        });
   }

   
</script>
@endsection