<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Document Details') }}
            </h2>
            <a href="{{ route('counselor.students.show', $document->student) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400">
                ← Back to Student
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Student</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $document->student->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->student->student_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Document Type</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->document_type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Title</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <span class="mt-1 inline-block px-3 py-1 text-xs font-semibold rounded-full
                                @if($document->status == 'verified') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($document->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                {{ ucfirst($document->status) }}
                            </span>
                        </div>
                        @if($document->expiry_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Expiry Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->expiry_date->format('M d, Y') }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Uploaded</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($document->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->notes }}</p>
                    </div>
                    @endif

                    @if($document->rejection_reason)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <label class="block text-sm font-medium text-red-800 dark:text-red-200">Rejection Reason</label>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $document->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($document->verifiedBy)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Verified By</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->verifiedBy->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->verified_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Document File</label>
                        @if($document->file_path && in_array(strtolower($document->file_type ?? ''), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset($document->file_path) }}" alt="{{ $document->title }}" class="max-w-full h-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        @else
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $document->file_name ?? 'Document' }}</p>
                                @if($document->file_path)
                                <a href="{{ asset($document->file_path) }}" target="_blank" download class="mt-2 inline-block text-blue-600 hover:text-blue-800 dark:text-blue-400">View/Download</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
