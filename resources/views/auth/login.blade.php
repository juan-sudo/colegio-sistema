@extends("layouts.app")
@section("title", "Iniciar sesión")
@section("content")
<div class="max-w-md mx-auto mt-16 bg-white shadow rounded-lg p-8">
    <h1 class="text-2xl font-bold text-center mb-6 text-indigo-700">Sistema Escolar</h1>
    <form method="POST" action="{{ route("login") }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Correo electrónico</label>
            <input type="email" name="email" required autofocus
                   class="w-full border rounded px-3 py-2" value="{{ old("email") }}">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember"> Recordarme
        </label>
        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
            Ingresar
        </button>
    </form>
</div>
@endsection
