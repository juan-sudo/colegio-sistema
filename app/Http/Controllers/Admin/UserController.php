<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    protected array $roles = ["admin", "teacher", "student", "parent"];

    public function index(Request $request): Response
    {
        $query = User::query()
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("email", "like", "%{$search}%")
                ->orWhere("role", "like", "%{$search}%"))
            ->when($request->filled("status"), fn ($q) => $q->where("active", $request->boolean("status")));

        $users = $this->applySort($query, $request, ["name", "email", "role", "phone", "active"], "name")
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render("Admin/Users/Index", [
            "users" => $users,
            "roles" => $this->roles,
            "filters" => [
                "search" => $request->search,
                "status" => $request->status,
                "per_page" => $this->perPage($request),
                "sort_by" => $request->sort_by,
                "sort_dir" => $request->sort_dir,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6",
            "role" => "required|in:admin,teacher,student,parent",
            "phone" => "nullable|string|max:20",
            "active" => "boolean",
        ]);

        $data["active"] = $request->boolean("active");
        $data["password"] = Hash::make($data["password"]);

        User::create($data);

        return redirect()->route("admin.users.index")->with("success", "Usuario registrado correctamente.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email," . $user->id,
            "password" => "nullable|string|min:6",
            "role" => "required|in:admin,teacher,student,parent",
            "phone" => "nullable|string|max:20",
            "active" => "boolean",
        ]);

        $data["active"] = $request->boolean("active");

        if (!empty($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        } else {
            unset($data["password"]);
        }

        $user->update($data);

        return redirect()->route("admin.users.index")->with("success", "Usuario actualizado correctamente.");
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with("success", "Usuario eliminado correctamente.");
    }

    public function toggleActive(User $user)
    {
        $user->update(["active" => !$user->active]);

        return back()->with("success", $user->active ? "Usuario activado correctamente." : "Usuario desactivado correctamente.");
    }
}
