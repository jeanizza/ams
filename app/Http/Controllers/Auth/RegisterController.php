<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Division; 
use App\Models\Section; 

class RegisterController extends Controller
{
  
    use RegistersUsers;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        // Fetch divisions
        $divisions = Division::all();
        return view('auth.register', compact('divisions'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'office' => ['required', 'string'],
            'division' => ['required', 'exists:division_pits,div_id'],
            'section' => ['required', 'exists:section,sec_id'],
            'role' => ['required', 'string'],
        ]);
    }

    protected function create(array $data)
    {
        $division = Division::find($data['division']);
        $section = Section::find($data['section']);

        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'office' => $data['office'],
            'div_name' => $division->div_name, // Save division name instead of ID
            'sec_name' => $section->sec_name, // Save section name instead of ID
            'role' => $data['role'],
        ]);
    }

    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();

        return redirect('/login')->with('success', 'Registration successful. Please login.');
    }
}
