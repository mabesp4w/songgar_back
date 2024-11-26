<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;
use App\Http\Controllers\TOOLS\MakeAccountController;

class EmployeeController extends Controller
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
            'nm_employee' => 'required',
            'img_employee' => "$required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ];

        $messages = [
            'nm_employee.required' => 'Nama harus diisi.',
            'img_employee.required' => 'File harus diisi.',
            'img_employee.image' => 'File harus berupa gambar.',
            'img_employee.mimes' => 'File harus berupa jpeg,png,jpg,gif,svg.',
            'img_employee.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
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
        $jabatan = $request->jabatan;
        $data = Employee::with(['major'])->where(function ($query) use ($search) {
            $query->where('nm_employee', 'like', "%$search%")
                ->orWhere('NIP', 'like', "%$search%");
        })
            ->when($jabatan, function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate(10);
        return new CrudResource('success', 'Data Employee', $data);
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
        DB::beginTransaction();

        try {
            // export foto
            if ($request->hasFile('img_employee')) {
                $img_employee = $this->imgController->addImage('img_employee', $data_req['img_employee']);
                // jika foto gagal di upload
                if (!$img_employee) {
                    DB::rollback();
                    return new CrudResource('error', 'Gagal Upload Foto', null);
                }
                $data_req['img_employee'] = "storage/$img_employee";
            }
            // simpan data
            Employee::create($data_req);
            // ambil data terakhir
            $data = Employee::with('major')->latest()->first();
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

        // remove _method from data_req
        unset($data_req['_method']);
        $data = Employee::findOrFail($id);
        // return $data_req;
        $validate = $this->spartaValidation($data_req, $id);
        if ($validate) {
            return $validate;
        }
        DB::beginTransaction();
        try {
            // find file img_employee
            $img_employee = $data->img_employee;
            // export img_employee
            if ($request->hasFile('img_employee')) {
                // remove file img_employee jika ada
                if ($img_employee) {
                    File::delete($img_employee);
                }
                $img_employee = $this->imgController->addImage('img_employee', $data_req['img_employee']);
                if (!$img_employee) {
                    return new CrudResource('error', 'Gagal Upload Img_employee', null);
                }
                $data_req['img_employee'] = "storage/$img_employee";
            } else {
                $data_req['img_employee'] = $img_employee;
            }

            $data->update($data_req);
            $data = Employee::with('major')->find($id);
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Diubah', $data);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // delete data employee
        $data = Employee::findOrFail($id);
        // get foto employee
        $img_employee = $data->img_employee;
        // remove img_employee img_employee
        if ($img_employee) {
            File::delete($img_employee);
        }
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
