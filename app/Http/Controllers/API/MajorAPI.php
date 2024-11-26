<?php

namespace App\Http\Controllers\API;

use App\Models\Major;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class MajorAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = Major::where('major_nm', 'like', "%$search%")
            ->orderBy($sortby ?? 'major_nm', $order ?? 'asc')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = Major::where('major_nm', 'like', "%$search%")
            ->orderBy($sortby ?? 'major_nm', $order ?? 'asc')
            ->get();
        return new CrudResource('success', 'Data Kelas', $data);
    }
}
