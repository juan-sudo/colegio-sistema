<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::with("user")
            ->when($request->search, fn ($q, $search) => $q->where("code", "like", "%{$search}%")
                ->orWhere("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%")
                ->orWhere("specialty", "like", "%{$search}%"))
            ->paginate(20);

        return view("admin.teachers.index", compact("teachers"));
    }

    public function create()
    {
        return view("admin.teachers.create");
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profesor registrado correctamente.', 'teacher' => $teacher]);
        }

        return redirect()->route("admin.teachers.index")->with("success", "Profesor registrado correctamente.");
    }

    public function edit(Teacher $teacher)
    {
        return view("admin.teachers.edit", compact("teacher"));
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profesor actualizado correctamente.', 'teacher' => $teacher]);
        }

        return redirect()->route("admin.teachers.index")->with("success", "Profesor actualizado correctamente.");
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user->delete();
        $teacher->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profesor eliminado correctamente.']);
        }

        return back()->with("success", "Profesor eliminado correctamente.");
    }
}
