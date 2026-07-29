<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in - {{ config('app.name', 'Laravel') }}</title>
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

        <!-- Right Panel (Login Form) -->
        <div class="lg:col-span-2 lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">

                <div class="mb-6">
                    <h2 class="text-lg font-medium">Expense and Income Tracker</h2>
                    <p class="text-sm text-gray-500">Please sign in</p>
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

                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                    @csrf

                    <div class="grid gap-6">
                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="email" class="text-xs tracking-wider text-gray-500 uppercase">Username</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                autocomplete="email"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <div class="grid gap-2 border-b border-gray-200 pb-2">
                            <label for="password"
                                class="text-xs tracking-wider text-gray-500 uppercase">Password</label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="h-8 border-0 px-0 shadow-none focus:ring-0 w-full outline-none">
                        </div>

                        <div class="mt-2 flex items-center justify-between">
                            <label for="remember"
                                class="flex items-center space-x-3 text-sm font-normal text-gray-600 cursor-pointer">
                                <input id="remember" type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                                <span>Keep me logged in</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="mt-2 flex w-32 items-center justify-center gap-2 rounded-md bg-[#5cb85c] py-2 text-white hover:bg-[#4cae4c] transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-log-in">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <polyline points="10 17 15 12 10 7" />
                                <line x1="15" x2="3" y1="12" y2="12" />
                            </svg>
                            Sign In
                        </button>

                        <div class="mt-4 flex flex-col gap-2">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="flex items-center text-sm text-red-500 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-triangle-alert mr-1">
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                                        <path d="M12 9v4" />
                                        <path d="M12 17h.01" />
                                    </svg>
                                    Forgot Password
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <div class="text-sm text-gray-500 mt-2">
                                    Don't have an account? <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-medium">Register here</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>