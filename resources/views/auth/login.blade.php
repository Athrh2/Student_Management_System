<x-guest-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Apply your custom gradient and centering logic */
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

        .welcome-card { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 3rem; 
            width: 100%;
            max-width: 600px; /* Keeps the card from getting too wide */
        }

        /* Ensure the white card looks crisp against the gradient */
        .w-full.sm\:max-w-md {
            background-color: white !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
            border: none !important;
        }

        /* Modern Input Styling */
        .modern-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
            background-color: #f8fafc !important;
        }

        .modern-input:focus {
            background-color: #ffffff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1) !important;
        }

        /* Beyond-style Pill Button */
        .btn-beyond {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px !important; 
            padding: 12px 25px !important;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-beyond:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
    </style>

    <div class="mb-4 text-center">
        <h2 class="fw-bold text-dark" style="letter-spacing: -1.5px;">Welcome Back</h2>
        <p class="text-secondary small fw-medium">Student Management System 2026</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold text-secondary small" style="letter-spacing: 0.5px;">EMAIL ADDRESS</label>
            <x-text-input id="email" class="form-control modern-input" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between">
                <label class="form-label fw-bold text-secondary small" style="letter-spacing: 0.5px;">PASSWORD</label>
            </div>
            <x-text-input id="password" class="form-control modern-input" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            @error('email')
                <a href="{{ route('password.request') }}" class="text-danger small fw-bold text-decoration-none">
                    Forgot Password?
                </a>
            @enderror
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-beyond fw-bold py-2">
                Log In
            </button>
        </div>
        
        <div class="text-center">
            <p class="text-muted small mb-0">Don't have an account?</p>
            <a href="{{ route('register') }}" class="text-primary text-decoration-none small fw-bold">
                Create Account
            </a>
        </div>
    </form>
</x-guest-layout>