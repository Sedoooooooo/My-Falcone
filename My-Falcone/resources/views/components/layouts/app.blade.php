<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finding Falcone</title>
    @livewireStyles
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    @yield('content')
    
    <div class="flex justify-center items-center min-h-screen">
        <div class="container mx-auto p-4">
            {{ $slot }}
        </div>
    </div>
    
    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>
