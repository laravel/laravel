<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:agency,subagent,customer'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if ($data['user_type'] === 'agency') {
            // إنشاء وكالة جديدة أولاً
            $agency = Agency::create([
                'name' => $data['agency_name'] ?? $data['name'],
                'email' => $data['email'], // Make sure this is properly assigned
                'phone' => $data['phone'],
            ]);
            
            $agencyId = $agency->id;
        } else {
            $agencyId = $data['agency_id'] ?? null;
        }
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
            'agency_id' => $agencyId,
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    /**
     * Redirect based on user type after registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if ($user->isAgency()) {
            return redirect()->route('agency.dashboard');
        } elseif ($user->isSubagent()) {
            return redirect()->route('subagent.dashboard');
        } elseif ($user->isCustomer()) {
            return redirect()->route('customer.dashboard');
        }
        
        return redirect($this->redirectPath());
    }
}
