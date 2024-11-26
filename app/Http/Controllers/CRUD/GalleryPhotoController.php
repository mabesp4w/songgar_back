<?php

namespace App\Http\Controllers\CRUD;

use App\Models\GalleryPhoto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;

class GalleryPhotoController extends Controller
{
    protected $imgController;
    // make construct
    public function __construct()
    {
        // memanggil controller image
        $this->imgController = new ImgToolsController();
    }
    protected function spartaValidation($request, $id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'title_photo' => 'required',
            'photo_path' => "$required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ];

        $messages = [
            'title_photo.required' => 'title harus diisi.',
            'photo_path.required' => 'Foto harus diisi.',
            'photo_path.image' => 'File harus berupa gambar.',
            'photo_path.mimes' => 'File harus berupa jpeg,png,jpg,gif,svg.',
            'photo_path.max' => 'File maksimal 2MB.',
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
        $data = GalleryPhoto::where(function ($query) use ($search) {
            $query->where('title_photo', 'like', "%$search%");
        })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate(10);
        return new CrudResource('success', 'Data GalleryPhoto', $data);
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
        $validate = $this->spartaValidation($data_req);
        if ($validate) {
            return $validate;
        }
        // unset user
        unset($data_req['user']);
        DB::beginTransaction();
        try {
            // export foto
            if ($request->hasFile('photo_path')) {
                $photo_path = $this->imgController->addImage('photo_path', $data_req['photo_path']);
                // jika foto gagal di upload
                if (!$photo_path) {
                    DB::rollback();
                    return new CrudResource('error', 'Gagal Upload Foto', null);
                }
                $data_req['photo_path'] = "storage/$photo_path";
            }

            // add data
            GalleryPhoto::create($data_req);
            // get last data
            $data = GalleryPhoto::latest()->first();
            // add options
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Disimpan', $data);
        } catch (\Throwable $th) {
            // jika terdapat kesalahan
            DB::rollback();
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
        $validate = $this->spartaValidation($data_req, $id);
        if ($validate) {
            return $validate;
        }
        // unset user
        unset($data_req['user']);
        unset($data_req['_method']);
        DB::beginTransaction();
        try {
            $galleryPhoto = GalleryPhoto::findOrFail($id);
            // find file photo_path
            $photo_path = $galleryPhoto->photo_path;
            // export photo_path
            if ($request->hasFile('photo_path')) {
                // remove file photo_path jika ada
                if ($photo_path) {
                    File::delete($photo_path);
                }
                $photo_path = $this->imgController->addImage('photo_path', $data_req['photo_path']);
                if (!$photo_path) {
                    return new CrudResource('error', 'Gagal Upload Photo_path', null);
                }
                $data_req['photo_path'] = "storage/$photo_path";
            } else {
                $data_req['photo_path'] = $photo_path;
            }
            // Update the content
            $galleryPhoto->update($data_req);
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Diperbarui', $galleryPhoto);
        } catch (\Throwable $th) {
            // Jika terdapat kesalahan
            DB::rollback();
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
        $data = GalleryPhoto::findOrFail($id);
        // get photo_path
        $photo_path = $data->photo_path;
        // remove photo_path photo_path
        if ($photo_path) {
            File::delete($photo_path);
        }
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
