<?php

// Import the Livewire component

use App\Livewire\FindingFalcone; 
use App\Livewire\Result;
use Illuminate\Support\Facades\Route;

// Routes for the FindingFalcone component
Route::get('/finding-falcone', FindingFalcone::class)->name('finding-falcone');
Route::get('/result', Result::class)->name('result');
