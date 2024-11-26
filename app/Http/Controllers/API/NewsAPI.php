<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class NewsAPI extends Controller
{
    function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->limit;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = News::where(function ($query) use ($search) {
            $query->where('title', 'like', "%$search%")
                ->orWhere('author', 'like', "%$search%");
        })
            ->orderBy('news_date', 'desc')
            ->orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->paginate($limit);
        return new CrudResource('success', 'Data Kelas', $data);
    }

    function all(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = News::where(function ($query) use ($search) {
            $query->where('title', 'like', "%$search%")
                ->orWhere('author', 'like', "%$search%");
        })
            ->orderBy('news_date', 'desc')
            ->orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->get();
        return new CrudResource('success', 'Data Kelas', $data);
    }
}
