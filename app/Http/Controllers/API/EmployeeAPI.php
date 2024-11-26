<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use App\Models\Employee;

class EmployeeAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $role = $request->role;
        $data = Employee::with(['user', 'major'])->whereHas('user', function ($q) use ($search) {
            $q->where('email', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->pluck('nm_employee')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all()
    {
        $data = Employee::with(['user', 'major'])->all();
        return new CrudResource('success', 'Data Employee', $data);
    }
}
