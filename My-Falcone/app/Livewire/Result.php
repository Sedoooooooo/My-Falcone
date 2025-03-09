<?php
namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Livewire\Component;

class Result extends Component
{
    public $planets  = [];
    public $vehicles = [];
    public $result   = [];
    public $errorMessage;

    public function mount()
    {
        // Retrieve the selected planets and vehicles from session the Finding Falcone component
        $this->planets  = session()->get('selectedPlanets', []);
        $this->vehicles = session()->get('selectedVehicles', []);

        Log::debug('Session Data: Selected Planets and Vehicles', [
            'planets' => $this->planets,
            'vehicles' => $this->vehicles
        ]);

        if (empty($this->planets) || empty($this->vehicles)) {
            $this->errorMessage = 'Invalid input. Please select planets and vehicles.';
            Log::error('Invalid input, planets or vehicles are empty.');
            return;
        }

        // Retrieve status, planet_found, and time_taken from session, if available
        $status = session('status', null);
        $planetFound = session('planet_found', 'Not Found');
        $timeTaken = session('time_taken', null);

        Log::debug('Result Data from Session:', ['status' => $status, 'planet_found' => $planetFound, 'time_taken' => $timeTaken]);

        // Check for the status in the session and set the result data accordingly
        if ($status === 'success') {
            $this->result = [
                'status'       => $status,
                'planet_found' => $planetFound,
                'time_taken'   => $timeTaken,
            ];
        } else {
            $this->result = [
                'status'       => 'failure',
                'planet_found' => 'Not Found',
                'time_taken'   => 0,
            ];
        }

        Log::info('Result stored:', ['result' => $this->result]);
    }

    public function render()
    {
        return view('livewire.result', [
            'errorMessage' => $this->errorMessage,
            'result'       => $this->result,
        ]);
    }
}
