<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Sign In') }} - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 min-h-screen overflow-x-hidden">
        <div class="min-h-screen w-full flex flex-col lg:flex-row overflow-hidden bg-white relative">
            
            <!-- Left Column (Vibrant Blue Pane) -->
            <div class="hidden lg:flex lg:w-1/2 bg-[#0c2b4d] relative flex-col justify-between p-16 select-none overflow-hidden">
                <!-- Background Accent Graphic: Large Concentric Rings in Bottom-Left -->
                <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full border-[28px] border-blue-500/10 pointer-events-none"></div>
                <div class="absolute -bottom-40 -left-40 w-112 h-112 rounded-full border-[28px] border-blue-400/5 pointer-events-none"></div>
                
                <!-- Top spacing/empty container -->
                <div></div>
                
                <!-- Center Content: Logo + Text -->
                <div class="flex items-center gap-8 my-auto z-10 max-w-xl">
                    <!-- School Logo -->
                    <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" alt="SMKN 1 Jakarta Logo" class="w-36 h-36 flex-shrink-0 object-contain">
                    
                    <!-- Text Content -->
                    <div class="flex-1">
                        <h1 class="text-white text-3xl font-extrabold tracking-wide drop-shadow-sm">
                            Welcome to S.T.O.V.I.A
                        </h1>
                        <p class="text-blue-100/70 text-sm mt-4 leading-relaxed font-light text-justify">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                    </div>
                </div>
                

            </div>
            
            <!-- Right Column (Sign In Form Pane) -->
            <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 md:p-16 relative bg-white min-h-screen lg:min-h-0 overflow-hidden">
                
                <!-- Top-Right Background Accent Graphic: Thin Grey Arc -->
                <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full border-[32px] border-gray-100 pointer-events-none"></div>
                

                
                <!-- Center Login Form -->
                <div class="w-full max-w-sm mx-auto my-auto z-10">
                    <h2 class="text-[#0c2b4d] text-4xl font-extrabold text-center mb-10 tracking-tight">
                        Sign In
                    </h2>
                    
                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 text-center">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Email"
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] placeholder-gray-400 text-gray-800 transition-all shadow-sm @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   placeholder="Password"
                                   required 
                                   autocomplete="current-password"
                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] placeholder-gray-400 text-gray-800 transition-all shadow-sm @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Sign In Button -->
                        <button type="submit" class="w-full bg-[#0c2b4d] hover:bg-[#07192d] active:bg-[#040f1c] text-white font-semibold py-3 px-4 rounded-lg mt-6 shadow-md hover:shadow-lg transition-all duration-150 text-center select-none cursor-pointer">
                            Sign In
                        </button>
                    </form>
                </div>
                
                <!-- Bottom spacer for alignment -->
                <div></div>
            </div>
        </div>
    </body>
</html>
