<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class AnnouncementAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = Announcement::with('major')
            ->where('title', 'like', "%$search%")
            ->orderBy('announcement_date', 'desc')
            ->orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = Announcement::with('major')
            ->where('title', 'like', "%$search%")
            ->orderBy('announcement_date', 'desc')
            ->orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->get();
        return new CrudResource('success', 'Data Kelas', $data);
    }
}
