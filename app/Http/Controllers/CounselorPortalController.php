<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use App\Models\Student;
use App\Models\Application;
use App\Models\Inquiry;
use App\Models\Task;
use App\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CounselorPortalController extends Controller
{
    private function getAuthenticatedCounselor()
    {
        $user = Auth::user();
        return Counselor::where('user_id', $user->id)->first();
    }

    public function dashboard()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        // Get statistics
        $stats = [
            'total_students' => $counselor->students()->count(),
            'active_students' => $counselor->students()->where('status', 'active')->count(),
            'pending_applications' => $counselor->applications()
                ->whereIn('status', ['draft', 'submitted', 'under_review'])
                ->count(),
            'pending_tasks' => Task::where('assigned_to', $counselor->user_id)
                ->where('status', 'pending')
                ->count(),
            'unread_messages' => $counselor->students()
                ->withCount(['communications as unread_count' => function($q) {
                    $q->where('direction', 'incoming')
                      ->where('is_read', false);
                }])
                ->get()
                ->sum('unread_count'),
        ];

        // Get recent students
        $recentStudents = $counselor->students()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending applications
        $pendingApplications = $counselor->applications()
            ->with('student')
            ->whereIn('status', ['draft', 'submitted', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending tasks
        $pendingTasks = Task::with('student')
            ->where('assigned_to', $counselor->user_id)
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Get recent communications
        $recentCommunications = Communication::whereHas('student', function($q) use ($counselor) {
            $q->where('counselor_id', $counselor->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        return view('counselor.dashboard', compact('counselor', 'stats', 'recentStudents', 'pendingApplications', 'pendingTasks', 'recentCommunications'));
    }

    public function students()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        $students = $counselor->students()
            ->with(['applications', 'documents'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('counselor.students', compact('counselor', 'students'));
    }

    public function applications()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        $applications = $counselor->applications()
            ->with(['student', 'university'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('counselor.applications', compact('counselor', 'applications'));
    }

    public function tasks()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        $tasks = Task::with('student')
            ->where('assigned_to', $counselor->user_id)
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return view('counselor.tasks', compact('counselor', 'tasks'));
    }

    public function messages()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        $communications = Communication::whereHas('student', function($q) use ($counselor) {
            $q->where('counselor_id', $counselor->id);
        })
        ->with('student')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('counselor.messages', compact('counselor', 'communications'));
    }

    public function profile()
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        return view('counselor.profile', compact('counselor'));
    }

    public function updateProfile(Request $request)
    {
        $counselor = $this->getAuthenticatedCounselor();
        
        if (!$counselor) {
            return redirect()->route('home')->with('error', 'No counselor profile found.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'extension' => 'nullable|string|max:10',
            'specialization' => 'nullable|string|max:255',
        ]);

        $counselor->update($validated);

        // Update user email if provided
        if ($request->filled('email')) {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $counselor->user_id,
            ]);
            $counselor->user->update(['email' => $request->email]);
        }

        return redirect()->route('counselor.profile')
            ->with('success', 'Profile updated successfully!');
    }
}
