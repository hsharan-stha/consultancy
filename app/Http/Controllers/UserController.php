<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Show all users except super admin (role_id 1)
        $users = User::where('role_id', '!=', 1)->get();
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        // Get all roles except super admin (role_id 1) - allow student, employee, teacher, hr, etc.
        // You can adjust this to fetch specific roles or all roles
        $roles = Role::where('id', '!=', 1)->get(); // Exclude super admin, include all other roles
        
        // If Company model exists, uncomment this:
        // $companies = \App\Models\Company::all();
        $companies = collect([]); // Empty collection for now
        
        return view('users.create', compact('roles', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'email'=>'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        User::create([
            'name' => $request->name,
            'role_id' => $request->role_id,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Get all roles except super admin (role_id 1)
        $roles = Role::where('id', '!=', 1)->get();
        
        // If Company model exists, uncomment this:
        // $companies = \App\Models\Company::all();
        $companies = collect([]); // Empty collection for now
        
        return view('users.edit', compact('user', 'roles', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Ma'lumotlarni yangilash
        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->email = $request->email;

        // Faqat password bor bo‘lsa yangilanadi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
    
    
}
