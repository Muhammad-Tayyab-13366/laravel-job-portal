<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(){
        //
        $data["job_applications"] = JobApplication::with(['job', 'user', 'job.category', 'job.employer'])->orderBy("created_at", "DESC")->paginate(10);
        return view('admin.jobs.job-applications', $data);
    }
}
