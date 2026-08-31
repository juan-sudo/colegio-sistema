<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("email", "like", "%{$search}%")
                ->orWhere("role", "like", "%{$search}%"))
            ->orderBy("name")
            ->paginate(20);

        $roles = ["admin", "teacher", "student", "parent"];

        return view("admin.users.index", compact("users", "roles"));
    }

    public function create()
    {
        $roles = ["admin", "teacher", "student", "parent"];

        return view("admin.users.create", compact("roles"));
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

        $data["active"] = $request->has("active");
        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);

        if ($request->expectsJson()) {
            return response()->json(["success" => true, "message" => "Usuario registrado correctamente.", "user" => $user]);
        }

        return redirect()->route("admin.users.index")->with("success", "Usuario registrado correctamente.");
    }

    public function edit(User $user)
    {
        $roles = ["admin", "teacher", "student", "parent"];

        return view("admin.users.edit", compact("user", "roles"));
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

        $data["active"] = $request->has("active");

        if (!empty($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        } else {
            unset($data["password"]);
        }

        $user->update($data);

        if ($request->expectsJson()) {
            return response()->json(["success" => true, "message" => "Usuario actualizado correctamente.", "user" => $user]);
        }

        return redirect()->route("admin.users.index")->with("success", "Usuario actualizado correctamente.");
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(["success" => true, "message" => "Usuario eliminado correctamente."]);
        }

        return back()->with("success", "Usuario eliminado correctamente.");
    }

    public function toggleActive(User $user)
    {
        $user->update(["active" => !$user->active]);

        if (request()->expectsJson()) {
            return response()->json(["success" => true, "message" => $user->active ? "Usuario activado correctamente." : "Usuario desactivado correctamente."]);
        }

        return back()->with("success", $user->active ? "Usuario activado correctamente." : "Usuario desactivado correctamente.");
    }
}
