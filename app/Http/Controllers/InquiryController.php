<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Student;
use App\Models\Counselor;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::with(['student', 'counselor.user', 'respondedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inquiry_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $inquiries = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('consultancy.inquiries.index', compact('inquiries'));
    }

    public function create()
    {
        $counselors = Counselor::with('user')->where('is_active', true)->get();
        return view('consultancy.inquiries.create', compact('counselors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,admission,visa,language,scholarship,accommodation,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'source' => 'nullable|string|max:255',
            'counselor_id' => 'nullable|exists:counselors,id',
        ]);

        $inquiry = Inquiry::create($validated);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry created successfully! ID: ' . $inquiry->inquiry_id);
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['student', 'counselor.user', 'respondedBy']);
        return view('consultancy.inquiries.show', compact('inquiry'));
    }

    public function edit(Inquiry $inquiry)
    {
        $counselors = Counselor::with('user')->where('is_active', true)->get();
        return view('consultancy.inquiries.edit', compact('inquiry', 'counselors'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,responded,follow_up,converted,closed',
            'response' => 'nullable|string',
            'follow_up_date' => 'nullable|datetime',
            'follow_up_notes' => 'nullable|string',
            'counselor_id' => 'nullable|exists:counselors,id',
        ]);

        if ($request->filled('response') && empty($inquiry->responded_at)) {
            $validated['responded_at'] = now();
            $validated['responded_by'] = auth()->id();
        }

        $inquiry->update($validated);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry updated successfully!');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully!');
    }

    public function convertToStudent(Inquiry $inquiry)
    {
        // Create student from inquiry
        $student = Student::create([
            'first_name' => explode(' ', $inquiry->name)[0] ?? $inquiry->name,
            'last_name' => explode(' ', $inquiry->name)[1] ?? '',
            'email' => $inquiry->email,
            'phone' => $inquiry->phone ?? '',
            'address' => 'To be updated',
            'city' => 'To be updated',
            'source' => $inquiry->source,
            'status' => 'registered',
            'counselor_id' => $inquiry->counselor_id,
        ]);

        // Update inquiry
        $inquiry->update([
            'student_id' => $student->id,
            'status' => 'converted',
        ]);

        return redirect()->route('students.edit', $student)
            ->with('success', 'Inquiry converted to student! Please complete the student profile.');
    }
}
