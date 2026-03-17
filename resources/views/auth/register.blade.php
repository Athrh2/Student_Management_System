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
    
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
