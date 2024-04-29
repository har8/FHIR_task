<?php

namespace App\Http\Controllers;

use App\Contracts\IFhirService;
use Illuminate\Support\Facades\Cache;

class CernerFhirController extends Controller
{

    public function __construct(private IFhirService $cernerService)
    {
    }

    public function prepareExport()
    {
		return $this->cernerService->prepareExport();
    }

    public function getBulkStatus()
	{
		return $this->cernerService->getBulkStatus();
	}
    
	public function fetchData($key)
	{
		return $this->cernerService->fetchData($key);
	}
	
	 public function view()
    {
        return view('cerner', ['bulkData' => Cache::get('Cerner.bulkOutputData', [])]);
    }
	
}
