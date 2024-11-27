<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use App\Models\GalleryVideo;

class GalleryVideoAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $jabatan = $request->jabatan;
        $data = GalleryVideo::where('title_video', 'like', "%$search%")
            ->when($jabatan, function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $jabatan = $request->jabatan;
        $data = GalleryVideo::where('title_video', 'like', "%$search%")
            ->when($jabatan, function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return new CrudResource('success', 'Data GalleryVideo', $data);
    }
}
