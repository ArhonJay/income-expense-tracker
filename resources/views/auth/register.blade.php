<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="font-sans antialiased text-gray-900 bg-white">
    <div
        class="relative grid h-screen flex-col items-center justify-center bg-white px-8 sm:px-0 lg:max-w-none lg:grid-cols-5 lg:px-0">
        <!-- Left Panel -->
        <div class="relative hidden h-full flex-col bg-cover bg-center p-10 lg:col-span-3 lg:flex border-r border-gray-200"
            style="background-image: url('/images/library_background.png')">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <!-- Right Panel (Register Form) -->
        <div class="lg:col-span-2 lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">

                <div class="mb-6">
                    <h2 class="text-lg font-medium">Create an account</h2>
                    <p class="text-sm text-gray-500">Enter your details below to create your account</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 text-center text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6">
                    @csrf

                    <div class="grid gap-6">
                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="name" class="text-xs tracking-wider text-gray-500 uppercase">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                autocomplete="name"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="email" class="text-xs tracking-wider text-gray-500 uppercase">Email
                                address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="password"
                                class="text-xs tracking-wider text-gray-500 uppercase">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="password_confirmation"
                                class="text-xs tracking-wider text-gray-500 uppercase">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <button type="submit"
                            class="mt-2 flex w-full items-center justify-center gap-2 rounded-md bg-[#5cb85c] py-2 text-white hover:bg-[#4cae4c] transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Create account
                        </button>

                        <div class="mt-4 text-center text-sm text-gray-500">
                            Already have an account? <a href="{{ route('login') }}"
                                class="text-green-600 hover:text-green-700 font-medium">Log in</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>