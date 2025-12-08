@extends('front.layouts.app')
@section('content')
<section class="section-3 py-5 bg-2 ">
    <div class="container">     
        <div class="row">
            <div class="col-6 col-md-10 ">
                <h2>Find Jobs</h2>  
            </div>
            <div class="col-6 col-md-2">
                <div class="align-end">
                    <select name="sort" id="sort" class="form-control" >
                        <option value="latest" @if(request()->get('sort') == "latest") selected @endif>Latest</option>
                        <option value="oldest" @if(request()->get('sort') == "oldest") selected @endif>Oldest</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row pt-5">
            <div class="col-md-4 col-lg-3 sidebar mb-4">
                <form id="filter_form" action="">
                    <div class="card border-0 shadow p-4">
                        <div class="mb-4">
                            <h2>Keywords</h2>
                            <input type="text" placeholder="Keywords" name="keyword" id="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                        </div>

                        <div class="mb-4">
                            <h2>Location</h2>
                            <input type="text" placeholder="Location" class="form-control" name="location" id="location" value="{{ request()->get('location') }}">
                        </div>

                        <div class="mb-4">
                            <h2>Category</h2>
                            <select name="category" id="category" class="form-control">
                                <option value="">Select a Category</option>
                                @if($categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(request()->get('category') == $category->id) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>                   

                        <div class="mb-4">
                            <h2>Job Type</h2>
                            @if($jobTypes->count() == 0)
                                <p>No job types found.</p>
                            @else
                                @foreach($jobTypes as $jobType)
                                    <div class="form-check mb-2"> 
                                        <input class="form-check-input " name="job_type" type="checkbox" value="{{ $jobType->id}}" id="job-type-{{ $jobType->id}}"
                                        @if(request()->get('job_type'))
                                            @php
                                                $selectedJobTypes = explode(',', request()->get('job_type'));
                                            @endphp
                                            @if(in_array($jobType->id, $selectedJobTypes)) checked @endif 
                                        @endif  
                                        >     
                                        <label class="form-check-label " for="job-type-{{ $jobType->id}}">{{ $jobType->name }}</label>
                                    </div>
                                @endforeach
                            @endif  
                        </div>

                        <div class="mb-4">
                            <h2>Experience</h2>
                            <select name="experience" id="experience" class="form-control">
                                <option value="">Select Experience</option>
                                @for($i=1; $i<=10; $i++)
                                    <option value="{{ $i}}"  @if(request()->get('experience') == $i) selected @endif> {{ $i}} Years</option>
                                @endfor
                                <option value="10+" @if(request()->get('experience') == "10+") selected @endif>10+ Years</option>
                            </select>
                        </div>  
                        
                        <button type="submit" class="btn btn-primary mb-3">Search</button>
                        <button class="btn btn-secondary" type="reset" id="reset_form_val">Reset</button>

                    </div>
                </form>
            </div>
            <div class="col-md-8 col-lg-9 ">
                <div class="job_listing_area">                    
                    <div class="job_lists">
                        <div class="row">
                            @if($jobs->count() == 0)
                                <div class="col">
                                    <p>No jobs found.</p>
                                </div>
                            @else
                                @foreach($jobs as $job)
                                    <div class="col-md-4">
                                        <div class="card border-0 p-3 shadow mb-4">
                                            <div class="card-body">
                                                <h3 class="border-0 fs-5 pb-2 mb-0">{{ $job->title }}</h3>
                                                
                                                <p> {!! Str::limit(strip_tags($job->description), 20) !!}</p>
                                                <div class="bg-light p-3 border">
                                                    <p class="mb-0">
                                                        <span class="fw-bolder"><i class="fa fa-map-marker"></i></span>
                                                        <span class="ps-1">{{ $job->location }}</span>
                                                    </p>
                                                    <p class="mb-0">
                                                        <span class="fw-bolder"><i class="fa fa-clock-o"></i></span>
                                                        <span class="ps-1">{{ $job->jobType->name }}</span>
                                                    </p>
                                                    @if($job->salary)
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-usd"></i></span>
                                                            <span class="ps-1">{{ $job->salary}}</span>
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="d-grid mt-3">
                                                    <a href="{{ route('job.detail', $job->id) }}" class="btn btn-primary btn-lg">Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif 
                            
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
    $("#filter_form").submit(function(e){
        e.preventDefault(); 
        
        url = '{{ route("jobs") }}?';
        // Fetch form data   
        var keyword = $("#keyword").val();
        var location = $("#location").val();
        var category = $("#category").val();
        var sort = $("#sort").val();
        var job_type = [];
        $("input[name='job_type']:checked").each(function(){
            job_type.push($(this).val());  
        });
        var experience = $("#experience").val();

        if($.trim(keyword) !=""){
            url += 'keyword=' + keyword;
        }
        if(location !=""){
            url += '&location=' + location;
        }
        if(category !=""){
            url += '&category=' + category;
        }
        if(job_type.length > 0){
            url += '&job_type=' + job_type.join(',');
        }
        if(experience !=""){
            url += '&experience=' + experience;
        }   

        if(sort !=""){
            url += '&sort=' + sort;
        }   

       window.location.href = url;
    
    });

    $("#reset_form_val").click(function(){
        window.location.href = '{{ route("jobs") }}';
    });

    $("#sort").change(function(){
        $("#filter_form").submit();
    });

</script>
@endsection