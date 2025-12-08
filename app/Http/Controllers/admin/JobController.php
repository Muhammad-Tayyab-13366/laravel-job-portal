<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job as ModelsJob;
use App\Models\JobType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function index(){
        //
        $data['jobs'] = ModelsJob::with(['category', 'jobType', 'employer'])->orderBy("created_at", "DESC")->paginate(10);
        return view('admin.jobs.list', $data);
    }

    function editJob($job_id){
        $data['job'] = ModelsJob::with(['category', 'jobType', 'employer'])->where('id', $job_id)->first();

        $data['categories'] = Category::where('status', 1)->orderBy('name', 'asc')->get();
        $data['jobtypes']   = JobType::where('status', 1)->orderBy('name', 'asc')->get();
        if(auth()->user()->role == 'admin'){
            $data['job'] = ModelsJob::where(['id' => $job_id])->first();
        }else {
            $data['job'] = ModelsJob::where(['id' => $job_id, "posted_by" => Auth::user()->id])->first();
        }
        if(!$data['job']){
            abort(404);
        }

        return view('admin.jobs.edit', $data);

    }

    public function updateJob(Request $request, $job_id){
        $validator = Validator::make($request->all(),[
            "title" => "required|min:5|max:255",
            "category_id" => "required|integer|min:1",
            "job_type_id" => "required|integer|min:1",
            "vacancies" => "required|integer|min:1",
            "location" => "required|min:5|max:255",
            "description" => "required",
            "company_name" => "required|min:5|max:255",
            ],
            [
                "category_id.required" => "Please select a job category.",
                "category_id.integer"  => "Invalid category value.",
                "category_id.min"      => "Please select a valid category.",

                "job_type_id.required" => "Please select a job type.",
                "job_type_id.integer"  => "Invalid job type value.",
                "job_type_id.min"      => "Please select a valid job type.",
            ]

        );

        if($validator->passes()){

            $job = ModelsJob::find($job_id);
            $job->title = $request->title;
            $job->category_id = $request->category_id;
            $job->job_type_id = $request->job_type_id;
            $job->vacancies = $request->vacancies;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->responsibilities = $request->responsibilities;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_website = $request->company_website;
            $job->company_location = $request->company_location;
            $job->is_featured = $request->is_featured;
            $job->save();

            session()->flash('success', 'Job has been updated successfully.');

            return response()->json([
                "status" => true,
                "errors" => []
            ]);

        }else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function deleteJob(Request $request){
        
        $job = ModelsJob::where(['id' => $request->job_id])->first();
        if($job){
            session()->flash('success', 'Job has been deleted successfully.');
            $job->delete();
            return response()->json([
                "status" => true,
                "message" => "Job has been deleted successfully."
            ]);
        }else {
            return response()->json([  
                "status" => false,
                "message" => "Job not found."
            ]); 
        }
    }
}
