<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Redirect};
use Inertia\Inertia;
use Carbon\Carbon;

class ShirtController extends Controller
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
            $shirts = DB::table("shirts")
                ->select(
                    DB::raw("
                        shirts.id as id, shirts.name as name, shirts.price as price, shirts.stock as stock,
                        shirts.deleted_at as deleted_at, bands.id as band_id, bands.name as band_name
                    ")
                )
                ->where("shirts.deleted_at", "=", null)
                ->join("bands", "bands.id", "=", "shirts.band_id")
                ->get();
        } else {
            $shirts = DB::table("shirts")
                ->select(
                    DB::raw("
                        shirts.id as id, shirts.name as name, shirts.price as price, shirts.stock as stock,
                        shirts.deleted_at as deleted_at, bands.id as band_id, bands.name as band_name
                    ")
                )
                ->where("shirts.deleted_at", "=", null)
                ->where("shirts.name", "like", "%$search%")
                ->join("bands", "bands.id", "=", "shirts.band_id")
                ->get();
        }
        return Inertia::render("Shirts/Index", [
            "shirts" => $shirts,
        ]);
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $trashedShirts = DB::table("shirts")
            ->select(
                DB::raw("
                    shirts.id as id, shirts.name as name, shirts.price as price, shirts.stock as stock,
                    shirts.deleted_at as deleted_at, bands.id as band_id, bands.name as band_name
                ")
            )
            ->where("shirts.deleted_at", "!=", null)
            ->join("bands", "bands.id", "=", "shirts.band_id")
            ->get();
        return Inertia::render("Shirts/Trashed", [
            "trashed_shirts" => $trashedShirts,
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
        return Inertia::render("Shirts/Create", [
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
                "band" => "shirt band",
                "name" => "shirt name",
                "price" => "shirt genre",
                "stock" => "shirt debut",
            ]
        );
        DB::table("shirts")->insert([
            "band_id" => $request->band,
            "name" => $request->name,
            "price" => $request->price,
            "stock" => $request->stock,
        ]);
        return Redirect::route("shirts.index");
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
        $shirt = DB::table("shirts")
            ->select(
                DB::raw("
                    shirts.id as id, shirts.name as name, shirts.genre as genre, shirts.debut as debut,
                    shirts.deleted_at as deleted_at
                ")
            )
            ->where("shirts.deleted_at", "=", null)
            ->where("shirts.id", "=", $id)
            ->get();
        return Inertia::render("Shirts/Edit", [
            "bands" => $bands,
            "shirt" => $shirt[0],
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
                "band" => "shirt band",
                "name" => "shirt name",
                "price" => "shirt genre",
                "stock" => "shirt debut",
            ]
        );
        DB::table("shirts")
            ->where("id", "=", $id)
            ->update([
                "band_id" => $request->band,
                "name" => $request->name,
                "price" => $request->price,
                "stock" => $request->stock,
                "updated_at" => Carbon::now(),
            ]);
        return Redirect::route("shirts.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("shirts")
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
        DB::table("shirts")
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
        DB::table("shirts")
            ->where("deleted_at", "!=", null)
            ->where("id", "=", $id)
            ->update([
                "deleted_at" => null,
            ]);
        return back();
    }
}