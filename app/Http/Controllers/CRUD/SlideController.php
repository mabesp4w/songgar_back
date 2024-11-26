<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Slide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;

class SlideController extends Controller
{
    protected $imgController;
    // make construct
    public function __construct()
    {
        $this->imgController = new ImgToolsController();
    }

    protected function spartaValidation($request, $id = "", $user_id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'position' => 'required',
            'email' => 'unique:users,email,' . $user_id,
            'img_slide' => "$required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ];

        $messages = [
            'nm_slide.required' => 'Nama harus diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'img_slide.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ];
        $validator = Validator::make($request, $rules, $messages);

        if ($validator->fails()) {
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $validator->errors()->first(),
            ];
            return response()->json($message, 400);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $data = Slide::where(function ($query) use ($search) {
            $query->where('title', 'like', "%$search%")
                ->orWhere('position', 'like', "%$search%");
        })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate(10);
        return new CrudResource('success', 'Data Slide', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data_req = $request->all();
        // return $data_req;
        $validate = $this->spartaValidation($data_req);
        if ($validate) {
            return $validate;
        }
        try {
            // export foto
            if ($request->hasFile('img_slide')) {
                $img_slide = $this->imgController->addImage('img_slide', $data_req['img_slide']);
                // jika foto gagal di upload
                if (!$img_slide) {
                    return new CrudResource('error', 'Gagal Upload Foto', null);
                }
                $data_req['img_slide'] = "storage/$img_slide";
            }
            Slide::create($data_req);
            // ambil data terakhir
            $data = Slide::latest()->first();
            return new CrudResource('success', 'Data Berhasil Disimpan', $data);
        } catch (\Throwable $th) {
            // jika terdapat kesalahan
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
            return response()->json($message, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data_req = $request->all();
        // remove _method from data_req
        unset($data_req['_method']);
        $data = Slide::findOrFail($id);
        // return $data_req;
        $validate = $this->spartaValidation($data_req, $id);
        if ($validate) {
            return $validate;
        }
        try {
            // find file img_slide
            $img_slide = $data->img_slide;
            // export img_slide
            if ($request->hasFile('img_slide')) {
                // remove file img_slide jika ada
                if ($img_slide) {
                    File::delete($img_slide);
                }
                $img_slide = $this->imgController->addImage('img_slide', $data_req['img_slide']);
                if (!$img_slide) {
                    return new CrudResource('error', 'Gagal Upload Img_slide', null);
                }
                $req_slide['img_slide'] = "storage/$img_slide";
            } else {
                $req_slide['img_slide'] = $img_slide;
            }

            $data->update($req_slide);
            $data = Slide::find($id);

            return new CrudResource('success', 'Data Berhasil Diubah', $data);
        } catch (\Throwable $th) {
            // jika terdapat kesalahan
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
            return response()->json($message, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // delete data slide
        $data = Slide::findOrFail($id);
        // get foto slide
        $img_slide = $data->img_slide;
        // remove img_slide img_slide
        if ($img_slide) {
            File::delete($img_slide);
        }
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
