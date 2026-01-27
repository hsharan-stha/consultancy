<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\University;
use App\Models\Counselor;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['counselor.user', 'targetUniversity']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by counselor
        if ($request->filled('counselor_id')) {
            $query->where('counselor_id', $request->counselor_id);
        }

        // Filter by intake
        if ($request->filled('intake')) {
            $query->where('target_intake', $request->intake);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        $counselors = Counselor::with('user')->where('is_active', true)->get();
        
        return view('consultancy.students.index', compact('students', 'counselors'));
    }

    public function create()
    {
        $universities = University::orderBy('name')->get();
        $counselors = Counselor::with('user')->where('is_active', true)->get();
        return view('consultancy.students.create', compact('universities', 'counselors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'counselor_id' => 'nullable|exists:counselors,id',
            'target_university_id' => 'nullable|exists:universities,id',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = 'student_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/students'), $photoName);
            $validated['photo'] = 'images/students/' . $photoName;
        }

        // Get Student role
        $studentRole = Role::where('role', 'Student')->first();
        if (!$studentRole) {
            return back()->withErrors(['error' => 'Student role not found. Please seed the database first.'])->withInput();
        }

        // Create User account for login
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'), // Default password if not provided
            'role_id' => $studentRole->id,
            'email_verified_at' => now(), // Auto-verify email for admin-created accounts
        ]);

        // Add user_id to validated data
        $validated['user_id'] = $user->id;

        $student = Student::create($validated);

        $message = 'Student registered successfully! ID: ' . $student->student_id;
        if (!$request->filled('password')) {
            $message .= ' Default password: password';
        }

        return redirect()->route('consultancy.students.show', $student)
            ->with('success', $message);
    }

    public function show(Student $student)
    {
        $student->load([
            'counselor.user', 'targetUniversity', 'documents',
            'applications.university', 'visaApplications', 'payments',
            'tasks', 'communications.user', 'inquiries'
        ]);
        return view('consultancy.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $universities = University::orderBy('name')->get();
        $counselors = Counselor::with('user')->where('is_active', true)->get();
        return view('consultancy.students.edit', compact('student', 'universities', 'counselors'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id . '|unique:users,email,' . ($student->user_id ?? 'NULL'),
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'status' => 'required|string',
            'counselor_id' => 'nullable|exists:counselors,id',
            'target_university_id' => 'nullable|exists:universities,id',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo && file_exists(public_path($student->photo))) {
                unlink(public_path($student->photo));
            }
            $photo = $request->file('photo');
            $photoName = 'student_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/students'), $photoName);
            $validated['photo'] = 'images/students/' . $photoName;
        }

        // Update or create User account
        if ($student->user_id) {
            // Update existing user
            $user = User::find($student->user_id);
            if ($user) {
                $user->update([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                ]);
                
                // Update password if provided
                if ($request->filled('password')) {
                    $user->update([
                        'password' => Hash::make($validated['password']),
                    ]);
                }
            }
        } else {
            // Create new user if student doesn't have one
            $studentRole = Role::where('role', 'Student')->first();
            if ($studentRole) {
                $user = User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'] ?? 'password'),
                    'role_id' => $studentRole->id,
                    'email_verified_at' => now(),
                ]);
                $validated['user_id'] = $user->id;
            }
        }

        $student->update($validated);

        return redirect()->route('consultancy.students.show', $student)
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('consultancy.students.index')
            ->with('success', 'Student deleted successfully!');
    }
}
