<?php

namespace App\Services;

use App\Contracts\IFhirService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CernerService extends FhirService implements IFhirService
{
    protected $contentLocation = "";

    public function __construct()
    {
        $this->clientId = config('fhir.cerner.client_id');
        $this->clientSecret = config('fhir.cerner.client_secret');
        $this->serverUrl = config('fhir.cerner.server_url');
        $this->tokenUrl = config('fhir.cerner.token_url');
		$this->bulkDataUrl = config('fhir.cerner.bulk_url');
    }

    public function prepareExport()
    {
        $this->requestAccessToken();
        $base64Credentials = $this->getAccessToken();

		$headers = [
			'accept' => 'application/fhir+json',
			'prefer' => 'respond-async',
            'Authorization' => 'Bearer '.$base64Credentials
		];

		$response = Http::withHeaders($headers)
			->get($this->bulkDataUrl .'/$export'); 

		if ($response->successful()) {
			$contentLocation = $response->header('Content-Location');
            $this->setStatusUrlLocation($contentLocation);
		} elseif($response->status() == 401){
            $this->requestAccessToken();
            $this->prepareExport();
        }
         else {
			return response()->json(['error' => 'Failed to prepare bulk data export'], $response->status());
		}

		return $contentLocation;
    }

    public function getBulkStatus()
	{
        $contentLocation = $this->getStatusUrlLocation();
        
		if (!$contentLocation) {
            return response()->json(['error' => 'Content-Location URL not found'], 404);
		}
        
        $base64Credentials = $this->getAccessToken();
        $headers = [
			'accept' => 'application/fhir+json',
            'Authorization' => 'Bearer '.$base64Credentials
		];

		$response = Http::withHeaders($headers)
			->get($contentLocation); 


		if ($response->successful()) {
			$responseData = json_decode($response->getBody()->getContents(), true);

			if(isset($responseData['output'])){
				Cache::put('Cerner.bulkOutputData', $responseData['output'], now()->addHour());

				return $responseData['output'];
			}else{
				return response()->json(['error' => 'Response is not ready yet. Please try later'], $response->status());
			}
		} elseif($response->status() == 401) {
            $this->requestAccessToken();
            $this->getBulkStatus();
        } else {
			return response()->json(['error' => 'Failed to get status endpoint'], $response->status());
		}
	}
    
	public function fetchData($key)
	{
		$bulkOutputData = Cache::get('Cerner.bulkOutputData');
		$url = $bulkOutputData[$key]['url'];

        $base64Credentials = $this->getAccessToken();
        $headers = [
			'accept' => 'application/fhir+ndjson',
            'Authorization' => 'Bearer '.$base64Credentials
		];

		$response = Http::withHeaders($headers)
			->get($url); 

        if ($response->successful()) {
			return $response->getBody()->getContents();	
		} elseif($response->status() == 401) {
            $this->requestAccessToken();
            $this->fetchData($key);
        } else {
            return response()->json(['error' => "Failed to fetch {$key} data"], $response->status());
        }
	
	}
   
    protected function setStatusUrlLocation(string $url)
    {
        Cache::put('Cerner.bulkStatusURI', $url, now()->addHour());
    }

    protected function getStatusUrlLocation(): string
    {
        return Cache::get('Cerner.bulkStatusURI');
    }
}