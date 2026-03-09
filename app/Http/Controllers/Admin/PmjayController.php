<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PmjayImportService;
use App\Models\PmjayTreatment;

class PmjayController extends Controller
{

    protected $importService;

    public function __construct(PmjayImportService $importService)
    {
        $this->importService = $importService;
    }


    public function index()
    {
        $records = PmjayTreatment::with('hospital.district')
            ->latest()
            ->paginate(50);

        return view('admin.pmjay.index', compact('records'));
    }


    public function uploadForm()
    {
        return view('admin.pmjay.upload');
    }


    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|file'
        ]);

        $path = $request->file('file')->getRealPath();

        $this->importService->import($path);

        return redirect()
            ->route('admin.pmjay.index')
            ->with('success','Data imported successfully');
    }

}