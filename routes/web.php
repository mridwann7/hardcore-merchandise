<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{BandController, AccessoryController, ShirtController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return Inertia::render("Welcome", [
        "canLogin" => Route::has("login"),
        "canRegister" => Route::has("register"),
        "laravelVersion" => Application::VERSION,
        "phpVersion" => PHP_VERSION,
    ]);
});

Route::middleware([
    "auth:sanctum",
    config("jetstream.auth_session"),
    "verified",
])->group(function () {
    // Bands Endpoint
    Route::group(
        [
            "prefix" => "bands",
            "as" => "bands.",
        ],
        function () {
            Route::get("/", [BandController::class, "index"])->name("index");
            Route::get("trashed", [BandController::class, "trashed"])->name(
                "trashed"
            );
            Route::get("create", [BandController::class, "create"])->name(
                "create"
            );
            Route::post("store", [BandController::class, "store"])->name(
                "store"
            );
            Route::get("{id}/edit", [BandController::class, "edit"])->name(
                "edit"
            );
            Route::put("{id}/update", [BandController::class, "update"])->name(
                "update"
            );
            Route::get("{id}/destroy", [
                BandController::class,
                "destroy",
            ])->name("destroy");
            Route::get("{id}/destroy-permanent", [
                BandController::class,
                "destroy_permanent",
            ])->name("destroy_permanent");
            Route::get("{id}/restore", [
                BandController::class,
                "restore",
            ])->name("restore");
        }
    );

    // Accessories Endpoint
    Route::group(
        [
            "prefix" => "accessories",
            "as" => "accessories.",
        ],
        function () {
            Route::get("/", [AccessoryController::class, "index"])->name(
                "index"
            );
            Route::get("trashed", [
                AccessoryController::class,
                "trashed",
            ])->name("trashed");
            Route::get("create", [AccessoryController::class, "create"])->name(
                "create"
            );
            Route::post("store", [AccessoryController::class, "store"])->name(
                "store"
            );
            Route::get("{id}/edit", [AccessoryController::class, "edit"])->name(
                "edit"
            );
            Route::put("{id}/update", [
                AccessoryController::class,
                "update",
            ])->name("update");
            Route::get("{id}/destroy", [
                AccessoryController::class,
                "destroy",
            ])->name("destroy");
            Route::get("{id}/destroy-permanent", [
                AccessoryController::class,
                "destroy_permanent",
            ])->name("destroy_permanent");
            Route::get("{id}/restore", [
                AccessoryController::class,
                "restore",
            ])->name("restore");
        }
    );

    // Shirts Endpoint
    Route::group(
        [
            "prefix" => "shirts",
            "as" => "shirts.",
        ],
        function () {
            Route::get("/", [ShirtController::class, "index"])->name("index");
            Route::get("trashed", [ShirtController::class, "trashed"])->name(
                "trashed"
            );
            Route::get("create", [ShirtController::class, "create"])->name(
                "create"
            );
            Route::post("store", [ShirtController::class, "store"])->name(
                "store"
            );
            Route::get("{id}/edit", [ShirtController::class, "edit"])->name(
                "edit"
            );
            Route::put("{id}/update", [ShirtController::class, "update"])->name(
                "update"
            );
            Route::get("{id}/destroy", [
                ShirtController::class,
                "destroy",
            ])->name("destroy");
            Route::get("{id}/destroy-permanent", [
                ShirtController::class,
                "destroy_permanent",
            ])->name("destroy_permanent");
            Route::get("{id}/restore", [
                ShirtController::class,
                "restore",
            ])->name("restore");
        }
    );
});