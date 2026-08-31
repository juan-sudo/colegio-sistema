<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuardianRequest;
use App\Http\Requests\Admin\UpdateGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $guardians = Guardian::with("user")
            ->when($request->search, fn ($q, $search) => $q->where("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%")
                ->orWhere("phone_whatsapp", "like", "%{$search}%"))
            ->paginate(20);

        return view("admin.guardians.index", compact("guardians"));
    }

    public function create()
    {
        return view("admin.guardians.create");
    }

    public function store(StoreGuardianRequest $request)
    {
        $data = $request->validated();

        $user = \App\Models\User::create([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "password" => bcrypt($data["password"]),
            "role" => "parent",
            "phone" => $data["phone"] ?? null,
        ]);

        $guardian = Guardian::create([
            "user_id" => $user->id,
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "phone_whatsapp" => $data["phone_whatsapp"],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Apoderado registrado correctamente.', 'guardian' => $guardian]);
        }

        return redirect()->route("admin.guardians.index")->with("success", "Apoderado registrado correctamente.");
    }

    public function edit(Guardian $guardian)
    {
        return view("admin.guardians.edit", compact("guardian"));
    }

    public function update(UpdateGuardianRequest $request, Guardian $guardian)
    {
        $data = $request->validated();

        $guardian->update([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "phone_whatsapp" => $data["phone_whatsapp"],
        ]);

        $guardian->user->update([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "phone" => $data["phone"] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Apoderado actualizado correctamente.', 'guardian' => $guardian]);
        }

        return redirect()->route("admin.guardians.index")->with("success", "Apoderado actualizado correctamente.");
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->user->delete();
        $guardian->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Apoderado eliminado correctamente.']);
        }

        return back()->with("success", "Apoderado eliminado correctamente.");
    }
}
