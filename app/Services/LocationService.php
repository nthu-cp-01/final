<?php

namespace App\Services;

use Aws\IotDataPlane\IotDataPlaneClient;
use Illuminate\Support\Facades\App;

class LocationService
{
    private IotDataPlaneClient $client;

    public function __construct()
    {
        $this->client = App::make('aws')->createClient('IotDataPlane');
    }

    public function getThingShadow(string $thingName, ?string $shadowName): array
    {
        try {
            $params = [
                'thingName' => $thingName,
            ];

            // Add shadow name if provided
            if ($shadowName) {
                $params['shadowName'] = $shadowName;
            }

            $result = $this->client->getThingShadow($params);

            // The payload is returned as a stream, so we need to get the contents
            $shadowState = json_decode($result['payload']->getContents(), true);

            // The AWS IoT Shadow state contains reported and desired states
            // We're interested in the reported state which contains the actual device readings
            return $shadowState['state']['reported'] ?? [
                'temperature' => 0,
                'humidity' => 0,
                'ac_is_enable' => false,
                'dehumidifier_is_enable' => false,
            ];

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to get thing shadow: ' . $e->getMessage());

            // Return default values to avoid breaking the application
            return [
                'temperature' => 0,
                'humidity' => 0,
                'ac_is_enable' => false,
                'dehumidifier_is_enable' => false,
            ];
        }
    }

    public function updateThingShadow(string $thingName, ?string $shadowName, array $payload): void
    {
        try {
            // Prepare the shadow document structure
            // The payload should be wrapped in a state object with desired property
            $shadowDocument = [
                'state' => [
                    'desired' => $payload
                ]
            ];

            $params = [
                'thingName' => $thingName,
                'payload' => json_encode($shadowDocument)
            ];

            // Add shadow name if provided
            if ($shadowName) {
                $params['shadowName'] = $shadowName;
            }

            // Update the thing shadow
            $this->client->updateThingShadow($params);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to update thing shadow: ' . $e->getMessage());

            // Re-throw the exception to let the controller handle it
            throw $e;
        }
    }
}
