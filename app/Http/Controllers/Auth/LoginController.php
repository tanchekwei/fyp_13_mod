<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Auth;
use App\Student;
use App\Staff;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider($provider)
    {
        Session::put('staffId','chekwei');
        Auth::guard('staff')->login(Staff::where('email',"chekweitan@gmail.com")->first());
        $this->redirectTo = "/viewprojectlist";
        return redirect($this->redirectTo);
//        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        $authuser = $this->findOrCreateUser($user,$provider);
        if($authuser && $authuser->status == 'active')
        {
            if($authuser instanceof Staff)
            {
                if($authuser->role == 'admin' || $authuser->role == 'facultyadmin')
                {
                    Auth::guard('staff')->login($authuser);
                    $this->redirectTo = "/Staffindex";
                }
                if($authuser->role == 'fypcommittee' || $authuser->role == 'supervisor')
                {
                    Auth::guard('staff')->login($authuser);
                    $this->redirectTo = "/showallcohort";
                }
                if($authuser->role == 'lecturer')
                {
                    return redirect('/')->with(['fail'=>'You have not authority to access!!']);
                }
            }
            else if($authuser instanceof Student)
            {
                Auth::guard('student')->login($authuser);
                $this->redirectTo = "/index";
            }
        }
        else
        {
            return redirect('/')->with(['fail'=>'You have not authority to access!!']);
        }
        return redirect($this->redirectTo);
    }

    public function findOrCreateUser($user,$provider)
    {
        $authuser = Staff::where('email',$user->email)->first();
        if($authuser)
        {
            Session::put('staffId',$authuser->staffId);
            return $authuser;
        }
        else
        {
            $authuser = Student::where('TARCemail',$user->email)->first();
            if($authuser)
            {
                Session::put('studentId',$authuser->studentId);
				Session::put('cohortId',$authuser->cohortId);
                return $authuser;
            }
        }
    }
}
