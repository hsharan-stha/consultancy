<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Attendance') }} - {{ $employee->full_name }}
            </h2>
            <a href="{{ route('consultancy.employees.show', $employee) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400">
                ← Back to Employee
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Monthly Stats -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Days</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $monthStats['total_days'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Present</p>
                        <p class="text-2xl font-bold text-green-600">{{ $monthStats['present_days'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Absent</p>
                        <p class="text-2xl font-bold text-red-600">{{ $monthStats['absent_days'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Late</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $monthStats['late_days'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Hours</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $monthStats['total_hours'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($attendances as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $attendance->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $attendance->hours_worked ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($attendance->status == 'present') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($attendance->status == 'absent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($attendance->status == 'late') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No attendance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
