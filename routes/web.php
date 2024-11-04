<?php

use App\Http\Controllers\Api\LeadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('leads.index');
});
Route::get('/leads/{id}/updates', [LeadController::class, 'showUpdates'])->name('leads.updates');
