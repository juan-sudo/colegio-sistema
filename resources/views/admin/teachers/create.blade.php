@extends('layouts.admin')
@section("title", "Nuevo profesor")
@section("content")
<h1 class="text-xl font-bold mb-4">Nuevo profesor</h1>
<form method="POST" action="{{ route("admin.teachers.store") }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="first_name" value="{{ old("first_name") }}" class="w-full border rounded p-2" required>
            @error("first_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Apellido</label>
            <input type="text" name="last_name" value="{{ old("last_name") }}" class="w-full border rounded p-2" required>
            @error("last_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old("email") }}" class="w-full border rounded p-2" required>
            @error("email")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
            @error("password")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Código</label>
            <input type="text" name="code" value="{{ old("code") }}" class="w-full border rounded p-2" required>
            @error("code")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Especialidad</label>
            <input type="text" name="specialty" value="{{ old("specialty") }}" class="w-full border rounded p-2">
            @error("specialty")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="phone" value="{{ old("phone") }}" class="w-full border rounded p-2">
            @error("phone")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
