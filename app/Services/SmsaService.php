<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;



class SmsaService
{
    public $base_url;
    public $api_key;
    public $create_shipment_url = "/api/shipment/b2c/new";

    public function __construct()
    {
        $this->base_url = config('smsa_config.smsa_url');
        $this->api_key = config('smsa_config.smsa_key');
    }

    /**
     * Send an HTTP request to a specified URL using a custom HTTP method.
     *
     * @param string $url The URL to which the HTTP request will be sent.
     * @param string $method The HTTP method to be used for the request (e.g., "GET", "POST").
     * @param array $data An optional array of data to be included in the request body.
     *
     * @return Illuminate\Http\Client\Response The response object representing the response from the HTTP request.
     *
     * @throws \Exception If there's an issue with sending the HTTP request.
     *
     * @example
     * ```
     * $httpClient = new YourHttpClient(); // Replace with the actual class name
     *
     * // Define the URL, HTTP method, and optional data
     * $url = 'https://api.example.com/resource';
     * $method = 'GET'; // You can use 'POST', 'PUT', or 'DELETE' as well
     * $data = ['key' => 'value', 'param1' => 'param1_value']; // Optional data
     *
     * // Send an HTTP request
     * $response = $httpClient->send($url, $method, $data);
     *
     * // Process the response
     * if ($response->successful()) {
     *     // Request was successful, handle the response data
     *     $responseData = $response->json();
     *     // Your code to handle successful response
     * } else {
     *     // Request failed, handle the error
     *     $errorResponse = $response->json();
     *     // Your code to handle the error
     * }
     * ```
     */
    public function send($url, $method, $data = [])
    {

        $response = Http::withHeaders([
            'apikey' => $this->api_key,
            'host' => $this->base_url,
            'content-type' => 'application/json',
        ])->$method($this->base_url . $url, $data);
            dd($response);
        return $response;
    }

    public function createShipment()
    {
        $postData =  [
            "ConsigneeAddress" => [ // client information
                "ContactName" => "SMSA Express JED", // user name req
                "ContactPhoneNumber" => "966000000", // user phone req
                "ContactPhoneNumber2" => "96600000", // user phone 2 ( optional )
                "Coordinates" => "21.589886,39.1662759", // user coordinates ( optional )
                "Country" => "SA", // user country req
                "District" => "Ar Rawhdah", // userDistrict ( optional )
                "PostalCode" => "", // o
                "City" => "Jeddah", // req
                "AddressLine1" => "سمسا حي الروضة", // req
                "AddressLine2" => "Ar Rawdah, Jeddah 23434", // o
                "ConsigneeID" => "" // client id required
            ],
            "ShipperAddress" => [ // admin info fixed
                "ContactName" => "Shipper company name",
                "ContactPhoneNumber" => "96600000000",
                "Coordinates" => "24.6864257,46.6995142",
                "Country" => "SA",
                "District" => "Sulimanyah",
                "PostalCode" => "63529",
                "City" => "Riyadh",
                "AddressLine1" => "SMSA Express HQ",
                "AddressLine2" => "Dababh St"
            ],
            "OrderNumber" => "FirstOrder001", // order id
            "DeclaredValue" => 10, // from method calcDeclaredValue // from eng ashraf
            "CODAmount" => 10, // order items number
            "Parcels" => 1, //Count of boxes // calcParcels from eng ashraf
            "ShipDate" => "2021-01-01T10:40:53", // now
            "ShipmentCurrency" => "SAR",
            "SMSARetailID" => "0", // ignore for now
            "WaybillType" => "PDF", // fixed
            "Weight" => 3, // calcWeight from eng ashraf
            "WeightUnit" => "KG", // fixed
            "ContentDescription" => "Shipment contents description", // fixed
            "VatPaid" => true, // fixed
            "DutyPaid" => false // fixed
        ];
        $postResponse = $this->send($this->create_shipment_url, 'post', $postData);

        if ($postResponse->successful()) {
            $responseData = $postResponse->json();
            dd($responseData);
        } else {
            $errorResponse = $postResponse->json();
            dd($errorResponse);
        }
    }
}
