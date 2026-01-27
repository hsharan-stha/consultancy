<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeePortalController extends Controller
{
    private function getAuthenticatedEmployee()
    {
        $user = Auth::user();
        return Employee::where('user_id', $user->id)->first();
    }

    public function dashboard()
    {
        $employee = $this->getAuthenticatedEmployee();
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'No employee profile found.');
        }

        // Get statistics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $stats = [
            'total_attendance_days' => $employee->attendances()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'present')
                ->count(),
            'total_late_days' => $employee->attendances()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'late')
                ->count(),
            'total_absent_days' => $employee->attendances()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'absent')
                ->count(),
            'total_hours_worked' => $employee->attendances()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'present')
                ->sum('hours_worked') ?? 0,
        ];

        // Get recent attendance
        $recentAttendance = $employee->attendances()
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Get this month's attendance
        $monthlyAttendance = $employee->attendances()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'asc')
            ->get();

        return view('employee.dashboard', compact('employee', 'stats', 'recentAttendance', 'monthlyAttendance'));
    }

    public function attendance()
    {
        $employee = $this->getAuthenticatedEmployee();
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'No employee profile found.');
        }

        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $attendances = $employee->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'total_hours' => $attendances->where('status', 'present')->sum('hours_worked') ?? 0,
        ];

        return view('employee.attendance', compact('employee', 'attendances', 'stats', 'month', 'year'));
    }

    public function profile()
    {
        $employee = $this->getAuthenticatedEmployee();
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'No employee profile found.');
        }

        return view('employee.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'No employee profile found.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        // Update user email if provided
        if ($request->filled('email')) {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $employee->user_id,
            ]);
            $employee->user->update(['email' => $request->email]);
        }

        return redirect()->route('employee.profile')
            ->with('success', 'Profile updated successfully!');
    }
}
