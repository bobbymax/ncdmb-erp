<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public $addressable;

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
        $addresses = Address::latest()->get();

        if ($addresses->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $addresses,
            'status' => 'success',
            'message' => 'Addresses List'
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
            'street_one' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'type' => 'required|string|in:hq,branch',
            'addressable' => 'required|string|in:staff,organization',
            'addressable_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $this->addressable = $this->getAddressable($request->addressable, $request->addressable_id);

        $address = new Address;
        $address->flat_no = $request->flat_no;
        $address->street_one = $request->street_one;
        $address->street_two = $request->street_two;
        $address->zipcode = $request->zipcode;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->type = $request->type;

        $this->addressable->addresses()->save($address);

        return response()->json([
            'data' => $address,
            'status' => 'success',
            'message' => 'Address Added Successfully!'
        ], 201);
    }

    protected function getAddressable($ent, $id)
    {
        switch ($ent) {
            case "company":
                return Organization::find($id);
                break;

            default:
                return User::find($id);
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($address)
    {
        $address = Address::find($address);

        if (! $address) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $address,
            'status' => 'success',
            'message' => 'Address Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($address)
    {
        $address = Address::find($address);

        if (! $address) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $address,
            'status' => 'success',
            'message' => 'Address Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $address)
    {
        $validator = Validator::make($request->all(), [
            'street_one' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'type' => 'required|string|in:hq,branch'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $address = Address::find($address);

        if (! $address) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $address->update([
            'flat_no' => $request->flat_no,
            'street_one' => $request->street_one,
            'street_two' => $request->street_two,
            'zipcode' => $request->zipcode,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'type' => $request->type
        ]);

        return response()->json([
            'data' => $address,
            'status' => 'success',
            'message' => 'Address Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($address)
    {
        $address = Address::find($address);

        if (! $address) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $address;
        $address->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Address deleted Successfully!'
        ], 200);
    }
}
