<div x-data="{
    planets: @entangle('planets'),
    vehicles: @entangle('vehicles'),
    selectedPlanets: @entangle('selectedPlanets'),
    selectedVehicles: @entangle('selectedVehicles'),
    timeTaken: @entangle('timeTaken'),
    vehicleCounts: { 'Space pod': 2, 'Space rocket': 1, 'Space shuttle': 1, 'Space ship': 2 }, // Sample counts only, depends on the API data
    disabledPlanets: [],
    showInstructions: false,  // For showing the instructions modal

    updateVehicleUnits() {
        let selectedVehicleCounts = {};

        this.selectedVehicles.forEach(vehicle => {
            if (vehicle) {
                selectedVehicleCounts[vehicle] = selectedVehicleCounts[vehicle] ? selectedVehicleCounts[vehicle] + 1 : 1;
            }
        });

        this.vehicles.forEach(vehicle => {
            if (selectedVehicleCounts[vehicle.name]) {
                this.vehicleCounts[vehicle.name] = vehicle.total_no - selectedVehicleCounts[vehicle.name];
            } else {
                this.vehicleCounts[vehicle.name] = vehicle.total_no;
            }

            if (this.vehicleCounts[vehicle.name] < 0) {
                this.vehicleCounts[vehicle.name] = 0;
            }
        });

        this.calculateTime();
    },

    calculateTime() {
        let totalTime = 0;
        this.selectedPlanets.forEach((planet, index) => {
            if (planet && this.selectedVehicles[index]) {
                let planetData = this.planets.find(p => p.name === planet);
                let vehicleData = this.vehicles.find(v => v.name === this.selectedVehicles[index]);

                if (planetData && vehicleData) {
                    totalTime += planetData.distance / vehicleData.speed;
                }
            }
        });
        this.timeTaken = totalTime;
    },

    checkDisabledPlanets() {
        this.disabledPlanets = this.selectedPlanets.filter(planet => planet !== null);
    },

    canFindFalcone() {
        return this.selectedPlanets.length === 4 &&
               this.selectedPlanets.every(planet => planet) &&
               this.selectedVehicles.length === 4 &&
               this.selectedVehicles.every(vehicle => vehicle);
    },

    init() {
        this.checkDisabledPlanets();
        this.updateVehicleUnits();
    }
}"
x-init="$watch('selectedPlanets', () => { checkDisabledPlanets(); updateVehicleUnits(); })"
x-effect="updateVehicleUnits()"
class = "flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4"
>

    <div class="flex flex-row justify-end w-full mb-6">
        <div class="flex items-center">
            <span
            <a href="#" @click.prevent="$wire.resetForm()" class="text-black">
                Reset
            </a>
            </span>
            <span class="px-4">|</span>
            <a href="#" @click.prevent="showInstructions = !showInstructions" class="text-black hover:text-grey-900">
                How to Play
            </a>
        </div>
    </div>

    <div class="flex flex-row justify-center w-full mb-6">
        <!-- Title (Centered) -->
        <span class="text-4xl font-bold">Finding Falcone!</span>
    </div>

    <div class="flex flex-row justify-between w-full mb-6 space-x-6">
        <!-- Select planets you want to search in (Centered) -->
        <p class="mb-6 text-xl text-center mx-auto">Select planets you want to search in:</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full justify-center">
        @foreach(range(0,3) as $index)
            <div class="flex flex-col items-center p-6 rounded-lg border border-gray-300 shadow-lg">
                <label class="font-semibold text-lg mb-2">Destination {{ $index + 1 }}</label>

                <!-- Planet Selection -->
                <select 
                    wire:model="selectedPlanets.{{ $index }}" 
                    class="border border-gray-300 rounded px-3 py-2 w-full mb-4 text-black"
                    x-model="selectedPlanets[{{ $index }}]"
                    @change="checkDisabledPlanets(); updateVehicleUnits()"
                >
                    <option value="">Select</option>
                    @foreach($planets as $planet)
                        <option 
                            value="{{ $planet['name'] }}" 
                            :disabled="disabledPlanets.includes('{{ $planet['name'] }}') && selectedPlanets[{{ $index }}] !== '{{ $planet['name'] }}'">
                            {{ $planet['name'] }}
                        </option>
                    @endforeach 
                </select>

                <!-- Vehicle Selection -->
                <div class="space-y-2" x-show="$wire.selectedPlanets[{{ $index }}]">
                    <template x-for="vehicle in vehicles" :key="vehicle.name">
                        <div>
                            <input type="radio" :value="vehicle.name"
                                class="text-blue-500"
                                x-model="selectedVehicles[{{ $index }}]"
                                :disabled="vehicleCounts[vehicle.name] <= 0"
                            >
                            <span x-text="vehicle.name"></span> (<span x-text="vehicleCounts[vehicle.name]"></span>)
                        </div>
                    </template>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Time Taken Display Section -->
    <div class="flex justify-between items-center w-full mb-6">
        <div class="text-lg font-semibold">
            Time taken: <span x-text="timeTaken"></span>
        </div>
    </div>

    <!-- Find Falcone Button -->
    <button 
        wire:click="findFalcone"
        x-bind:disabled="!canFindFalcone()"
        x-bind:class="{
            'bg-black text-black cursor-not-allowed': !canFindFalcone(), 
            'bg-gray-600 text-gray-300 cursor-pointer': canFindFalcone()
        }"
        class="border text-sm py-3 px-6 rounded-md transition-all duration-300 
                hover:bg-gray-600 hover:text-white hover:scale-105 mb-6">
        Find Falcone!
    </button>

    <!-- Modal for How to Play Instructions -->
    <div x-show="showInstructions" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
        <div class="bg-white p-4 rounded-lg shadow-lg w-96 max-w-full">

            <h1 class="text-2xl font-bold mb-4 text-center flex justify-center">How to Play: Finding Falcone</h1>
            <p class="text-sm mb-4 text-center flex justify-center">
                In this game, you’ll need to choose planets and vehicles to search for the elusive planet Falcone.
            </p>
            
            <!-- Step 1: Select Planets -->
            <p class="text-sm font-semibold mb-2">Step 1: Select Planets (Destination 1-4)</p>
            <p class="text-sm mb-4">
                Select a planet for each of the four destinations (Destination 1 to Destination 4) from the dropdown menu.
            </p>

            <!-- Step 2: Select Vehicles -->
            <p class="text-sm font-semibold mb-2">Step 2: Select Vehicles for Each Destination</p>
            <p class="text-sm mb-4">
                After selecting a planet, choose a vehicle for that destination. Each vehicle can only be selected once.
            </p>

            <!-- Step 3: Check the Time Taken -->
            <p class="text-sm font-semibold mb-2">Step 3: Check the Time Taken</p>
            <p class="text-sm mb-4">
                The Time Taken field will automatically update as you make selections. Keep an eye on the time to ensure a quick search!
            </p>

            <!-- Step 4: Find Falcone -->
            <p class="text-sm font-semibold mb-2">Step 4: Find Falcone!</p>
            <p class="text-sm mb-4">
                Once you've selected planets and vehicles for all four destinations, click <strong>Find Falcone!</strong> to submit your mission.
            </p>

            <!-- Step 5: Reset -->
            <p class="text-sm font-semibold mb-2">Step 5: Reset</p>
            <p class="text-sm mb-4">
                Click <strong>Reset</strong> if you want to start over and make new selections.
            </p>

            <a href="#" @click.prevent="showInstructions = false" class="mt-4 justify-center flex text-center bg-blue-500 text-black py-2 px-4 rounded-lg transition-all duration-300 hover:bg-blue-600">
                Close Instructions
            </a>
        </div>
    </div>

</div>