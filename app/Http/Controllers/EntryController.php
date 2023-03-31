<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntryController extends Controller
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
        $entries = Entry::where('isActive', true)->latest()->get();

        if ($entries->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found'
            ], 200);
        }

        return response()->json([
            'data' => $entries,
            'status' => 'success',
            'message' => 'List of Entries!!'
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
        $validator = Validator::make($request->all(), [
            'track_id' => 'required|integer',
            'department_id' => 'required|integer',
            'stage_id' => 'required|integer',
            'user_id' => 'required|integer',
            'type' => 'required|string|max:255|in:inflow,outflow',
            'previous_stage' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $track = Track::find($request->track_id);
        $previous = Entry::find($request->previous_stage);

        if (! $track || ! $previous) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID selected!!'
            ], 422);
        }

        $entry = new Entry;
        $entry->user_id = $request->user_id;
        $entry->department_id = $request->department_id;
        $entry->stage_id = $request->stage_id;
        $entry->type = $request->type;
        $entry->isActive = true;

        if ($track->entries()->save($entry)) {
            $previous->isActive = false;
            $previous->save();
        }

        return response()->json([
            'data' => $entry,
            'status' => 'success',
            'message' => 'Entry has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $entry
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entry): \Illuminate\Http\JsonResponse
    {
        $entry = Entry::find($entry);

        if (! $entry) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID selected!!'
            ], 422);
        }

        return response()->json([
            'data' => $entry,
            'status' => 'success',
            'message' => 'Entry details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $entry
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($entry): \Illuminate\Http\JsonResponse
    {
        $entry = Entry::find($entry);

        if (! $entry) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID selected!!'
            ], 422);
        }

        return response()->json([
            'data' => $entry,
            'status' => 'success',
            'message' => 'Entry details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $entry
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $entry): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'stage_id' => 'required|integer',
            'type' => 'required|string|max:255|in:inflow,outflow',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $entry = Entry::find($entry);

        if (! $entry) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID selected!!'
            ], 422);
        }

        $entry->update([
            'department_id' => $request->department_id,
            'stage_id' => $request->stage_id,
            'type' => $request->type,
        ]);

        return response()->json([
            'data' => $entry,
            'status' => 'success',
            'message' => 'Entry has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $entry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($entry): \Illuminate\Http\JsonResponse
    {
        $entry = Entry::find($entry);

        if (! $entry) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID selected!!'
            ], 422);
        }

        $old = $entry;
        $entry->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Entry has been deleted successfully!!'
        ], 200);
    }
}
