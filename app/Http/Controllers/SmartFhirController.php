<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Contracts\IFhirService;

class SmartFhirController extends Controller
{

    public function __construct(private IFhirService $smartService)
    {
    }
	
	public function prepareExport()
    {
		return $this->smartService->prepareExport();
    }
	
	public function getBulkStatus()
	{
		return $this->smartService->getBulkStatus();
	}
	
	public function fetchData($key)
	{
        return $this->smartService->fetchData($key);
	}
	
	 public function view()
    {
        return view('smart', ['bulkData' => Cache::get('Smart.bulkOutputData', [])]);
    }
	
	
}
