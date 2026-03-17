<x-guest-layout>
    <style>
        body, .min-h-screen {
            background: linear-gradient(135deg, #f5f7fa 0%, #b5c6e1 100%) no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .w-full.sm\:max-w-md {
            background-color: white !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
            border: none !important;
            padding: 2rem;
        }

        .modern-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
            background-color: #f8fafc !important;
        }

        .btn-beyond {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px !important; 
            padding: 12px 25px !important;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
        }
    </style>
    
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-dark" style="letter-spacing: -1px;">Reset Password</h2>
        <p class="text-sm text-gray-600 mt-2">
            Enter your email and we will send you a reset link.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="fw-bold text-secondary small" />
            <x-text-input id="email" class="block mt-1 w-full modern-input" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-4">
            <button type="submit" class="btn btn-beyond fw-bold">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-sm text-primary text-decoration-none fw-bold">
                Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>