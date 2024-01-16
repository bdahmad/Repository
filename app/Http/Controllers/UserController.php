<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Auth;

class UserController extends Controller{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    public function index(){
        $all=User::where('status',1)->orderBy('id','DESC')->get();
        return view('admin.user.all',compact('all'));
    }

    public function add(){
        return view('admin.user.add');
    }

    public function edit(){
        return view('admin.user.edit');
    }

    public function view(){
        return view('admin.user.view');
    }

    public function insert(Request $request){
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|max:50|unique:users',
            'password'=>'required|min:8',
            'confirm_password'=>'required_with:password|same:password',
            'username'=>'required',
            // 'role'=>'required',
        ],[
            'name.required'=>'Please enter your name',
            'email.required'=>'Please enter your email',
            'password.required'=>'Please enter password',
            'confirm_password.required'=>'Please enter your confirm password', 
            'username.required'=>'Please enter your confirm password', 
            // 'role.required'=>'Please select your role', 
        ]);

        $slug='U'.uniqid('20');

        $insert=User::insertGetId([
            'name'=>$request['name'],
            'phone'=>$request['phone'],
            'email'=>$request['email'],
            'username'=>$request['username'],
            'password'=>Hash::make($request['password']),
            // 'role'=>$request['role'],
            'slug'=>$slug,
            'created_at'=>Carbon::now('asia/dhaka')->toDateTimeString(),
        ]);

        if($request->hasfile('pic')){
            $manager = new ImageManager(new Driver());
            $image = $request->file('pic');
            $imageName = 'user_'.time().'.'.$image->getClientOriginalName(); 
            $image = $manager->read($image);
            $image = $image->resize(300,300);
            $image->save('uploads/user/'.$imageName);

            User::where('id',$insert)->update([
                'photo' => $imageName,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }


        if($insert){
            Session::flash('success','Successfully completed user registration');
            return redirect('dashboard/user/add');
        }else{
            Session::flash('error','Opps operation failed');
            return redirect('dashboard/user/add');
        }
    }

    public function update(){

    }

    public function softdelete(){

    }

    public function restore(){

    }

    public function delete(){

    }
}
