@extends('layouts.admin')
@section("title", "Editar profesor")
@section("content")
<h1 class="text-xl font-bold mb-4">Editar profesor</h1>
<form method="POST" action="{{ route("admin.teachers.update", $teacher) }}">
    @csrf @method("PUT")
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="first_name" value="{{ old("first_name", $teacher->first_name) }}" class="w-full border rounded p-2" required>
            @error("first_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Apellido</label>
            <input type="text" name="last_name" value="{{ old("last_name", $teacher->last_name) }}" class="w-full border rounded p-2" required>
            @error("last_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old("email", $teacher->user->email) }}" class="w-full border rounded p-2" required>
            @error("email")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Código</label>
            <input type="text" name="code" value="{{ old("code", $teacher->code) }}" class="w-full border rounded p-2" required>
            @error("code")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Especialidad</label>
            <input type="text" name="specialty" value="{{ old("specialty", $teacher->specialty) }}" class="w-full border rounded p-2">
            @error("specialty")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="phone" value="{{ old("phone", $teacher->user->phone) }}" class="w-full border rounded p-2">
            @error("phone")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="active" value="1" {{ old("active", $teacher->user->active) ? "checked" : "" }}>
                <span class="text-sm">Activo</span>
            </label>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
