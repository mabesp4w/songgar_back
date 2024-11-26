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
        $jabatan = $request->jabatan;
        $data = Employee::with(['major'])
            ->where('nm_employee', 'like', "%$search%")
            ->when($jabatan, function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->pluck('nm_employee')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $jabatan = $request->jabatan;
        $data = Employee::with(['major'])
            ->where('nm_employee', 'like', "%$search%")
            ->when($jabatan, function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->get();
        return new CrudResource('success', 'Data Employee', $data);
    }
}
