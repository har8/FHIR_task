<?php

namespace App\Services;

class FhirService
{

    protected $clientId;
    protected $clientSecret;
    protected $serverUrl;
    protected $tokenUrl;
    protected $bulkDataUrl;

    // public function makeRequest(string $method, string $url, array $data = [], array $headers = [])
    // {

    //     $request = \Http;

    //     $withHeaders = '';

    //     if (count($headers)) {
    //         $withHeaders = '::withHeaders($headers)';
    //     }

    //     $response = Http::$method($url, $data);

    //     dd($response);


    // }
    
    protected function requestAccessToken()
    {
        $base64Credentials = base64_encode($this->clientId.":" . $this->clientSecret);

		$options = [
			'grant_type' => 'client_credentials',
			'scope' => 'system/Observation.read system/Patient.read system/AllergyIntolerance.read system/Binary.read system/Condition.read system/DiagnosticReport.read system/DocumentReference.read system/Immunization.read'
		];
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->tokenUrl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => http_build_query($options),
		  CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-Type: application/x-www-form-urlencoded',
			'cache-control: no-cache',
			'Authorization: Basic '.$base64Credentials
		  ),
		));

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if($httpCode == 200){
			$responseData = json_decode($response);
			$accessToken = $responseData->access_token;
           $this->setAccessToken($accessToken);

            // Return the access token
            return $accessToken;
        } else {
            // Handle the error case
            return response()->json(['error' => 'Failed to request access token'], $httpCode);
        }
    }

    protected function setAccessToken(string $accessToken)
    {
        session(['access_token' => $accessToken]);
    }

    protected function getAccessToken(): string
    {
        $access_token = session()->get('access_token');
        if(empty($access_token)){
            $this->requestAccessToken();
        }
        return $access_token;
    }
}
