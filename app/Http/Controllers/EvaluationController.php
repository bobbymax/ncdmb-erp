<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
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
        $evaluation = Evaluation::latest()->get();

        if ($evaluation->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $evaluation,
            'status' => 'success',
            'message' => 'List of Service Categories'
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
            'bid_id' => 'required|integer',
            'score' => 'required|integer',
            'marks' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $evaluation = Evaluation::create([
            'name' => $request->name,
            'score' => $request->score,
        ]);

        if (! $evaluation) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }

        foreach($request->marks as $score) {
            Score::create([
                'evaluation_id' => $evaluation->id,
                'requirement' => $score['requirement'],
                'pattern' => $score['pattern'],
                'value' => $score['value'],
                'sighting' => $score['sighting']
            ]);
        }

        return response()->json([
            'data' => $evaluation,
            'status' => 'success',
            'message' => 'Evaluation has been computed successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $evaluation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($evaluation)
    {
        $evaluation = Evaluation::find($evaluation);
        if (! $evaluation) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $evaluation,
            'status' => 'success',
            'message' => 'Evaluation details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $evaluation
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($evaluation)
    {
        $evaluation = Evaluation::find($evaluation);
        if (! $evaluation) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $evaluation,
            'status' => 'success',
            'message' => 'Evaluation details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $evaluation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($evaluation)
    {
        $evaluation = Evaluation::find($evaluation);
        if (! $evaluation) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $evaluation;
        $evaluation->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Evaluation deleted successfully!!'
        ], 200);
    }
}
