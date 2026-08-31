<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $groups = Setting::select('group')->distinct()->pluck('group');
        $selectedGroup = $request->group ?? $groups->first();

        $settings = Setting::where('group', $selectedGroup)->get();

        return view('admin.settings.index', compact('settings', 'groups', 'selectedGroup'));
    }

    public function update(Request $request)
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            if ($request->has($setting->key)) {
                $value = $request->input($setting->key);

                if ($setting->type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif ($setting->type === 'number') {
                    $value = (float) $value;
                } elseif ($setting->type === 'json') {
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
