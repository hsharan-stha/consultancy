<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('counselor.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $counselor->user->email) }}" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $counselor->phone) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Extension</label>
                            <input type="text" name="extension" value="{{ old('extension', $counselor->extension) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialization</label>
                            <select name="specialization" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">General</option>
                                <option value="Language School" {{ $counselor->specialization == 'Language School' ? 'selected' : '' }}>Language School</option>
                                <option value="University" {{ $counselor->specialization == 'University' ? 'selected' : '' }}>University</option>
                                <option value="Vocational" {{ $counselor->specialization == 'Vocational' ? 'selected' : '' }}>Vocational School</option>
                                <option value="Visa" {{ $counselor->specialization == 'Visa' ? 'selected' : '' }}>Visa Processing</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="{{ route('counselor.dashboard') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Update Profile</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Change Password') }}</h3>
                <form action="{{ route('counselor.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Current Password') }} *</label>
                            <input type="password" name="current_password" required autocomplete="current-password"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('New Password') }} *</label>
                            <input type="password" name="password" required minlength="8" autocomplete="new-password"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">{{ __('At least 8 characters.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Confirm New Password') }} *</label>
                            <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">{{ __('Change Password') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
