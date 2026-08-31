@extends('layouts.admin')
@section('title', 'Configuraciones')
@section('content')
<h1 class="text-xl font-bold mb-4">Configuraciones</h1>
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <div class="flex gap-4 mb-4">
        @foreach($groups as $group)
        <a href="{{ route('admin.settings.index', ['group' => $group]) }}"
           class="px-3 py-1 rounded text-sm {{ $selectedGroup == $group ? 'bg-indigo-600 text-white' : 'bg-gray-200' }}">
            {{ ucfirst($group) }}
        </a>
        @endforeach
    </div>
    <div class="bg-white rounded shadow p-4 max-w-2xl">
        @foreach($settings as $setting)
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
            @if($setting->key === 'attendance_method')
                <select name="{{ $setting->key }}" class="w-full border rounded p-2">
                    <option value="qr" {{ $setting->value === 'qr' ? 'selected' : '' }}>QR</option>
                    <option value="barcode" {{ $setting->value === 'barcode' ? 'selected' : '' }}>Código de barras</option>
                    <option value="biometric" {{ $setting->value === 'biometric' ? 'selected' : '' }}>Biométrico</option>
                    <option value="both" {{ $setting->value === 'both' ? 'selected' : '' }}>Ambos</option>
                </select>
            @elseif($setting->type === 'boolean')
                <select name="{{ $setting->key }}" class="w-full border rounded p-2">
                    <option value="1" {{ $setting->value ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ !$setting->value ? 'selected' : '' }}>No</option>
                </select>
            @elseif($setting->type === 'number')
                <input type="number" step="0.01" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" class="w-full border rounded p-2">
            @else
                <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" class="w-full border rounded p-2">
            @endif
            @error($setting->key)<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        @endforeach
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar cambios</button>
    </div>
</form>
@endsection
