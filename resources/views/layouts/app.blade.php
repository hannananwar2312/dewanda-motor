<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dewanda Motor')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    @yield('content')

    <script>
        window.addEventListener('load', () => window.lucide && lucide.createIcons());
    </script>
    @stack('scripts')
</body>
</html>