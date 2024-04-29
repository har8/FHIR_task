<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CernerFhirController extends Controller
{
    protected $authorizationUrl;
    protected $tokenUrl;
    protected $clientId;
    protected $clientSecret;
	protected $redirectUri;

    public function __construct()
    {
        $this->authorizationUrl = config('fhir.authorization_url');
        $this->tokenUrl = config('fhir.token_url');
        $this->clientId = "fed64a88-b3a2-493f-9750-91774a37948d";
        $this->clientSecret = config('fhir.client_secret');
		$this->redirectUri = env("APP_REDIRECT_URI");
    }

    public function index()
    {
        // Redirect users to Cerner's authorization URL
        return redirect()->away($this->authorizationUrl . '?client_id=' . $this->clientId . '&redirect_uri=' . urlencode($this->redirectUri) . '&response_tDype=code&scope=openid%20profile%20user/*.*%20launch/patient%20launch/patient.read%20offline_access');
    }

    public function callback(Request $request)
    {
        // Handle callback from Cerner's authorization server
        // Exchange authorization code for access token

        $client = new Client();
        $response = $client->post($this->tokenUrl, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => config('app.redirect_uri'),
                'code' => $request->input('code'),
            ],
        ]);

        $body = json_decode((string) $response->getBody());
        $access_token = $body->access_token;

        // Store access token securely (e.g., in session)
        session(['access_token' => $access_token]);

        // Redirect to a protected resource or return response
    }

    public function getResource()
    {
        // Retrieve resource from Cerner's SMART on FHIR API
        // Include access token in request headers

        $client = new Client();
        $response = $client->get('https://api.cerner.com/fhir/r4/Patient/123', [
            'headers' => [
                'Authorization' => 'Bearer ' . session('access_token'),
            ],
        ]);

        $resource = json_decode((string) $response->getBody());

        // Process resource or return response
    }
}
