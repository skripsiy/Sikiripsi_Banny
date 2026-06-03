<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Akun') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto font-sans py-8 space-y-8">
        <!-- Update Profile Information Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete User Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-red-600 to-rose-500"></div>
            <div class="p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
