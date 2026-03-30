<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Performance Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <h6 class="text-gray-500 text-xs font-bold uppercase">Total Students</h6>
                    <h2 class="text-2xl font-bold">{{ \App\Models\Student::count() }}</h2>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
                    <h6 class="text-gray-500 text-xs font-bold uppercase">Average Attendance</h6>
                    <h2 class="text-2xl font-bold">{{ round(\App\Models\Student::avg('attendance_rate'), 1) }}%</h2>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                    <h6 class="text-gray-500 text-xs font-bold uppercase">Critical Cases</h6>
                    <h2 class="text-2xl font-bold text-red-600">{{ $stats['high'] }}</h2>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 flex items-stretch border border-gray-100">
                <div class="bg-red-600 p-6 flex items-center justify-center">
                    <i class="fas fa-robot text-white text-3xl"></i>
                </div>
                <div class="p-6">
                    <h4 class="text-red-600 font-bold text-lg">AI Early Warning System</h4>
                    <p class="text-gray-600 text-sm mb-4">The AI has identified <strong>{{ $stats['high'] }} students</strong> who require immediate intervention based on current performance trends.</p>
                    <a href="{{ route('students.index') }}" class="text-sm font-bold text-red-600 hover:underline">Review Priority List →</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>