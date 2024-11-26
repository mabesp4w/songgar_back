<?php

namespace App\Http\Controllers\API;

use App\Models\Structural;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class StructuralAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $data = Structural::where('position', 'like', "%$search%")
            ->orderByRaw("FIELD(position, 'Dekan', 'Wakil Dekan I', 'Wakil Dekan II', 'Ketua Jurusan', 'Wakil Ketua Jurusan', 'Sekretaris', 'Bendahara')")
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $data = Structural::where('position', 'like', "%$search%")
            ->orderByRaw("FIELD(position, 'Dekan', 'Wakil Dekan I', 'Wakil Dekan II', 'Ketua Jurusan', 'Wakil Ketua Jurusan', 'Sekretaris', 'Bendahara')")
            ->get();
        return new CrudResource('success', 'Data Kelas', $data);
    }
}
