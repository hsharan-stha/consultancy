<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use App\Models\User;
use Illuminate\Http\Request;

class CounselorController extends Controller
{
    public function index()
    {
        $counselors = Counselor::with(['user', 'students'])->get();
        return view('consultancy.counselors.index', compact('counselors'));
    }

    public function create()
    {
        // Get users who are admins/editors but not yet counselors
        $users = User::whereIn('role_id', [1, 2])
            ->whereDoesntHave('counselor')
            ->get();
        return view('consultancy.counselors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:counselors,user_id',
            'employee_id' => 'required|string|max:255|unique:counselors,employee_id',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'extension' => 'nullable|string|max:10',
            'max_students' => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = true;

        Counselor::create($validated);

        return redirect()->route('counselors.index')
            ->with('success', 'Counselor created successfully!');
    }

    public function show(Counselor $counselor)
    {
        $counselor->load(['user', 'students', 'applications.student', 'visaApplications.student']);
        return view('consultancy.counselors.show', compact('counselor'));
    }

    public function edit(Counselor $counselor)
    {
        return view('consultancy.counselors.edit', compact('counselor'));
    }

    public function update(Request $request, Counselor $counselor)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|max:255|unique:counselors,employee_id,' . $counselor->id,
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'extension' => 'nullable|string|max:10',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $counselor->update($validated);

        return redirect()->route('counselors.show', $counselor)
            ->with('success', 'Counselor updated successfully!');
    }

    public function destroy(Counselor $counselor)
    {
        $counselor->delete();
        return redirect()->route('counselors.index')
            ->with('success', 'Counselor deleted successfully!');
    }
}
