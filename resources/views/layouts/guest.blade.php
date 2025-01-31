<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-pink-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Form Container -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-pink-50 shadow-lg rounded-3xl overflow-hidden sm:rounded-lg border-2 border-pink-200">
                <div class="form-title text-center mb-8">
                    <h2 class="text-3xl font-bold text-pink-600">
                        @if(Route::currentRouteName() == 'login')
                            {{ __('Login') }}
                        @elseif(Route::currentRouteName() == 'register')
                            {{ __('Register') }}
                        @endif
                    </h2>
                    <p class="text-sm text-gray-600">
                        {{ Route::currentRouteName() == 'login' ? 'Welcome Back!' : 'Create a new account' }}
                    </p>
                </div>

                <!-- Form content placeholder -->
                {{ $slot }} <!-- Tempat untuk konten form login atau register -->
            </div>
        </div>
    </body>
</html>
