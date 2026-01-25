<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $student->full_name }} - {{ $student->student_id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('consultancy.students.edit', $student) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit</a>
                <a href="{{ route('consultancy.students.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Back</a>
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
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Full Name</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->phone }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Gender</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date of Birth</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->address }}, {{ $student->city }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Applications -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Applications</h3>
                            <a href="{{ route('consultancy.applications.create', ['student_id' => $student->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ New Application</a>
                        </div>
                        @if($student->applications->count())
                            <div class="space-y-3">
                                @foreach($student->applications as $app)
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $app->university->name ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-500">{{ $app->application_id }} - {{ $app->intake }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $app->status)) }}</span>
                                    </div>
                                    <a href="{{ route('consultancy.applications.show', $app) }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">View Details →</a>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No applications yet</p>
                        @endif
                    </div>

                    <!-- Documents -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                            <a href="{{ route('consultancy.documents.create', ['student_id' => $student->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Upload Document</a>
                        </div>
                        @if($student->documents->count())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($student->documents as $doc)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $doc->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $doc->document_type }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($doc->status == 'verified') bg-green-100 text-green-800
                                        @elseif($doc->status == 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No documents uploaded</p>
                        @endif
                    </div>

                    <!-- Payments -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payments</h3>
                            <a href="{{ route('consultancy.payments.create', ['student_id' => $student->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Record Payment</a>
                        </div>
                        @if($student->payments->count())
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
                                    @foreach($student->payments as $payment)
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
                        @else
                            <p class="text-gray-500 text-center py-4">No payments recorded</p>
                        @endif
                    </div>

                    <!-- Communication Log -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Communication Log</h3>
                            <a href="{{ route('consultancy.communications.create', ['student_id' => $student->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Log Communication</a>
                        </div>
                        @if($student->communications->count())
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($student->communications->take(10) as $comm)
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
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Photo & Status -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                        @if($student->photo)
                            <img src="{{ asset($student->photo) }}" alt="{{ $student->full_name }}" class="w-32 h-32 rounded-full mx-auto object-cover mb-4">
                        @else
                            <div class="w-32 h-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center mb-4">
                                <span class="text-4xl text-gray-500">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</h3>
                        <p class="text-gray-500">{{ $student->student_id }}</p>
                        <span class="mt-2 px-3 py-1 inline-flex text-sm rounded-full 
                            @if($student->status == 'visa_approved') bg-green-100 text-green-800
                            @elseif($student->status == 'visa_rejected') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $student->status)) }}
                        </span>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Target Intake</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->target_intake ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Target University</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->targetUniversity->name ?? 'Not selected' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">JLPT Level</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->jlpt_level ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Counselor</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->counselor->user->name ?? 'Unassigned' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Registered</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tasks</h3>
                            <a href="{{ route('consultancy.tasks.create', ['student_id' => $student->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">+ Add</a>
                        </div>
                        @if($student->tasks->where('status', 'pending')->count())
                            <div class="space-y-2">
                                @foreach($student->tasks->where('status', 'pending')->take(5) as $task)
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
