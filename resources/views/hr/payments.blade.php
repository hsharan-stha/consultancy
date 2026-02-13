<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Payments') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (!$employee)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('No employee profile linked to your account. Payment history is only available for users with an employee record.') }}</p>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Paid amounts') }}</h3>
                    @if($payments->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Method</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Period</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($payments as $payment)
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $payment->payment_method ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $payment->period ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($payment->description ?? $payment->notes ?? '—', 40) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('No payments recorded yet.') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
