<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekonPegawaiExportController;
use Illuminate\Support\Facades\Auth;
use App\Models\Document; 

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
    return view('welcome');
});

Route::get(
    '/rekon/pegawai/export',
    [RekonPegawaiExportController::class, 'export']
)->name('rekon.pegawai.export');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

Route::get('/preview-temp/{document}', function (Document $document) {
    abort_unless($document->temp_path, 404);

    return response()->file(
        storage_path('app/' . $document->temp_path)
    );
})
->middleware('auth')
->name('preview.temp');
