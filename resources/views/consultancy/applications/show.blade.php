<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Application: {{ $application->application_id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('consultancy.applications.edit', $application) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
                <a href="{{ url()->previous() }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Application Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Student</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('consultancy.students.show', $application->student) }}" class="text-blue-600 hover:underline">
                                        {{ $application->student->full_name ?? 'N/A' }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">University</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->university->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Intake</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->intake }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Course</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->course_name ?? 'N/A' }} ({{ $application->course_type ?? 'N/A' }})</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Application Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->application_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Submission Deadline</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->submission_deadline?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Counselor</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->counselor->user->name ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Interview Details -->
                    @if($application->interview_date)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Interview Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Date & Time</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->interview_date->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mode</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->interview_mode ?? 'N/A' }}</p>
                            </div>
                            @if($application->interview_notes)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Notes</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->interview_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- COE Status -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">COE (Certificate of Eligibility)</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-sm rounded-full 
                                    @if($application->coe_status == 'approved') bg-green-100 text-green-800
                                    @elseif($application->coe_status == 'rejected') bg-red-100 text-red-800
                                    @elseif($application->coe_status == 'processing') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $application->coe_status)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Applied Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->coe_applied_date?->format('M d, Y') ?? 'Not applied' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Received Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->coe_received_date?->format('M d, Y') ?? 'Not received' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Communication Log -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Communication Log</h3>
                            <a href="{{ route('consultancy.communications.create', ['student_id' => $application->student_id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Log Communication</a>
                        </div>
                        @if($application->student->communications->count())
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($application->student->communications->take(10) as $comm)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $comm->subject ?? ucfirst($comm->type) }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($comm->content, 100) }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $comm->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">By: {{ $comm->user->name ?? 'System' }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No communications logged</p>
                        @endif
                    </div>

                    <!-- Payments -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payments</h3>
                            <a href="{{ route('consultancy.payments.create', ['student_id' => $application->student_id, 'application_id' => $application->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Record Payment</a>
                        </div>
                        @if($application->student->payments->count())
                            <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase">
                                        <th class="pb-2">Type</th>
                                        <th class="pb-2">Amount</th>
                                        <th class="pb-2">Paid</th>
                                        <th class="pb-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($application->student->payments as $payment)
                                    <tr>
                                        <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $payment->payment_type }}</td>
                                        <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->paid_amount, 2) }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                                @elseif($payment->status == 'partial') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No payments recorded</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $application->application_id }}</h3>
                        <span class="mt-2 px-3 py-1 inline-flex text-sm rounded-full 
                            @if($application->status == 'accepted' || $application->status == 'enrolled') bg-green-100 text-green-800
                            @elseif($application->status == 'rejected') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </div>

                    <!-- Documents -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                            <a href="{{ route('consultancy.documents.create', ['student_id' => $application->student_id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Upload</a>
                        </div>

                        @if(isset($requiredDocumentsStatus) && $requiredDocumentsStatus->isNotEmpty())
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <h4 class="text-xs font-medium text-gray-900 dark:text-white mb-2">Required ({{ $application->university->country ?? 'N/A' }})</h4>
                            <div class="space-y-2">
                                @foreach($requiredDocumentsStatus as $row)
                                <div class="flex justify-between items-start text-xs">
                                    <span class="text-gray-900 dark:text-white">{{ Str::limit($row->item->name, 20) }}</span>
                                    <span class="px-1.5 py-0.5 rounded-full 
                                        @if($row->submitted) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ $row->submitted ? 'OK' : '✕' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @php
                            $remainingDocs = $requiredDocumentsStatus->filter(fn($row) => !$row->submitted);
                        @endphp
                        @if($remainingDocs->count() > 0)
                        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                            <h4 class="text-xs font-medium text-red-800 dark:text-red-200 mb-2">❗ Remaining</h4>
                            <div class="space-y-1">
                                @foreach($remainingDocs as $row)
                                <p class="text-xs text-red-700 dark:text-red-300">• {{ $row->item->name }}</p>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endif

                        @if($application->student->documents->count())
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($application->student->documents->take(8) as $doc)
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs">
                                    <div class="flex justify-between items-start gap-2 mb-1">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $doc->title ?? $doc->document_type }}</p>
                                            <p class="text-gray-500 dark:text-gray-400">{{ $doc->document_type }}</p>
                                        </div>
                                        <span class="px-1.5 py-0.5 rounded-full whitespace-nowrap 
                                            @if($doc->status == 'verified') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($doc->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    </div>
                                    <div class="flex gap-1">
                                        <a href="{{ route('consultancy.documents.show', $doc) }}" class="flex-1 text-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition">View</a>
                                        @if($doc->file_path)
                                            <a href="{{ asset($doc->file_path) }}" download class="flex-1 text-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded hover:bg-green-200 dark:hover:bg-green-800 transition">Download</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-3 text-sm">No documents</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Fees</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Application Fee</span>
                                <span class="font-medium text-gray-900 dark:text-white">¥{{ number_format($application->application_fee ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tuition Fee</span>
                                <span class="font-medium text-gray-900 dark:text-white">¥{{ number_format($application->tuition_fee ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t">
                                <span class="text-gray-500">App Fee Paid</span>
                                @if($application->application_fee_paid)
                                    <span class="text-green-600">Yes</span>
                                @else
                                    <span class="text-red-600">No</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Visa Application -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Visa</h3>
                        </div>
                        @if($application->visaApplication)
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-sm rounded-full 
                                    @if($application->visaApplication->status == 'approved') bg-green-100 text-green-800
                                    @elseif($application->visaApplication->status == 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $application->visaApplication->status)) }}
                                </span>
                                <a href="{{ route('consultancy.visa.show', $application->visaApplication) }}" class="text-sm text-blue-600 hover:text-blue-800 block mt-2">View Details →</a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No visa application</p>
                            @if($application->status == 'accepted')
                                <a href="{{ route('consultancy.visa.create', ['student_id' => $application->student_id, 'application_id' => $application->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Create Visa Application</a>
                            @endif
                        @endif
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tasks</h3>
                            <a href="{{ route('consultancy.tasks.create', ['student_id' => $application->student_id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Add</a>
                        </div>
                        @if($application->tasks->where('status', 'pending')->count())
                            <div class="space-y-2">
                                @foreach($application->tasks->where('status', 'pending')->take(5) as $task)
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                    <p class="text-xs text-gray-500">Due: {{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4 text-sm">No pending tasks</p>
                        @endif
                    </div>

                    @if($application->notes)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $application->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
