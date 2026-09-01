<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuardianRequest;
use App\Http\Requests\Admin\UpdateGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuardianController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Guardian::with("user")
            ->when($request->search, fn ($q, $search) => $q->where("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%")
                ->orWhere("phone_whatsapp", "like", "%{$search}%"))
            ->when($request->filled("status"), fn ($q) => $q->whereHas("user", fn ($uq) => $uq->where("active", $request->boolean("status"))));

        $guardians = $this->applySort($query, $request, ["first_name", "phone_whatsapp"], "first_name")
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render("Admin/Guardians/Index", [
            "guardians" => $guardians,
            "filters" => [
                "search" => $request->search,
                "status" => $request->status,
                "per_page" => $this->perPage($request),
                "sort_by" => $request->sort_by,
                "sort_dir" => $request->sort_dir,
            ],
        ]);
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

        return redirect()->route("admin.guardians.index")->with("success", "Apoderado registrado correctamente.");
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

        return redirect()->route("admin.guardians.index")->with("success", "Apoderado actualizado correctamente.");
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->user->delete();
        $guardian->delete();

        return back()->with("success", "Apoderado eliminado correctamente.");
    }
}
