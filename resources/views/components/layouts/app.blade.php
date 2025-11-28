<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laravel App' }}</title>
    @vite('resources/css/app.css')



    {{-- Livewire Styles --}}
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100">

    {{-- محتوى الصفحة --}}
    {{ $slot ?? '' }}

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
