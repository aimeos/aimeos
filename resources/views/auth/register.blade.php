<x-guest-layout>
    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ airoute('register') }}">
        @csrf

        @if(config('app.shop_multishop') && config('app.shop_registration'))
            <!-- Code -->
            <div class="mt-4">
                <x-label for="code" :value="__('Account')" />
                <x-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" autocomplete="username" required autofocus />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @else
            <!-- Name -->
            <div class="mt-4">
                <x-label for="name" :value="__('Name')" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autocomplete="name" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
        @endif

        <!-- Email Address -->
        <div class="mt-4">
            <x-label for="email" :value="__('Email')" />
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-label for="password" :value="__('Password')" />
            <x-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                autocomplete="new-password"
                required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ airoute('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
