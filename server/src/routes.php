<?php

use Fleetbase\VendorsForSuppliers\Http\Controllers\VendorsForSuppliersResourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix(config('vendors-for-suppliers.api.routing.prefix', 'starter'))->namespace('Fleetbase\VendorsForSuppliers\Http\Controllers')->group(
    function ($router) {
        /*
        |--------------------------------------------------------------------------
        | Vendors For Suppliers API Routes
        |--------------------------------------------------------------------------
        |
        | Primary internal routes for console.
        */
        $router->prefix(config('vendors-for-suppliers.api.routing.internal_prefix', 'int'))->group(
            function ($router) {
                $router->group(
                    ['prefix' => 'v1', 'middleware' => ['fleetbase.protected']],
                    function ($router) {
                        // $router->fleetbaseRoutes('resource');
                    }
                );
            }
        );
    }
);

Route::prefix('vendor-management')->group(function () {
    Route::get('/vendors', [VendorsForSuppliersResourceController::class, 'index']);
    Route::get('/vendors/{uuid}', [VendorsForSuppliersResourceController::class, 'show'])->name('vendor-management.show');
    Route::post('/vendors', [VendorsForSuppliersResourceController::class, 'store']);
    Route::put('/vendors/{uuid}', [VendorsForSuppliersResourceController::class, 'update']);
    Route::delete('/vendors/{uuid}', [VendorsForSuppliersResourceController::class, 'destroy']);
});