<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Teacher::with("user")
            ->when($request->search, fn ($q, $search) => $q->where("code", "like", "%{$search}%")
                ->orWhere("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%")
                ->orWhere("specialty", "like", "%{$search}%"))
            ->when($request->filled("status"), fn ($q) => $q->whereHas("user", fn ($uq) => $uq->where("active", $request->boolean("status"))));

        $teachers = $this->applySort($query, $request, ["code", "first_name", "specialty"], "first_name")
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render("Admin/Teachers/Index", [
            "teachers" => $teachers,
            "filters" => [
                "search" => $request->search,
                "status" => $request->status,
                "per_page" => $this->perPage($request),
                "sort_by" => $request->sort_by,
                "sort_dir" => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();

        $user = \App\Models\User::create([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "password" => bcrypt($data["password"]),
            "role" => "teacher",
            "phone" => $data["phone"] ?? null,
        ]);

        $teacher = Teacher::create([
            "user_id" => $user->id,
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "code" => $data["code"],
            "specialty" => $data["specialty"] ?? null,
        ]);

        return redirect()->route("admin.teachers.index")->with("success", "Profesor registrado correctamente.");
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();

        $teacher->update([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "code" => $data["code"],
            "specialty" => $data["specialty"] ?? null,
        ]);

        $teacher->user->update([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "phone" => $data["phone"] ?? null,
            "active" => $data["active"] ?? true,
        ]);

        return redirect()->route("admin.teachers.index")->with("success", "Profesor actualizado correctamente.");
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user->delete();
        $teacher->delete();

        return back()->with("success", "Profesor eliminado correctamente.");
    }
}
