<?php

namespace App\Http\Controllers;

use App\Jobs\ProductJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ImportProduct;
use Illuminate\Support\Facades\Queue;

class UploadController extends Controller
{

    public function index()
    {
        $imports = ImportProduct::orderBy('id','DESC')->get();
        return view('welcome')->with('imports', $imports);
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $path = $file->store('csv_files');

        // dispatch job
        //$job=ProductJob::dispatch($path);

        $jobId = Queue::push(new ProductJob($path));

        $import = new ImportProduct();
        $import->import_date = date('Y-m-d h:i:s');
        $import->file = $file->getClientOriginalName();
        $import->status = "pending";
        $import->job_id = $jobId;
        $import->save();

        session()->flash('success', 'File has been uploaded successfully!');

        return redirect()->back();
    }

    public function status()
    {
        $imports = ImportProduct::orderBy('id','DESC')->get();
        return view("status")->with('imports', $imports);
    }
}
