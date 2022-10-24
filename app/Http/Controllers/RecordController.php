<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Record $record)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function edit(Record $record)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $record)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'staffId' => 'required|string',
            'gradeLevel' => 'required|integer',
            'company_id' => 'required|integer',
            'mobile' => 'required',
            'location' => 'required|string',
            'dob' => 'required|date',
            'date_joined' => 'required|date',
            'type' => 'required|string|max:255|in:permanent,contract,secondment,appointment,contractor,support,adhoc',
            'status' => 'required|string|max:255|in:in-service,retired,transfer,remove'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $record = Record::find($record);

        if (! $record) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $record->update([
            'designation' => $request->designation,
            'department_id' => $request->department_id,
            'staffId' => $request->staffId,
            'gradeLevel' => $request->gradeLevel,
            'company_id' => $request->company_id,
            'mobile' => $request->mobile,
            'location' => $request->location,
            'dob' => Carbon::parse($request->dob),
            'date_joined' => Carbon::parse($request->date_joined),
            'type' => $request->type,
            'status' => $request->status
        ]);

        return response()->json([
            'data' => $record,
            'status' => 'success',
            'message' => 'Record has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Record $record)
    {
        //
    }
}
