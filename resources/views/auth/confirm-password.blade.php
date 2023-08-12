<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ airoute('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-label for="password" :value="__('Password')" />
            <x-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                autocomplete="current-password"
                required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
