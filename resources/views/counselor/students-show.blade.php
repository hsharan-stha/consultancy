<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $student->full_name }} ({{ $student->student_id }})
            </h2>
            <a href="{{ route('counselor.students') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 text-sm">← Back to Students</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-gray-500 dark:text-gray-400">Full Name</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->full_name }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Email</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->email }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Phone</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->phone ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Alternate / WhatsApp</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->alternate_phone ?? $student->whatsapp ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Gender</p><p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($student->gender ?? '—') }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Date of Birth</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Nationality</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->nationality ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Status</p><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ ucfirst(str_replace('_', ' ', $student->status ?? 'N/A')) }}</span></div>
                        </div>
                    </div>

                    <!-- Address & Contact -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address & Contact</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="md:col-span-2"><p class="text-gray-500 dark:text-gray-400">Address</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->address ?? '—' }}{{ $student->city ? ', ' . $student->city : '' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">City</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->city ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Country (residence)</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->country ?? '—' }}</p></div>
                            <div class="md:col-span-2"><p class="text-gray-500 dark:text-gray-400">Emergency Contact</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->emergency_contact_name ?? '—' }}{{ $student->emergency_contact_relation ? ' (' . $student->emergency_contact_relation . ')' : '' }} — {{ $student->emergency_contact_phone ?? '' }}</p></div>
                        </div>
                    </div>

                    <!-- Target & Education -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Target & Education</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-gray-500 dark:text-gray-400">Target Country</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->target_country ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Target Intake</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->target_intake ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Target Course Type</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->target_course_type ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Highest Education</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->highest_education ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Last Institution</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->last_institution ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Graduation Year / GPA</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->graduation_year ?? '—' }} {{ $student->gpa ? ' / ' . $student->gpa : '' }}</p></div>
                        </div>
                        @if($student->jlpt_level || $student->nat_level || $student->ielts_score || $student->toefl_score)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 dark:text-gray-400 mb-2">Language / Test</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $student->jlpt_level ? 'JLPT: ' . $student->jlpt_level . ' ' : '' }}{{ $student->nat_level ? 'NAT: ' . $student->nat_level . ' ' : '' }}{{ $student->ielts_score ? 'IELTS: ' . $student->ielts_score . ' ' : '' }}{{ $student->toefl_score ? 'TOEFL: ' . $student->toefl_score : '' }}{{ !$student->jlpt_level && !$student->nat_level && !$student->ielts_score && !$student->toefl_score ? '—' : '' }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Applications -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Applications ({{ $student->applications->count() }})</h3>
                        @if($student->applications->count())
                        <ul class="space-y-2">
                            @foreach($student->applications as $app)
                            <li class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span class="text-gray-900 dark:text-white">{{ $app->university->name ?? 'N/A' }} — {{ $app->intake ?? $app->application_id }}</span>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200">{{ ucfirst(str_replace('_', ' ', $app->status)) }}</span>
                                <a href="{{ route('counselor.applications.show', $app) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">View</a>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">No applications yet.</p>
                        @endif
                    </div>

                    <!-- Documents: Required (target country) + Uploaded -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Documents</h3>
                        @if(isset($requiredDocumentsStatus) && $requiredDocumentsStatus->isNotEmpty())
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <h4 class="text-xs font-medium text-gray-900 dark:text-white mb-2">Required ({{ $student->target_country ?? 'N/A' }})</h4>
                            <div class="space-y-2">
                                @foreach($requiredDocumentsStatus as $row)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-900 dark:text-white">{{ $row->item->name }}</span>
                                    @if($row->submitted && $row->document)
                                    <span class="px-1.5 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs">OK</span>
                                    <a href="{{ route('counselor.documents.show', $row->document) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs">View</a>
                                    @else
                                    <span class="px-1.5 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs">Not submitted</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Uploaded documents</h4>
                        @if($student->documents->count())
                        <div class="space-y-2">
                            @foreach($student->documents as $doc)
                            <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $doc->title }}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $doc->document_type }}</p>
                                </div>
                                <span class="px-1.5 py-0.5 rounded-full text-xs whitespace-nowrap
                                    @if($doc->status == 'verified') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($doc->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ ucfirst($doc->status) }}
                                </span>
                                <a href="{{ route('counselor.documents.show', $doc) }}" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs">View</a>
                                @if($doc->file_path)
                                <a href="{{ asset($doc->file_path) }}" download class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400 text-xs">Download</a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No documents uploaded.</p>
                        @endif
                    </div>

                    <!-- Payments -->
                    @if($student->payments->count())
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payments</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead><tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-600"><th class="pb-2">Type</th><th class="pb-2">Amount</th><th class="pb-2">Paid</th><th class="pb-2">Status</th></tr></thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($student->payments as $payment)
                                    <tr>
                                        <td class="py-2 text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_type ?? '—')) }}</td>
                                        <td class="py-2 text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-2 text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->paid_amount ?? 0, 2) }}</td>
                                        <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full @if($payment->status == 'completed') bg-green-100 text-green-800 @elseif($payment->status == 'partial') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">{{ ucfirst($payment->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Communications -->
                    @if($student->communications->count())
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Communications</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($student->communications->take(10) as $comm)
                            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $comm->subject ?? ucfirst($comm->type ?? 'Message') }}</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ Str::limit($comm->content, 80) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $comm->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tasks -->
                    @if($student->tasks->count())
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tasks</h3>
                        <ul class="space-y-2">
                            @foreach($student->tasks->take(10) as $task)
                            <li class="flex justify-between items-start p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                <span class="text-gray-900 dark:text-white">{{ $task->title }}</span>
                                <span class="px-2 py-0.5 text-xs rounded-full @if($task->status == 'completed') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">{{ ucfirst($task->status) }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    @if($student->photo)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                        <img src="{{ asset($student->photo) }}" alt="{{ $student->full_name }}" class="w-32 h-32 rounded-full mx-auto object-cover">
                        <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->student_id }}</p>
                    </div>
                    @endif
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-2 text-sm">
                            <div><p class="text-gray-500 dark:text-gray-400">Target Country</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->target_country ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Target Intake</p><p class="font-medium text-gray-900 dark:text-white">{{ $student->target_intake ?? '—' }}</p></div>
                            <div><p class="text-gray-500 dark:text-gray-400">Status</p><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ ucfirst(str_replace('_', ' ', $student->status ?? 'N/A')) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
