<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
                
                <div class="flex items-center space-x-4">
                    <img src="{{ Auth::user()->profile_picture_url }}" alt="Profile Picture" class="h-16 w-16 rounded-full">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800">{{ Auth::user()->name }}</h2>
                        <p class="text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
