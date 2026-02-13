<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeePayment;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Employee::distinct()->pluck('department')->filter();

        return view('consultancy.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $roles = Role::whereIn('role', ['Employee', 'Teacher', 'HR', 'Editor'])->orderBy('role')->get();
        return view('consultancy.employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roleIds = Role::whereIn('role', ['Employee', 'Teacher', 'HR', 'Editor'])->pluck('id')->toArray();
        $validated = $request->validate([
            'employee_id' => 'required|string|max:255|unique:employees,employee_id',
            'role_id' => 'required|in:' . implode(',', $roleIds),
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $password = $request->filled('password') ? $request->input('password') : 'password';
        $user = User::create([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role_id' => $validated['role_id'],
            'email_verified_at' => now(),
        ]);
        $validated['user_id'] = $user->id;
        unset($validated['role_id']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = 'employee_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('employees'), $photoName);
            $validated['photo'] = 'employees/' . $photoName;
        }

        Employee::create($validated);

        $message = 'Employee created successfully!';
        if (!$request->filled('password')) {
            $message .= ' A login account was created with default password: password';
        }

        return redirect()->route('consultancy.employees.index')
            ->with('success', $message);
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user',
            'courses',
            'payments' => fn($q) => $q->with('paidBy')->orderBy('payment_date', 'desc')->limit(50),
            'attendances' => function($query) {
                $query->orderBy('date', 'desc')->limit(30);
            },
        ]);

        // Load tasks assigned to this employee's user
        $assignedTasks = [];
        $assignedCommunications = [];
        if ($employee->user_id) {
            $assignedTasks = \App\Models\Task::where('assigned_to', $employee->user_id)
                ->with(['student', 'assignedBy'])
                ->orderBy('due_date')
                ->get();
            $assignedCommunications = \App\Models\Communication::where('user_id', $employee->user_id)
                ->with('student')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $todayAttendance = $employee->attendances()->whereDate('date', today())->first();
        $thisMonthStats = $this->getMonthlyStats($employee);

        return view('consultancy.employees.show', compact('employee', 'todayAttendance', 'thisMonthStats', 'assignedTasks', 'assignedCommunications'));
    }

    public function storePayment(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'period' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['currency'] = $validated['currency'] ?? 'NPR';
        $validated['paid_by'] = auth()->id();

        EmployeePayment::create($validated);

        return redirect()->route('consultancy.employees.show', $employee)
            ->with('success', 'Payment recorded successfully.');
    }

    public function edit(Employee $employee)
    {
        $roles = Role::whereIn('role', ['Employee', 'Teacher', 'HR', 'Editor'])->orderBy('role')->get();
        return view('consultancy.employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $roleIds = Role::whereIn('role', ['Employee', 'Teacher', 'HR', 'Editor'])->pluck('id')->toArray();
        $validated = $request->validate([
            'employee_id' => 'required|string|max:255|unique:employees,employee_id,' . $employee->id,
            'role_id' => 'required|in:' . implode(',', $roleIds),
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($employee->photo && file_exists(public_path($employee->photo))) {
                unlink(public_path($employee->photo));
            }
            $photo = $request->file('photo');
            $photoName = 'employee_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('employees'), $photoName);
            $validated['photo'] = 'employees/' . $photoName;
        }

        if ($employee->user_id && isset($validated['role_id'])) {
            User::where('id', $employee->user_id)->update(['role_id' => $validated['role_id']]);
        }
        unset($validated['role_id']);
        $employee->update($validated);

        return redirect()->route('consultancy.employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        // Delete photo
        if ($employee->photo && file_exists(public_path($employee->photo))) {
            unlink(public_path($employee->photo));
        }

        $employee->delete();

        return redirect()->route('consultancy.employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    // Check-in/Check-out: one row per employee per day. Check-in creates/uses that row; check-out always updates the same row (never creates).
    public function checkIn(Request $request, Employee $employee)
    {
        $today = today();
        $attendance = EmployeeAttendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            ['status' => 'present']
        );

        if ($attendance->check_in) {
            return redirect()->back()->with('error', 'Already checked in today!');
        }

        $attendance->check_in = now();
        $attendance->check_in_location = $request->input('location', 'Office');
        $attendance->status = 'present';
        $attendance->save();

        return redirect()->back()->with('success', 'Checked in successfully!');
    }

    public function checkOut(Request $request, Employee $employee)
    {
        $today = today();
        // Always update the same row as morning check-in (never create a new row for checkout)
        $attendance = $employee->attendances()
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Please check in first!');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Already checked out today!');
        }

        $attendance->check_out = now();
        $attendance->check_out_location = $request->input('location', 'Office');

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($attendance->check_out);
        $hoursWorked = $checkIn->diffInHours($checkOut) + ($checkIn->diffInMinutes($checkOut) % 60) / 60;
        $attendance->hours_worked = round($hoursWorked, 2);

        $attendance->save();

        return redirect()->back()->with('success', 'Checked out successfully!');
    }

    public function attendance(Request $request, Employee $employee)
    {
        $query = $employee->attendances()->orderBy('date', 'desc');

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereYear('date', $month->year)
                  ->whereMonth('date', $month->month);
        } else {
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        $attendances = $query->paginate(31);
        $monthStats = $this->getMonthlyStats($employee, $request->filled('month') ? Carbon::parse($request->month) : now());

        return view('consultancy.employees.attendance', compact('employee', 'attendances', 'monthStats'));
    }

    /**
     * Update check-out time for an attendance record from the table (e.g. when today's attendance exists and checkout needs to be set or corrected).
     */
    public function updateAttendanceCheckout(Request $request, Employee $employee, EmployeeAttendance $attendance)
    {
        if ($attendance->employee_id !== $employee->id) {
            abort(403, 'Attendance does not belong to this employee.');
        }

        if (!$attendance->check_in) {
            return redirect()->back()->with('error', 'Cannot set check-out without check-in. Check-in first.');
        }

        $validated = $request->validate([
            'check_out' => 'required|date_format:H:i',
        ]);

        $date = $attendance->date->format('Y-m-d');
        $attendance->check_out = Carbon::parse($date . ' ' . $validated['check_out']);
        if ($attendance->check_out->lt(Carbon::parse($attendance->check_in))) {
            return redirect()->back()->with('error', 'Check-out time must be after check-in time.');
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = $attendance->check_out;
        $hoursWorked = $checkIn->diffInMinutes($checkOut) / 60;
        $attendance->hours_worked = round($hoursWorked, 2);
        $attendance->save();

        return redirect()->back()->with('success', 'Check-out updated successfully.');
    }

    private function getMonthlyStats(Employee $employee, $month = null)
    {
        $month = $month ?? now();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'total_hours' => round($attendances->sum('hours_worked'), 2),
        ];
    }
}
