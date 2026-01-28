<?php

namespace App\Http\Controllers;

use App\Models\Student;
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
                $q->where('status', 'pending')->orderBy('due_date');
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
        $file->move(public_path('documents/students'), $fileName);

        Document::create([
            'student_id' => $student->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_path' => 'documents/students/' . $fileName,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize() / 1024, // KB
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
