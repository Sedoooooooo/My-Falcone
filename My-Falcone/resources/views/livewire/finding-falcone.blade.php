<div x-data="{
    planets: @entangle('planets'),
    vehicles: @entangle('vehicles'),
    selectedPlanets: @entangle('selectedPlanets'),
    selectedVehicles: @entangle('selectedVehicles'),
    timeTaken: @entangle('timeTaken'),
    vehicleCounts: { 'Space pod': 3, 'Space rocket': 2 }, // Sample counts, modify as needed
    disabledPlanets: [],

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

    <div class="flex flex-row items-center justify-between w-full mb-6 ml-6">
        <!-- Main Title -->
        <span class="text-4xl font-bold mx-auto">Finding Falcone!</span>

        <!-- Header Section with Links -->
        <div class="text-sm ml-auto">
            <a href="/finding-falcone" class="text-black">Reset</a>
        </div>
    </div>

    <!-- Instructions for Selecting Planets -->
    <p class="mb-4 text-xl">Select planets you want to search in:</p>

    <!-- Updated Grid Layout for Destination and Vehicle Selection -->
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
            Time taken: <span x-text="timeTaken"></span> hours
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
                hover:bg-gray-600 hover:text-white hover:scale-105">
        Find Falcone!
    </button>


</div>