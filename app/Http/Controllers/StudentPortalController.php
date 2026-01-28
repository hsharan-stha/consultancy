<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Task;
use App\Models\Document;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('home')->with('error', 'No student profile found.');
        }

        $student->load([
            'applications.university',
            'visaApplications',
            'documents',
            'payments',
            'tasks' => function($q) {
                $q->where('status', 'pending')->orderBy('due_date')->with('assignedTo');
            }
        ]);

        // Get document checklist status
        $documentsStatus = [
            'total' => $student->documents->count(),
            'verified' => $student->documents->where('status', 'verified')->count(),
            'pending' => $student->documents->where('status', 'pending')->count(),
            'rejected' => $student->documents->where('status', 'rejected')->count(),
        ];

        // Get application progress
        $activeApplication = $student->applications()
            ->whereNotIn('status', ['enrolled', 'rejected', 'withdrawn'])
            ->latest()
            ->first();

        // Get pending payments
        $pendingPayments = $student->payments()
            ->whereIn('status', ['pending', 'partial'])
            ->get();

        return view('portal.dashboard', compact('student', 'documentsStatus', 'activeApplication', 'pendingPayments'));
    }

    public function profile()
    {
        $student = $this->getAuthenticatedStudent();
        return view('portal.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $student->update($validated);

        return redirect()->route('portal.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function documents()
    {
        $student = $this->getAuthenticatedStudent();
        $documents = $student->documents()->orderBy('created_at', 'desc')->get();
        
        return view('portal.documents', compact('student', 'documents'));
    }

    public function uploadDocument(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:51200',
        ]);

        $file = $request->file('file');
        $fileName = $student->student_id . '_' . time() . '_' . $file->getClientOriginalName();
        $fileSizeKb = round($file->getSize() / 1024);

        $docPath = public_path('documents/students');
        if (!is_dir($docPath)) {
            mkdir($docPath, 0755, true);
        }
        $file->move($docPath, $fileName);

        Document::create([
            'student_id' => $student->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_path' => 'documents/students/' . $fileName,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $fileSizeKb,
            'status' => 'pending',
        ]);

        return redirect()->route('portal.documents')
            ->with('success', 'Document uploaded successfully! It will be reviewed shortly.');
    }

    public function applications()
    {
        $student = $this->getAuthenticatedStudent();
        $applications = $student->applications()
            ->with(['university', 'visaApplication'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('portal.applications', compact('student', 'applications'));
    }

    public function payments()
    {
        $student = $this->getAuthenticatedStudent();
        $payments = $student->payments()
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('portal.payments', compact('student', 'payments'));
    }

    public function tasks()
    {
        $student = $this->getAuthenticatedStudent();
        if (!$student) {
            return redirect()->route('home')->with('error', 'No student profile found.');
        }
        $tasks = $student->tasks()
            ->with('assignedTo')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->get();
        
        return view('portal.tasks', compact('student', 'tasks'));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $student = $this->getAuthenticatedStudent();
        if (!$student) {
            return redirect()->route('home')->with('error', 'No student profile found.');
        }
        if ($task->student_id !== $student->id) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $update = ['status' => $validated['status']];
        if ($validated['status'] === 'completed') {
            $update['completed_at'] = now();
        }

        $task->update($update);

        return redirect()->route('portal.tasks')
            ->with('success', 'Task status updated to ' . str_replace('_', ' ', $validated['status']) . '.');
    }

    public function messages()
    {
        $student = $this->getAuthenticatedStudent();
        $communications = $student->communications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('portal.messages', compact('student', 'communications'));
    }

    public function sendMessage(Request $request)
    {
        $student = $this->getAuthenticatedStudent();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Communication::create([
            'student_id' => $student->id,
            'type' => 'note',
            'direction' => 'incoming',
            'subject' => $validated['subject'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('portal.messages')
            ->with('success', 'Message sent successfully!');
    }

    private function getAuthenticatedStudent()
    {
        return Student::where('user_id', Auth::id())->first();
    }
}
