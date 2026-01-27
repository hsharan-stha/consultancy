<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Application;
use App\Models\Inquiry;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EditorPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get statistics
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'pending_applications' => Application::whereIn('status', ['draft', 'submitted', 'under_review'])->count(),
            'new_inquiries' => Inquiry::where('status', 'new')->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
        ];

        // Get recent activities
        $recentApplications = Application::with(['student', 'university'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentInquiries = Inquiry::with(['student', 'counselor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingTasks = Task::with(['student', 'assignedTo'])
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return view('editor.dashboard', compact('stats', 'recentApplications', 'recentInquiries', 'pendingTasks'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('editor.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('editor.profile')
            ->with('success', 'Profile updated successfully!');
    }
}
