<?php

namespace App\Services;

use App\Contracts\IFhirService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class SmartService extends FhirService implements IFhirService
{
    protected $contentLocation = "";

    public function __construct()
    {
        $this->clientId = config('fhir.smart.client_id');
        $this->clientSecret = config('fhir.smart.client_secret');
        $this->serverUrl = config('fhir.smart.server_url');
        $this->tokenUrl = config('fhir.smart.token_url');
		$this->bulkDataUrl = config('fhir.smart.bulk_url');
    }

    public function prepareExport()
    {
        $this->requestAccessToken();

		$contentLocation = null;

		$headers = [
			'accept' => 'application/fhir+json',
			'prefer' => 'respond-async',
		];

		$response = Http::withHeaders($headers)
			->get($this->bulkDataUrl .'/$export?_type=Observation%2CCondition%2CMedication%2CDocumentReference%2CPatient%2CImmunization%2CDiagnosticReport');

		if ($response->successful()) {
			$contentLocation = $response->header('Content-Location');

            $this->setStatusUrlLocation($contentLocation);
		} else {
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
        
		$response = Http::get($contentLocation);

		if ($response->successful()) {
			$responseData = json_decode($response->getBody()->getContents(), true);

			if(isset($responseData['output'])){
				Cache::put('Smart.bulkOutputData', $responseData['output'], now()->addHour());

				return $responseData['output'];
			}else{
				return response()->json(['error' => 'Response is not ready yet. Please try later'], $response->status());
			}
		} else {
			return response()->json(['error' => 'Failed to get status endpoint'], $response->status());
		}
	}
    
	public function fetchData($key)
	{
        $bulkOutputData = Cache::get('Smart.bulkOutputData');
		$url = $bulkOutputData[$key]['url'];

        $client = new Client();
		$response = $client->request('GET', $url);
	
		if ($response->getStatusCode() == 200) {
			return $response->getBody()->getContents();	
		}
	}
   
    protected function setStatusUrlLocation(string $url)
    {
        Cache::put('Smart.bulkStatusURI', $url, now()->addHour());
    }

    protected function getStatusUrlLocation(): string
    {
        return Cache::get('Smart.bulkStatusURI');
    }
}