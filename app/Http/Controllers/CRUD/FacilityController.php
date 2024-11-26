<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;

class FacilityController extends Controller
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
            'nm_facility' => 'required',
            'img_facility' => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ];

        $messages = [
            'nm_facility.required' => 'Nama harus diisi.',
            'img_facility.image' => 'File harus berupa gambar.',
            'img_facility.mimes' => 'File harus berupa jpeg,png,jpg,gif,svg.',
            'img_facility.max' => 'File maksimal 2MB.',
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
        $data = Facility::where(function ($query) use ($search) {
            $query->where('nm_facility', 'like', "%$search%");
        })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate(10);
        return new CrudResource('success', 'Data Facility', $data);
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
            if ($request->hasFile('img_facility')) {
                $img_facility = $this->imgController->addImage('img_facility', $data_req['img_facility']);
                // jika foto gagal di upload
                if (!$img_facility) {
                    DB::rollback();
                    return new CrudResource('error', 'Gagal Upload Foto', null);
                }
                $data_req['img_facility'] = "storage/$img_facility";
            } else {
                $data_req['img_facility'] = null;
            }

            // add data
            Facility::create($data_req);
            // get last data
            $data = Facility::latest()->first();
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
            $facFacility = Facility::findOrFail($id);
            // find file img_facility
            $img_facility = $facFacility->img_facility;
            // export img_facility
            if ($request->hasFile('img_facility')) {
                // remove file img_facility jika ada
                if ($img_facility) {
                    File::delete($img_facility);
                }
                $img_facility = $this->imgController->addImage('img_facility', $data_req['img_facility']);
                if (!$img_facility) {
                    return new CrudResource('error', 'Gagal Upload Img_facility', null);
                }
                $data_req['img_facility'] = "storage/$img_facility";
            } else {
                $data_req['img_facility'] = $img_facility;
            }
            // Update the content
            $facFacility->update($data_req);
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Diperbarui', $facFacility);
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
        $data = Facility::findOrFail($id);
        // get img_facility
        $img_facility = $data->img_facility;
        // remove img_facility img_facility
        if ($img_facility) {
            File::delete($img_facility);
        }
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
