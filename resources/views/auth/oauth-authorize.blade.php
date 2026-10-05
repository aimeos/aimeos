<x-guest-layout>
    @php
        // Passport already validated the redirect URI against the ones registered by the client
        $redirect = $request->query('redirect_uri') ?: ($client->redirect_uris[0] ?? '');
        $host = parse_url($redirect, PHP_URL_HOST) ?: $redirect;
    @endphp

    <div class="mb-4 text-sm text-gray-600">
        {{ __(':client wants to access and manage your shop using your account :email.', ['client' => $client->name, 'email' => $user->email]) }}
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Access will be granted to:') }}
        <strong class="block mt-1 text-base text-gray-900 break-all">{{ $host }}</strong>
    </div>

    <div class="mb-4 p-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded">
        {{ __('Only authorize if you have just connected an AI app yourself and you recognize this address. The app will be able to read and change all data of your shop.') }}
    </div>

    @if (count($scopes) > 0)
        <ul class="mb-4 text-sm text-gray-600 list-disc list-inside">
            @foreach ($scopes as $scope)
                <li>{{ $scope->description }}</li>
            @endforeach
        </ul>
    @endif

    <div class="flex items-center justify-end mt-4">
        <form method="POST" action="{{ route('passport.authorizations.deny') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <x-secondary-button type="submit">
                {{ __('Cancel') }}
            </x-secondary-button>
        </form>

        <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="ml-3">
            @csrf
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <x-primary-button>
                {{ __('Authorize') }}
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
