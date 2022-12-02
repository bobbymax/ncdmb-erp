<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequisitionResource;
use App\Models\Item;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequisitionController extends Controller
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
        $requisitions = Requisition::latest()->get();

        if ($requisitions->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => RequisitionResource::collection($requisitions),
            'status' => 'success',
            'message' => 'List of Requisitions'
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
            'department_id' => 'required|integer',
            'no_of_items' => 'required|integer',
            'items' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $requisition = new Requisition;
        $requisition->user_id = auth()->user()->id;
        $requisition->department_id = $request->department_id;
        $requisition->no_of_items = $request->no_of_items;
        $requisition->save();

        foreach($request->items as $value) {
            $item = new Item;
            $item->product_id = $value['product_id'];
            $item->quantity_expected = $value['quantity'];
            $item->urgency = $value['urgency'];
            $requisition->items()->save($item);
        }

        return response()->json([
            'data' => new RequisitionResource($requisition),
            'status' => 'success',
            'message' => 'Requisition has been registered successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($requisition): \Illuminate\Http\JsonResponse
    {
        $requisition = Requisition::find($requisition);

        if (! $requisition) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => new RequisitionResource($requisition),
            'status' => 'success',
            'message' => 'Requisition details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($requisition): \Illuminate\Http\JsonResponse
    {
        $requisition = Requisition::find($requisition);

        if (! $requisition) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => new RequisitionResource($requisition),
            'status' => 'success',
            'message' => 'Requisition details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $requisition): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255|in:pending,department-approval,approved,denied',
            'items' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $requisition = Requisition::find($requisition);

        if (! $requisition) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $requisition->update([
            'approving_officer_id' => auth()->user()->id,
            'status' => $request->status
        ]);

        $requisition->items()->delete();

        foreach ($request->items as $value) {
            $item = new Item;
            $item->product_id = $value['product_id'];
            $item->quantity_expected = $value['quantity_expected'];
            $item->quantity_received = $value['quantity_received'];
            $item->urgency = $value['urgency'];
            $requisition->items()->save($item);
        }

        return response()->json([
            'data' => new RequisitionResource($requisition),
            'status' => 'success',
            'message' => 'Requisition has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $requisition
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($requisition): \Illuminate\Http\JsonResponse
    {
        $requisition = Requisition::find($requisition);

        if (! $requisition) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $old = $requisition;
        $requisition->items()->delete();
        $requisition->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Requisition has been deleted successfully!!'
        ], 200);
    }
}
