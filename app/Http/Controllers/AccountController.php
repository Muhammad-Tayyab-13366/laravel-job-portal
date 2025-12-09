<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Category;
use App\Models\Job as ModelsJob;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class AccountController extends Controller
{
    //
    public function registeration(){
        return view('front.account.registeration');
    }

    public function processRegisteration(Request $request){
        
        $validator = Validator::make($request->all(),[
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:5|same:confirm_password",
            "confirm_password" => "required|min:5"
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->save();
            session()->flash('success', 'You have register succesfully.');
            return response()->json([
                "status" => true,
                "errors" => []
            ]);

        }else {
            return response()->json([
                "status" => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function login(){
        return view("front.account.login");
    }

    public function processLogin(Request $request){

        // ❗ Stop the request immediately if rate limit exceeded
        if ($response = $this->checkTooManyFailedAttempts($request)) {
            return $response;   // <---- return here and stop
        }

        $validator = Validator::make($request->all(),[
    
            "email" => "required|email",
            "password" => "required"
        ]);

        if($validator->passes()){

            $authenticated = Auth::attempt([
                "email" => $request->email,
                "password" => $request->password
            ]);

            if($authenticated){

                // clear attempts on successful login
                RateLimiter::clear($this->throttleKey($request));

                return response()->json([
                    "status" => true,
                    "errors" => []
                ]);

            }else {

                // increment failed attempts
                RateLimiter::hit($this->throttleKey($request), 60); // block for 60 seconds
                return response()->json([
                    "status" => false,
                    "errors" => [
                        "password" => ["Invalid email or password"]
                    ]
                ]);
            }

        }else {

            // increment failed attempts
            RateLimiter::hit($this->throttleKey($request), 60); // block for 60 seconds

            return response()->json([
                "status" => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function profile(){

        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();
        
        return  view("front.account.profile", ["user" => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user_id = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            "name" => "required|max:20",
            "email" => 'required|email|unique:users,email,'.$user_id.',id',
            "designation" => "required|max:20",
            "mobile" => "required|max:13"
        ]);

        if($validator->passes()){

            $user = User::find($user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();
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

    public function updatePassword(Request $request){
        
        $validator = Validator::make($request->all(),[
            "password" => "required",
            "new_password" => "required|min:5|same:confirm_new_password",
            "confirm_new_password" => "required|min:5"
        ]);

        if($validator->passes()){

            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                "status" => true,
                "errors" => $validator->errors()
            ]);
        }else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function updateProfilePic(Request $request){
        $user_id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            "image" => "required|image"
        ]);

        if($validator->passes()){

            $image = $request->image;
            $ext   = $image->getClientOriginalExtension();
            $img_name = $user_id."-".time().".".$ext;
            
            $image->move(public_path("profile_pic"), $img_name);

            // create a small thumbnail
            $source_path = public_path("profile_pic")."/".$img_name;
            // crop the best fitting 1:1 ratio (200x200) and resize to 200x200 pixel
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($source_path);
            $image->cover(150, 150);
            $image->toPng()->save(public_path("profile_pic/thumb")."/".$img_name);



            User::where('id',$user_id)->update(["image" => $img_name]);
            $img_path = asset('profile_pic/thumb/'.$img_name);
            return response()->json([
                "status" => true,
                "errors" => [],
                "img_path" => $img_path
            ]);
        }else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function createJob(){

        $data['categories'] = Category::where('status', 1)->orderBy('name', 'asc')->get();
        $data['jobtypes']   = JobType::where('status', 1)->orderBy('name', 'asc')->get();
        return view('front.account.job.create', $data);
    }

    public function saveJob(Request $request){

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

            $job = new ModelsJob();
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
            $job->posted_by = Auth::user()->id;
            $job->save();

            session()->flash('success', 'Job has been posted successfully.');

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

    public function myJobs(){
        $user_id = Auth::user()->id;
        $jobs = ModelsJob::with(["category", "jobType"])->where('posted_by', $user_id)->orderBy('created_at', 'desc')->paginate(10);
        
        
        return view('front.account.job.my-jobs', ['jobs' => $jobs]);
    }   

    public function editJob($job_id){
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

        return view('front.account.job.edit', $data);
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
        $jobId = $request->job_id;
        $job = ModelsJob::where(['id' => $jobId, 'posted_by' => Auth::user()->id])->first();
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

    public function myJobApplications(){
        $data['jobs_applications'] = JobApplication::with(['job', 'job.jobType', 'job.category', 'job.applications'])
                                        ->where(['user_id' => auth()->user()->id])
                                        ->orderBy('id', 'desc')
                                        ->paginate(10);
        return view('front.account.job.applied-jobs', $data);
    }

    public function showSavedJobs(){
        $data['saved_jobs'] = SavedJob::with(['job', 'job.jobType', 'job.category', 'job.applications'])
                                    ->where(['user_id' => auth()->user()->id])
                                    ->orderBy('id', 'desc')
                                    ->paginate(10);
        return view('front.account.job.saved-jobs', $data);
    }

    public function removeSavedJob(Request $request){
        $jobId = $request->job_id;
        $savedJob = SavedJob::where(['job_id' => $jobId, 'user_id' => auth()->user()->id])->first();
        if($savedJob){
            $savedJob->delete();
            session()->flash('success', 'Saved job has been removed successfully.');
            return response()->json([
                "status" => true,
                "message" => "Saved job has been removed successfully."
            ]);
        }else {
            session()->flash('error', 'Saved job not found.');
            return response()->json([  
                "status" => false,
                "message" => "Saved job not found."
            ]); 
        }
    }

    public function forgetPassword(){
        return view('front.account.forget-password');
    }
    
    public function processForgetPassword(Request $request){
        
        $validator = Validator::make($request->all(),[
            "email" => "required|email|exists:users,email"
        ],
        [
            "email.exists" => "The email does not exist in our records."
        ]);

        if($validator->passes()){
            // Here you can implement the logic to send a password reset link to the user's email.
            $token = Str::random(60);
            //$token = Hash::make($token);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            $user = User::where('email', $request->email)->first();
            $mailData = [
                'token' => $token,
                'user' => $user
            ];

            Mail::to($request->email)->send(new ResetPasswordEmail($mailData));
            

            // For now, we'll just flash a success message.
            session()->flash('success', 'A password reset link has been sent.');
            return redirect()->back();
        }else {
            return  redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    }

    public function resetPassword($token){
        
        $data = DB::table('password_reset_tokens')->where('token', $token)->first();
        if(!$data){
            abort(404);
        }

        $email = $data->email;

        return view('front.account.reset-password', ['email' => $email, 'token' => $token]);
    }

    public function processResetPassword(Request $request){
        
        $validator = Validator::make($request->all(),[

            "password" => "required|min:5|same:confirm_password",
            "confirm_password" => "required|min:5",
            "token" => "required"
        ]);

        if($validator->passes()){
            $data = DB::table('password_reset_tokens')->where('token', $request->token)->first();
           
            if(!$data){
                session()->flash('error', 'Invalid or expired token.');
                return  redirect()->back()
                ->withErrors(['token' => 'Invalid or expired token.'])
                ->withInput();
            }

            

            $user = User::where('email', $data->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            session()->flash('success', 'Your password has been reset successfully.');
            return redirect()->route('account.login');

        }else {
            return  redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->email) . '|' . $request->ip();
    }

    protected function checkTooManyFailedAttempts(Request $request)
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {

            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            // ❗ Return the JSON response (do not send)
            return response()->json([
                "status" => false,
                "errors" => [
                    "email" => ["Too many login attempts. Try again in $seconds seconds."]
                ]
            ]);

            exit;
        }
        return null; // no rate limit, continue normally
    }

}
