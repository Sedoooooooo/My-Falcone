<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
    <h1 class="text-3xl font-bold mb-6">Finding Falcone!</h1>

    @if (session('status') == 'success')
        <p class="text-xl font-semibold mb-4">Success! Congratulations on Finding Falcone. King Shan is mighty pleased.</p>
        <p class="text-lg mb-4">Time taken: {{ session('time_taken') }}</p>
        <p class="text-lg mb-6">Planet found: <strong>{{ session('planet_found') }}</strong></p>
    @else
        <p class="text-lg text-red-500 mb-6">Sorry, Falcone was not found.</p>
    @endif

    <a href="/finding-falcone" class="border text-black text-sm py-3 px-6 rounded-md hover:bg-gray-600 hover:text-white hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
        Start Again
    </a>
</div>  
