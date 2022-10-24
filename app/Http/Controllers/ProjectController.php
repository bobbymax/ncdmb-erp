<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
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
    public function index()
    {
        $projects = Project::latest()->get();

        if ($projects->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $projects,
            'status' => 'success',
            'message' => 'Projects Lists'
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_category_id' => 'required|integer',
            'procurement_method_id' => 'required|integer',
            'department_id' => 'required|integer',
            'code' => 'required|string|unique:projects',
            'lot_number' => 'required|string|unique:projects',
            'location' => 'required|string',
            'title' => 'required',
            'boq' => 'required',
            'measure' => 'required|string|in:days,weeks,months,years',
            'period' => 'required|integer',
            'year' => 'required|integer',
            'threshold' => 'required|string',
            'stage' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $project = Project::create([
            'service_category_id' => $request->service_category_id,
            'procurement_method_id' => $request->procurement_method_id,
            'department_id' => $request->department_id,
            'code' => $request->code,
            'lot_number' => $request->lot_number,
            'location' => $request->location,
            'period' => $request->period,
            'measure' => $request->measure,
            'coordinates' => $request->coordinates,
            'title' => $request->title,
            'boq' => $request->boq ?? 0,
            'year' => $request->year,
            'threshold' => $request->threshold,
            'stage' => $request->stage,
            'champion' => $request->champion,
        ]);

        if (! $project) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong!!'
            ], 500);
        }

        $timeline = new Timeline;
        $project->timeline()->save($timeline);

        return response()->json([
            'data' => $project,
            'status' => 'success',
            'message' => 'Project have been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($project)
    {
        $project = Project::find($project);

        if (! $project) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $project,
            'status' => 'success',
            'message' => 'Project details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($project)
    {
        $project = Project::find($project);

        if (! $project) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $project,
            'status' => 'success',
            'message' => 'Project details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $project)
    {
        $validator = Validator::make($request->all(), [
            'service_category_id' => 'required|integer',
            'procurement_method_id' => 'required|integer',
            'department_id' => 'required|integer',
            'location' => 'required|string',
            'title' => 'required',
            'boq' => 'required',
            'measure' => 'required|string|in:days,weeks,months,years',
            'period' => 'required|integer',
            'year' => 'required|integer',
            'threshold' => 'required|string',
            'stage' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $project = Project::find($project);

        if (! $project) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $project->update([
            'service_category_id' => $request->service_category_id,
            'procurement_method_id' => $request->procurement_method_id,
            'department_id' => $request->department_id,
            'location' => $request->location,
            'period' => $request->period,
            'measure' => $request->measure,
            'coordinates' => $request->coordinates,
            'title' => $request->title,
            'boq' => $request->boq ?? 0,
            'year' => $request->year,
            'threshold' => $request->threshold,
            'stage' => $request->stage,
            'champion' => $request->champion,
        ]);

        return response()->json([
            'data' => $project,
            'status' => 'success',
            'message' => 'Project has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($project)
    {
        $project = Project::find($project);

        if (! $project) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $project;
        $project->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Project Details have been deleted successfully!!'
        ], 200);
    }
}
