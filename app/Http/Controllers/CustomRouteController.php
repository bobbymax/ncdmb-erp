<?php

namespace App\Http\Controllers;

use App\Http\Resources\JoiningResource;
use App\Models\Joining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomRouteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * @param Request $request
     * @param $training
     * @return \Illuminate\Http\JsonResponse
     * @description Verifies Trainings by HR (Learning and Development)
     */
    public function verifyTraining(Request $request, $training): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'attended' => 'required',
            'isArchived' => 'required',
            'status' => 'required|string|max:255|in:registered,ongoing,completed,verified',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $joining = Joining::find($training);

        if (! $training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $joining->update([
            'attended' => $request->attended,
            'isArchived' => $request->isArchived,
            'status' => $request->status,
        ]);

        return response()->json([
            'data' => new JoiningResource($joining),
            'status' => 'success',
            'message' => 'Joining instruction has been verified and archived successfully!'
        ], 200);
    }
}
