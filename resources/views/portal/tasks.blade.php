<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tasks Assigned to You') }}
            </h2>
            <a href="{{ route('portal.dashboard') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($tasks->count() > 0)
            <div class="space-y-4">
                @foreach($tasks as $task)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700 {{ $task->due_date && $task->due_date->isPast() && $task->status == 'pending' ? 'border-l-4 border-l-red-500' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $task->title }}</h3>
                            @if($task->description)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $task->description }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ ucfirst(str_replace('_', ' ', $task->type ?? 'task')) }}</span>
                                @if($task->due_date)
                                <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                                @endif
                                @if($task->assignedTo)
                                <span>From: {{ $task->assignedTo->name }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 text-sm font-medium rounded-full shrink-0
                            @if($task->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($task->status == 'cancelled') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @elseif($task->priority == 'urgent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif($task->priority == 'high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @endif">
                            {{ $task->status == 'pending' ? 'Pending' : ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                    @if($task->notes)
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3">{{ $task->notes }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">No tasks assigned to you yet.</p>
                <a href="{{ route('portal.dashboard') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 dark:text-blue-400">Back to Dashboard</a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
