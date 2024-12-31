<?php

use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\VoitureController;
use App\Services\RouteCalculatorService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Models\User;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::post('/map/calculate-route', [MapController::class, 'calculateRoute'])->name('map.calculate');
Route::post('/map/current-location', [MapController::class, 'getCurrentLocation'])->name('map.location');

Route::get('/test-google-maps', function() {
    try {
        $service = app(RouteCalculatorService::class);
        return response()->json([
            'success' => true,
            'message' => 'Configuration Google Maps valide'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
});

// routes/web.php
Route::get('/test-google-maps-complete', function () {
    try {
        $service = app(RouteCalculatorService::class);

        // Test réel avec des coordonnées
        $result = $service->calculateRoute(
            'Paris, France',
            'Lyon, France'
        );

        return response()->json([
            'success' => true,
            'message' => 'Test complet réussi',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => app()->environment('local') ? $e->getTrace() : null
        ], 422);
    }
});




Route::resource('voitures', VoitureController::class);
Route::resource('chauffeurs', ChauffeurController::class);



// Return view Template page

Route::get('/', function () {
    return view("home");
})->name('home');

Route::get('/about', function () {
    return view("about.index");
})->name('about');
Route::get('/contact', function () {
    return view("contact");
});



// Route Method
/*

* GET
* POST
* PUT
* PATCH
* DELETE

*/

















// // Route Grouping
// Route::group(['prefix'=>'customer'], function(){

//     Route::get('/', function(){
//         return "<h1>Customer List</h1>";
//     });
//     Route::get('/create', function(){
//         return "<h1>Customer Create</h1>";
//     });
//     Route::get('/show', function(){
//         return "<h1>Customer Show</h1>";
//     });

// });



// Route::get('/home', function () {
//   echo "this is Home Page";
// });



// Route::get("/contact",[ContactController::class, "index"])->name("con");

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         $users = User::all();
//         return view('dashboard', compact("users"));
//     })->name('dashboard');
// });
// Route::get('about2', function () {
//     return "<h1>this is about page</h1>";
// })->name('about');
// Route::get('contact2', function () {
//     return "<h1>this is a contact page</h1>";
// });
// Route::get('contact2/{id}', function ($id) {
//     return "<h1>this is a contact page with id: $id</h1>";
// })->name('edit-contact');

// Route::get('home', function () {
//     return "<a href='".route('edit-contact',1)."'>about</a>";
// });
// Fallback Route

Route::fallback(function(){
    return "<h1>La route n'existe pas.</h1>";
});
