<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cerner  FHIR Configurations
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for connecting to 
	| Smart's Bulk data access.
	| Cerner's SMART on FHIR API.
    |
    */
	'cerner' => [
		'client_id' => env('CERNER_CLIENT_ID'),
		'client_secret' => env('CERNER_CLIENT_SECRET'),
		'token_url' => env('CERNER_TOKEN_URL'),
		'server_url' => env('CERNER_SERVER_URL'),
		'bulk_url' => env('CERNER_BULK_URL'),
		'authorization_url' => env('CERNER_AUTHORIZATION_URL')
	],

	'smart' => [
		'client_id' => env('SMART_CLIENT_ID'),
		'client_secret' => env('SMART_CLIENT_SECRET'),
		'token_url' => env('SMART_TOKEN_URL'),
		'server_url' => env('SMART_SERVER_URL'), 
		'bulk_url' => env('SMART_BULK_URL'), 
		'authorization_url' => env('SMART_AUTHORIZATION_URL')
	],
];