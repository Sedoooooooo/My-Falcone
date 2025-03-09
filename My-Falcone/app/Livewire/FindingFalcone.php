<?php
namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FindingFalcone extends Component
{
    public $planets             = [];
    public $vehicles            = [];
    public $selectedPlanets = [null, null, null, null];
    public $selectedVehicles = [null, null, null, null];
    public $timeTaken           = 0;
    public $vehicleCounts       = []; 

    public function mount()
    {
        $this->planets = Http::get('https://findfalcone.geektrust.com/planets')->json() ?? [ 
            // if the API has problem, it will return the default data
            ['name' => 'DONLON', 'distance' => 100],
            ['name' => 'ENCHAI', 'distance' => 200],
            ['name' => 'JEBING', 'distance' => 300],
            ['name' => 'SAPIR', 'distance' => 400],
            ['name' => 'LERBIN', 'distance' => 500],
            ['name' => 'PINGASOR', 'distance' => 600],
        ];

        $this->vehicles = Http::get('https://findfalcone.geektrust.com/vehicles')->json() ?? [ 
            // also here
            ['name' => 'SPACE POD', 'speed' => 2, 'max_distance' => 200, 'total_no' => 2],
            ['name' => 'SPACE ROCKET', 'speed' => 4, 'max_distance' => 300, 'total_no' => 1],
            ['name' => 'SPACE SHUTTLE', 'speed' => 5, 'max_distance' => 400, 'total_no' => 1],
            ['name' => 'SPACE SHIP', 'speed' => 10, 'max_distance' => 600, 'total_no' => 2],
        ];

        foreach ($this->vehicles as $vehicle) {
            $this->vehicleCounts[$vehicle['name']] = $vehicle['total_no'];
        }
    }

    private function calculateTime()
    {
        $totalTime = 0;

        foreach ($this->selectedPlanets as $index => $planet) {
            if (!empty($planet) && !empty($this->selectedVehicles[$index])) {
                $planetData = collect($this->planets)->firstWhere('name', $planet);
                $vehicleData = collect($this->vehicles)->firstWhere('name', $this->selectedVehicles[$index]);

                if ($planetData && $vehicleData) {
                    $totalTime += $planetData['distance'] / $vehicleData['speed'];
                }
            }
        }

        return $totalTime;
    }

    public function updatedSelectedPlanets()
    {   
        $this->timeTaken = $this->calculateTime();
    }

    public function updatedSelectedVehicles()
    {
        Log::debug('Selected Vehicles Updated', [
            'selectedVehicles' => $this->selectedVehicles, 
            'vehicleCountsBefore' => $this->vehicleCounts, 
        ]);

        // Calculate time and update vehicle counts
        $this->timeTaken = $this->calculateTime();
        $this->updateVehicleCounts();

        Log::debug('Vehicle Counts After Update', [
            'vehicleCounts' => $this->vehicleCounts
        ]);
    }

    public function updateVehicleCounts()
    {
        // Track the selected vehicles and adjust counts
        foreach ($this->selectedVehicles as $vehicle) {
            if ($vehicle) {
                // Check if vehicle has enough units left
                if ($this->vehicleCounts[$vehicle] > 0) {
                    $this->vehicleCounts[$vehicle]--;
                } else {
                    // Ensure it cannot go below zero
                    $this->vehicleCounts[$vehicle] = 0;
                }
            }
        }

        Log::debug('Vehicle Counts After Adjustment', ['vehicleCounts' => $this->vehicleCounts]);

        session()->put('vehicleCounts', $this->vehicleCounts);
    }

    public function findFalcone()
    {
        if (count($this->selectedPlanets) < 4 || count($this->selectedVehicles) < 4) {
            session()->flash('error', 'Please select 4 planets and 4 vehicles.');
            return;
        }

        $tokenResponse = Http::withHeaders([ // need this header for POST request, not accepting the request without this header
            'Accept' => 'application/json',
        ])->post('https://findfalcone.geektrust.com/token');

        Log::debug('Token Response Status:', ['status' => $tokenResponse->status()]);
        Log::debug('Token Response Body:', ['body' => $tokenResponse->body()]);

        $tokenResponse = $tokenResponse->json();

        $token = $tokenResponse['token'] ?? null;
        if (!$token) { // check if token is existing
            Log::error('Error: Token not received', ['response' => $tokenResponse]);
            session()->flash('error', 'Failed to retrieve authentication token.');
            return;
        }

        Log::debug('Using Token:', ['token' => $token]);

        $response = Http::withHeaders([ // this also required unless it will return 404 not found and the token will be null
            'Content-Type' => 'application/json', 
            'Accept'       => 'application/json', 
        ])->post('https://findfalcone.geektrust.com/find', [
            'token'         => $token, // pass the token to the API
            'planet_names'  => $this->selectedPlanets,
            'vehicle_names' => $this->selectedVehicles,
        ])->json();

        Log::debug('API Response:', ['response' => $response]);

        if (!isset($response['status'])) {
            session()->flash('error', 'Unexpected response from the server.');
            Log::error('Unexpected API response.', ['response' => $response]);
            return;
        }

        if ($response['status'] === 'success') {
            session()->flash('status', 'success');
            session()->flash('planet_found', $response['planet_name']);
            session()->flash('time_taken', $this->timeTaken);
            Log::info('Success: Falcone found!', [
                'planet' => $response['planet_name'],
                'time_taken' => $this->timeTaken,
            ]);
        } else {
            session()->flash('status', 'failure');
            Log::warning('Failure: Falcone not found');
        }

        session()->put('selectedPlanets', $this->selectedPlanets);
        session()->put('selectedVehicles', $this->selectedVehicles);
        session()->put('timeTaken', $this->timeTaken);

        $this->redirectRoute('result');
    }

    public function render()
    {
        return view('livewire.finding-falcone');
    }
}
