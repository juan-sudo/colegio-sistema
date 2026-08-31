@extends('layouts.admin')
@section("title", "Nuevo usuario")
@section("content")
<h1 class="text-xl font-bold mb-4">Nuevo usuario</h1>
<form method="POST" action="{{ route("admin.users.store") }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old("name") }}" class="w-full border rounded p-2" required>
            @error("name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
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
            <label class="block text-sm font-medium mb-1">Rol</label>
            <select name="role" class="w-full border rounded p-2" required>
                @foreach($roles as $role)
                <option value="{{ $role }}" {{ old("role") == $role ? "selected" : "" }}>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error("role")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="phone" value="{{ old("phone") }}" class="w-full border rounded p-2">
            @error("phone")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="active" value="1" {{ old("active", true) ? "checked" : "" }}>
                <span class="text-sm">Activo</span>
            </label>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection