<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekonPegawaiExportController;
use Illuminate\Support\Facades\Auth;
use App\Models\Document; 
use App\Livewire\LandingPage;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', LandingPage::class);

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

Route::get('/preview-temp', function (\Illuminate\Http\Request $request) {
    $file = $request->query('file'); // ambil dari query string
    $path = storage_path('app/' . $file);

    if (!file_exists($path)) {
        abort(404, 'File tidak ditemukan');
    }

    return response()->file($path);
})
->middleware('auth')
->name('preview.temp');
