<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index(){
        $data['categories'] = Category::where('status', 1)->orderBy('name', 'asc')->take(6)->get();
        $data['alll_categories'] = Category::where('status', 1)->orderBy('name', 'asc')->get();
        $data['jobs'] = Job::where(['status' =>  1, "is_featured" => 1])->orderBy('id', 'desc')->take(8)->get();

        $data['jobs_latest'] = Job::where(['status' =>  1, "is_featured" => 0])->orderBy('id', 'desc')->take(6)->get();
        return view('front.home', $data);
    }
}
