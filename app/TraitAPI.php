<?php

namespace App;
use Illuminate\Support\Facades\Http;
use Log;

trait TraitAPI
{

    public function getApi(string $method, string $endpoint, array $data = []){

        $apiKey = config('constant.intract_key');
        $apiUrl = config('constant.intract_url');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$apiKey,
                'Content-Type' => 'application/json',
            ])->{$method}("{$apiUrl}{$endpoint}", $data);

            Log::info('Interakt API Response:', ['endpoint' => $endpoint, 'response' => $response->body()]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Interakt API Failed', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Interakt Exception', ['error' => $e->getMessage()]);
        }

        return null;

    }

    public function send_whatsapp($data){
        $endpoint = '/message/';
        $method = "POST";
        $this->getApi($method, $endpoint,$data);
    }
}
