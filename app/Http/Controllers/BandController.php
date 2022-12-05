<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator, Redirect};
use Inertia\Inertia;
use Carbon\Carbon;

class BandController extends Controller
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
            $bands = DB::table("bands")
                ->select(
                    DB::raw("
                        bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                        bands.deleted_at as deleted_at
                    ")
                )
                ->where("bands.deleted_at", "=", null)
                ->get();
        } else {
            $bands = DB::table("bands")
                ->select(
                    DB::raw("
                    bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                    bands.deleted_at as deleted_at
                ")
                )
                ->where("bands.deleted_at", "=", null)
                ->where("bands.name", "like", "%$search%")
                ->get();
        }
        return Inertia::render("Bands/Index", [
            "bands" => $bands,
        ]);
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $trashedBands = DB::table("bands")
            ->select(
                DB::raw("
                    bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                    bands.deleted_at as deleted_at
                ")
            )
            ->where("bands.deleted_at", "!=", null)
            ->get();
        return Inertia::render("Bands/Trashed", [
            "trashed_bands" => $trashedBands,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render("Bands/Create");
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
                "name" => ["required", "string", "max:255"],
                "genre" => ["required", "string", "max:255"],
                "debut" => ["required", "integer", "min:0"],
            ],
            [],
            [
                "name" => "band name",
                "genre" => "band genre",
                "debut" => "band debut",
            ]
        );
        DB::table("bands")->insert([
            "name" => $request->name,
            "genre" => $request->genre,
            "debut" => $request->debut,
        ]);
        return Redirect::route("bands.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $band = DB::table("bands")
            ->select(
                DB::raw("
                bands.id as id, bands.name as name, bands.genre as genre, bands.debut as debut,
                bands.deleted_at as deleted_at
        ")
            )
            ->where("bands.deleted_at", "=", null)
            ->where("bands.id", "=", $id)
            ->get();
        return Inertia::render("Bands/Edit", [
            "band" => $band[0],
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
                "name" => ["required", "string", "max:255"],
                "genre" => ["required", "string", "max:255"],
                "debut" => ["required", "integer", "min:0"],
            ],
            [],
            [
                "name" => "band name",
                "genre" => "band genre",
                "debut" => "band debut",
            ]
        );
        DB::table("bands")
            ->where("id", "=", $id)
            ->update([
                "name" => $request->name,
                "genre" => $request->genre,
                "debut" => $request->debut,
                "updated_at" => Carbon::now(),
            ]);
        return Redirect::route("bands.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("bands")
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
        DB::table("bands")
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
        DB::table("bands")
            ->where("deleted_at", "!=", null)
            ->where("id", "=", $id)
            ->update([
                "deleted_at" => null,
            ]);
        return back();
    }
}