<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Support\Str;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;

class AnnouncementController extends Controller
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
            'title' => 'required',
            'content' => 'required',
        ];

        $messages = [
            'title.required' => 'title harus diisi.',
            'content.required' => 'Pertanyaan harus diisi.',
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
        $data = Announcement::with('major')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
            })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate(10);
        return new CrudResource('success', 'Data Announcement', $data);
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
            $contentHtml = $data_req['content'];
            // Load HTML content and find images
            $dom = new \DOMDocument();
            @$dom->loadHTML($contentHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');
            // get APP_URL from .env
            $APP_URL = env('APP_URL');
            // name folder
            $nm_folder = Str::uuid();
            // chane id with name folder
            $data_req['id'] = $nm_folder;
            foreach ($images as $img) {
                /** @var \DOMElement $img */
                $src = $img->getAttribute('src');

                // If the src attribute is base64
                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                    $imageName = $this->imgController->addBase64Image("Announcement/$nm_folder", $src);
                    // Replace src attribute with the new image URL
                    $img->setAttribute('src', $APP_URL . '/storage/' . $imageName);
                }
            }

            // Save the modified HTML content
            $contentHtml = $dom->saveHTML();
            $data_req['content'] = $contentHtml;
            Announcement::create($data_req);
            // get last data
            $data = Announcement::with('major')
                ->latest()->first();
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

    private function removeUnusedImages($oldContent, $newContent, $folder)
    {
        // Load old HTML content and find images
        $oldDom = new \DOMDocument();
        @$oldDom->loadHTML($oldContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $oldImages = $oldDom->getElementsByTagName('img');

        $oldSrcs = [];
        foreach ($oldImages as $img) {
            /** @var \DOMElement $img */
            $oldSrcs[] = $img->getAttribute('src');
        }

        // Load new HTML content and find images
        $newDom = new \DOMDocument();
        @$newDom->loadHTML($newContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $newImages = $newDom->getElementsByTagName('img');

        $newSrcs = [];
        foreach ($newImages as $img) {
            /** @var \DOMElement $img */
            $newSrcs[] = $img->getAttribute('src');
        }

        // Find images that are no longer in use
        $unusedImages = array_diff($oldSrcs, $newSrcs);

        foreach ($unusedImages as $image) {
            // Get relative path
            $relativePath = str_replace(env('APP_URL') . '/storage/', '', $image);
            Storage::disk('public')->delete($relativePath);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
            $announcement = Announcement::with('major')
                ->findOrFail($id);

            // edit announcement
            $oldContentHtml = $announcement->content;
            $newContentHtml = $data_req['content'];

            // Load HTML content and find images
            $dom = new \DOMDocument();
            @$dom->loadHTML($newContentHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');
            // get APP_URL from .env
            $APP_URL = env('APP_URL');
            // name folder
            $nm_folder = $announcement->id;
            foreach ($images as $img) {
                /** @var \DOMElement $img */
                $src = $img->getAttribute('src');

                // If the src attribute is base64
                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                    $imageName = $this->imgController->addBase64Image("Announcement/$nm_folder", $src);
                    // Replace src attribute with the new image URL
                    $img->setAttribute('src', $APP_URL . '/storage/' . $imageName);
                }
            }
            // Save the modified HTML content
            $newContentHtml = $dom->saveHTML();
            $data_req['content'] = $newContentHtml;
            // Update the content
            $announcement->update($data_req);
            // Remove unused images
            $this->removeUnusedImages($oldContentHtml, $newContentHtml, "Announcement/$nm_folder");
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Diperbarui', $announcement);
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
        $data = Announcement::findOrFail($id);
        // get folder path of student
        $folderPath = public_path('storage/Announcement/' . $data->id);
        // remove folder
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }
        // delete data
        $data->delete();
        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
