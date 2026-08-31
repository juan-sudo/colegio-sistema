<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "Sistema Escolar")</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    @auth
    <nav class="bg-indigo-700 text-white px-6 py-3 flex justify-between items-center">
        <span class="font-bold text-lg">🏫 Sistema Escolar</span>
        <div class="flex items-center gap-4 text-sm">
            <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
            <form method="POST" action="{{ route("logout") }}">
                @csrf
                <button class="bg-indigo-900 px-3 py-1 rounded hover:bg-indigo-800">Salir</button>
            </form>
        </div>
    </nav>
    @endauth

    <main class="p-6 max-w-6xl mx-auto">
        @if(session("success"))
            <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3 mb-4">
                {{ session("success") }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3 mb-4">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield("content")
    </main>
</body>
</html>
