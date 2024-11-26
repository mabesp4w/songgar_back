<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use App\Models\Slide;

class SlideAPI extends Controller
{
    function index(Request $request)
    {
        $limit = $request->limit;
        $data = Slide::orderBy('position', 'asc')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all()
    {
        $data = Slide::orderBy('position', 'asc')->get();
        return new CrudResource('success', 'Data Slide', $data);
    }
}
