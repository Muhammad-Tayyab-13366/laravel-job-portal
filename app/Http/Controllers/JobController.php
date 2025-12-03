<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function index(Request $request){
        $data['categories'] = Category::where('status', 1)->orderBy('name', 'asc')->get();
        $data['jobTypes'] = JobType::where('status', 1)->orderBy('name', 'asc')->get();
        $jobs = Job::with('jobType')->where(['status' =>  1]);
        if($request->has('keyword') && !empty($request->keyword)){
            $keyword = $request->keyword;
            $jobs = $jobs->where(function($query) use ($keyword){
                $query->where('title', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%")
                      ->orWhere('keywords', 'like', "%$keyword%");
            });
        }

        if($request->has('location') && !empty($request->location)){
            $location = $request->location;
            $jobs = $jobs->where('location', 'like', "%$location%");
        }

        if($request->has('category') && !empty($request->category)){
            $category = $request->category;
            $jobs = $jobs->where('category_id', $category);
        }

        if($request->has('experience') && !empty($request->experience)){
            $experience = $request->experience;
            $jobs = $jobs->where('experience', '<=', $experience);
        }

        if($request->has('job_type') && !empty($request->job_type)){
            $job_type = $request->job_type;
            $job_type = explode(',', $job_type);
            $jobs = $jobs->whereIn('job_type_id', $job_type);
        }

        if($request->has('sort') && !empty($request->sort)){
            $sort = $request->sort;
            
            if($sort == 'latest'){
                $jobs = $jobs->orderBy('id', 'desc');
            }elseif($sort == 'oldest'){
                $jobs = $jobs->orderBy('id', 'asc');        
            }
        }else{      
            $jobs = $jobs->orderBy('id', 'desc');
        }

        
        $jobs = $jobs->paginate(9);
        $data['jobs'] = $jobs;
        return view('front.jobs', $data);
    }

    public function jobDetail($job_id){

        if(auth()->check()){
        $data['is_alrady_applied'] = JobApplication::where(['job_id' => $job_id, 'user_id' => auth()->user()->id])->exists();
        }else{
            $data['is_alrady_applied'] = false;
        }

        $data['job'] = Job::with('category', 'jobType')->where(['status' => 1, 'id' => $job_id])->first();
        if(!$data['job']){
            return redirect()->route('jobs');
        }
        return view('front.job-detail', $data);

    }

    public function applyOnJob(Request $request){
        if(!$request->has('job_id') || empty($request->job_id)){
            return response()->json(['status' => false, "message" => 'Invalid job.']);
        }

        $job = Job::where(['status' => 1, 'id' => $request->job_id])->first();
        if(!$job){
            return response()->json(['status' => false, 'message' => 'Job not found.']);
        }

        $user = auth()->user();

        if($user->id  == $job->posted_by){
            session()->flash('error', 'You cannot apply on your own job.');
            return response()->json(['status' => false, 'message' => 'You cannot apply on your own job.']);
        }


        if(JobApplication::where(['job_id' => $job->id, 'user_id' => $user->id])->exists()){
            session()->flash('error', 'You have already applied for this job.');
            return response()->json(['status' => false, 'message' => 'You have already applied for this job.']);
        }


        // Here you can add logic to save the application in the database
        // For example, creating a new Application model instance

        JobApplication::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'employer_id' => $job->posted_by
        ]);

        // send notification email to employer (job poster) - optional

        $employerData = User::where('id', $job->posted_by)->first();
        $mailData = [
            'employer' => $employerData,
            'user' => auth()->user(),
            'job' => $job,     
        ];

       

        Mail::to($employerData->email)->send(new JobNotificationEmail($mailData));

        session()->flash('success', 'You have successfully applied for the job.');
        return response()->json(['status' => 'success', 'message' => 'You have successfully applied for the job.']);
    }
        
   
    
}
