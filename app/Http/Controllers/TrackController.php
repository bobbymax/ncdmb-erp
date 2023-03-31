<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrackResource;
use App\Models\Entry;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $tracks = Track::latest()->get();

        if ($tracks->count() < 1) {
            return response()->json([
                'data' => [],
                'message' => 'No Data Found!!',
                'status' => 'info'
            ], 200);
        }

        return response()->json([
            'data' => TrackResource::collection($tracks),
            'status' => 'success',
            'message' => 'List of Tracks'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function edit(Track $track)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $track
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $track): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'stage_id' => 'required|integer',
            'entry_id' => 'required|integer',
            'user_id' => 'required|integer',
            'type' => 'required|string|max:255|in:inflow,outflow',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $track = Track::find($track);
        $oldEntry = Entry::find($request->entry_id);

        if (! $track || ! $oldEntry) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong token ID entered!!'
            ], 422);
        }

        $track->update([
            'department_id' => $request->department_id,
            'stage_id' => $request->stage_id,
            'state' => $request->type,
        ]);

        $oldEntry->isActive = false;
        $oldEntry->save();

        $entry = new Entry;
        $entry->department_id = $request->department_id;
        $entry->user_id = $request->user_id;
        $entry->stage_id = $request->stage_id;
        $entry->type = $request->type;
        $entry->isActive = true;
        $track->entries()->save($entry);

        return response()->json([
            'data' => new TrackResource($track),
            'status' => 'success',
            'message' => 'Track Process for this batch has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track)
    {
        //
    }
}
