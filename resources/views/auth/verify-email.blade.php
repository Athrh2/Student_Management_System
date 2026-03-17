<x-guest-layout>
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
    
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
