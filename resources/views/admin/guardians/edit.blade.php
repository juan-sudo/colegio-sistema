@extends('layouts.admin')
@section("title", "Editar apoderado")
@section("content")
<h1 class="text-xl font-bold mb-4">Editar apoderado</h1>
<form method="POST" action="{{ route("admin.guardians.update", $guardian) }}">
    @csrf @method("PUT")
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="first_name" value="{{ old("first_name", $guardian->first_name) }}" class="w-full border rounded p-2" required>
            @error("first_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Apellido</label>
            <input type="text" name="last_name" value="{{ old("last_name", $guardian->last_name) }}" class="w-full border rounded p-2" required>
            @error("last_name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old("email", $guardian->user->email) }}" class="w-full border rounded p-2" required>
            @error("email")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">WhatsApp</label>
            <input type="text" name="phone_whatsapp" value="{{ old("phone_whatsapp", $guardian->phone_whatsapp) }}" class="w-full border rounded p-2" required>
            @error("phone_whatsapp")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="phone" value="{{ old("phone", $guardian->user->phone) }}" class="w-full border rounded p-2">
            @error("phone")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
