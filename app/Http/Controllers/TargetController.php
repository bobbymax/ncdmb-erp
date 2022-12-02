<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Target;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TargetController extends Controller
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
        $targets = Target::latest()->get();

        if ($targets->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $targets,
            'status' => 'success',
            'message' => 'List of Targets'
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'commitment_id' => 'required|integer',
            'objectives' => 'required|min:5',
            'measure' => 'required|min:5',
            'weight' => 'required|integer',
            'target' => 'required|integer',
            'milestones' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }


        $target = Target::create([
            'commitment_id' => $request->commitment_id,
            'objectives' => $request->objectives,
            'measure' => $request->measure,
            'weight' => $request->weight,
            'target' => $request->target,
        ]);

        if (! $target) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }

        foreach ($request->milestones as $value) {

            $milestone = new Milestone;
            $milestone->description = $value['description'];
            $milestone->percentage_completion = $value['percentage_completion'];
            $milestone->period = $value['period'];
            $milestone->due_date = Carbon::parse($value['due_date']);

            $target->milestones()->save($milestone);

            $timeline = new Timeline;
            $milestone->timeline()->save($timeline);
        }

        return response()->json([
            'data' => $target,
            'status' => 'success',
            'message' => 'Target created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function show(Target $target)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function edit(Target $target)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Target $target)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function destroy(Target $target)
    {
        //
    }
}
