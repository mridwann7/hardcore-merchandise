<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Redirect};
use Inertia\Inertia;
use Carbon\Carbon;

class AccessoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query("search");
        if (empty($search)) {
            $accessories = DB::table("accessories")
                ->select(
                    DB::raw("
                        accessories.id as id, accessories.name as name, accessories.price as price, accessories.stock as stock,
                        accessories.deleted_at as deleted_at, bands.id as accessory_band_id, bands.name as accessory_band_name
                    ")
                )
                ->where("accessories.deleted_at", "=", null)
                ->join("bands", "bands.id", "=", "accessories.band_id")
                ->get();
        } else {
            $accessories = DB::table("accessories")
                ->select(
                    DB::raw("
                        accessories.id as id, accessories.name as name, accessories.price as price, accessories.stock as stock,
                        accessories.deleted_at as deleted_at, bands.id as accessory_band_id, bands.name as accessory_band_name
                    ")
                )
                ->where("accessories.deleted_at", "=", null)
                ->where("accessories.name", "like", "%$search%")
                ->join("bands", "bands.id", "=", "accessories.band_id")
                ->get();
        }
        return Inertia::render("Accessories/Index", [
            "accessories" => $accessories,
        ]);
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $trashedAccessories = DB::table("accessories")
            ->select(
                DB::raw("
                    accessories.id as id, accessories.name as name, accessories.price as price, accessories.stock as stock,
                    accessories.deleted_at as deleted_at, bands.id as accessory_band_id, bands.name as accessory_band_name
                ")
            )
            ->where("accessories.deleted_at", "!=", null)
            ->join("bands", "bands.id", "=", "accessories.band_id")
            ->get();
        return Inertia::render("Accessories/Trashed", [
            "trashed_accessories" => $trashedAccessories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bands = DB::table("bands")
            ->select(
                DB::raw("
                    bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                    bands.deleted_at as deleted_at
                ")
            )
            ->where("bands.deleted_at", "=", null)
            ->get();
        return Inertia::render("Accessories/Create", [
            "bands" => $bands,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::validate(
            $request->all(),
            [
                "band" => ["required", "integer", "exists:bands,id"],
                "name" => ["required", "string", "max:255"],
                "price" => ["required", "integer", "min:0"],
                "stock" => ["required", "integer", "min:0"],
            ],
            [],
            [
                "band" => "accessory band",
                "name" => "accessory name",
                "price" => "accessory genre",
                "stock" => "accessory debut",
            ]
        );
        DB::table("accessories")->insert([
            "band_id" => $request->band,
            "name" => $request->name,
            "price" => $request->price,
            "stock" => $request->stock,
        ]);
        return Redirect::route("accessories.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bands = DB::table("bands")
            ->select(
                DB::raw("
                    bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                    bands.deleted_at as deleted_at
                ")
            )
            ->where("bands.deleted_at", "=", null)
            ->get();
        $accessory = DB::table("accessories")
            ->select(
                DB::raw("
                    accessories.id as id, accessories.name as name, accessories.price as price, accessories.stock as stock,
                    accessories.deleted_at as deleted_at, bands.id as accessory_band_id, bands.name as accessory_band_name
                ")
            )
            ->join("bands", "bands.id", "=", "accessories.band_id")
            ->where("accessories.deleted_at", "=", null)
            ->where("accessories.id", "=", $id)
            ->get();
        return Inertia::render("Accessories/Edit", [
            "bands" => $bands,
            "accessory" => $accessory[0],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::validate(
            $request->all(),
            [
                "band" => ["required", "integer", "exists:bands,id"],
                "name" => ["required", "string", "max:255"],
                "price" => ["required", "integer", "min:0"],
                "stock" => ["required", "integer", "min:0"],
            ],
            [],
            [
                "band" => "accessory band",
                "name" => "accessory name",
                "price" => "accessory genre",
                "stock" => "accessory debut",
            ]
        );
        DB::table("accessories")
            ->where("id", "=", $id)
            ->update([
                "band_id" => $request->band,
                "name" => $request->name,
                "price" => $request->price,
                "stock" => $request->stock,
                "updated_at" => Carbon::now(),
            ]);
        return Redirect::route("accessories.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("accessories")
            ->where("id", "=", $id)
            ->update([
                "deleted_at" => Carbon::now(),
            ]);
        return back();
    }

    /**
     * Permanently remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_permanent($id)
    {
        DB::table("accessories")
            ->where("id", "=", $id)
            ->delete();
        return back();
    }

    /**
     * Restore the specified trashed resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        DB::table("accessories")
            ->where("deleted_at", "!=", null)
            ->where("id", "=", $id)
            ->update([
                "deleted_at" => null,
            ]);
        return back();
    }
}