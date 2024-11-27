<?php

namespace App\Http\Controllers\API;

use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class AcademicCalendarAPI extends Controller
{
    function index(Request $request)
    {
        $limit = $request->limit;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = AcademicCalendar::orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->paginate($limit);
        return new CrudResource('success', 'Data Academic Calendar', $data);
    }

    function all(Request $request)
    {
        $sortby = $request->sortby;
        $order = $request->order;
        $data = AcademicCalendar::orderBy($sortby ?? 'created_at', $order ?? 'desc')
            ->get();
        return new CrudResource('success', 'Data Academic Calendar', $data);
    }

    function show($id)
    {
        $data = AcademicCalendar::with('major')->find($id);
        return new CrudResource('success', 'Data Academic Calendar', $data);
    }
}
