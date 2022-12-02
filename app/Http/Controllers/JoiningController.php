<?php

namespace App\Http\Controllers;

use App\Http\Resources\JoiningResource;
use App\Models\Joining;
use App\Models\Timeline;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JoiningController extends Controller
{

    protected $training;

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
        $joinings = Joining::latest()->get();

        if ($joinings->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => JoiningResource::collection($joinings),
            'status' => 'success',
            'message' => 'List of Trainings'
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
            'training_id' => 'required|integer',
            'qualification_id' => 'required|integer',
            'learning_category_id' => 'required|integer',
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'facilitator' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255|in:virtual,on-premise',
            'type' => 'required|string|max:255|in:nomination,archive',
            'resident' => 'required|string|max:255|in:international,local',
            'status' => 'required|string|max:255|in:registered,ongoing,completed,verified',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        if ($request->training_id < 1) {
            $this->training = Training::create([
                'title' => $request->title,
                'label' => Str::slug($request->title),
            ]);
        } else {
            $this->training = Training::find($request->training_id);
        }

        if (! $this->training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Training not found'
            ], 422);
        }

        $joining = Joining::create([
            'training_id' => $this->training->id,
            'learning_category_id' => $request->learning_category_id,
            'qualification_id' => $request->qualification_id,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'facilitator' => $request->facilitator,
            'location' => $request->location,
            'category' => $request->category,
            'type' => $request->type,
            'resident' => $request->resident,
            'status' => $request->status,
        ]);

        if (! in_array(auth()->user()->id, $joining->staff->pluck('id')->toArray())) {
            $joining->addParticipant(auth()->user());
        }

        $timeline = new Timeline;
        $joining->timeline()->save($timeline);

        return response()->json([
            'data' => new JoiningResource($joining),
            'status' => 'success',
            'message' => 'Joining Instruction Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $joining
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($joining): \Illuminate\Http\JsonResponse
    {
        $joining = Joining::find($joining);

        if (! $joining) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new JoiningResource($joining),
            'status' => 'success',
            'message' => 'Training Joining Instruction Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $joining
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($joining): \Illuminate\Http\JsonResponse
    {
        $joining = Joining::find($joining);

        if (! $joining) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new JoiningResource($joining),
            'status' => 'success',
            'message' => 'Training Joining Instruction Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $joining
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $joining): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|integer',
            'learning_category_id' => 'required|integer',
            'qualification_id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date',
            'facilitator' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255|in:virtual,on-premise',
            'type' => 'required|string|max:255|in:nomination,archive',
            'resident' => 'required|string|max:255|in:international,local',
            'status' => 'required|string|max:255|in:registered,ongoing,completed,verified',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $joining = Joining::find($joining);

        if (! $joining) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $joining->update([
            'training_id' => $request->training_id,
            'qualification_id' => $request->qualification_id,
            'learning_category_id' => $request->learning_category_id,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'facilitator' => $request->facilitator,
            'location' => $request->location,
            'category' => $request->category,
            'type' => $request->type,
            'resident' => $request->resident,
            'status' => $request->status,
        ]);

        return response()->json([
            'data' => new JoiningResource($joining),
            'status' => 'success',
            'message' => 'Joining Instruction Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $joining
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($joining): \Illuminate\Http\JsonResponse
    {
        $joining = Joining::find($joining);

        if (! $joining) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $joining;
        $joining->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Joining Instruction Deleted Successfully!'
        ], 200);
    }
}
