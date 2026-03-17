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
            </div>
        </div>
    </div>
</x-app-layout>

@php
    // Fetch the count of high-risk students
    $atRiskCount = \App\Models\Student::where('risk_level', 'High')->count();
@endphp

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="d-flex align-items-stretch">
                    <div class="bg-danger d-flex align-items-center justify-content-center px-4">
                        <i class="fas fa-exclamation-triangle text-white fa-2x"></i>
                    </div>
                    <div class="p-4 flex-grow-1 bg-white">
                        <h6 class="text-uppercase fw-bold text-muted small mb-1">AI Early Warning System</h6>
                        <h3 class="fw-bold text-danger mb-2">
                            {{ $atRiskCount }} Students At Risk
                        </h3>
                        <p class="text-muted small mb-3">These students have a high probability of failure based on weighted academic scores.</p>
                        
                        <a href="{{ route('students.index', ['filter' => 'high_risk']) }}" 
                           class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold">
                            View Students <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>