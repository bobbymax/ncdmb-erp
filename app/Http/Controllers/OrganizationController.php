<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
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
        $organizations = Organization::latest()->get();

        if ($organizations->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $organizations,
            'status' => 'success',
            'message' => 'List of Organizations'
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
            'registration_no' => 'required|string|max:255|unique:companies',
            'tin_no' => 'required|string|max:255|unique:companies',
            'service_code' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'contact_email' => 'required|string|email|max:255',
            'no_of_staff' => 'required|integer',
            'mobile' => 'required|string|unique:companies',
            'contact_mobile' => 'required|string',
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'type' => 'required|string|in:contractor,owner',
            'category' => 'required|string|in:nigeria-owned,nigeria-company-owned-by-foreign-company,foreign-owned,government-ministry,government-parastatal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $organization = Organization::create([
            'service_code' => $request->service_code,
            'no_of_staff' => $request->no_of_staff,
            'registration_no' => $request->registration_no,
            'tin_no' => $request->tin_no,
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'type' => $request->type,
            'category' => $request->category
        ]);

        if (! $organization) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Organization was not created!!'
            ], 500);
        }

        $pass = strtolower($request->firstname) . "." . strtolower($request->surname);

        $member = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'surname' => $request->surname,
            'email' => $request->contact_email,
            'password' => Hash::make($pass),
        ]);

        if (! $member) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Organization\'s Member was not created!!'
            ], 500);
        }

        Record::create([
            'company_id' => $organization->id,
            'staffId' => time() . Str::random(5),
            'type' => 'contractor',
            'mobile' => $request->contact_mobile,
            'designation' => 'contractor'
        ]);

        return response()->json([
            'data' => $organization,
            'status' => 'success',
            'message' => 'Contractor Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($organization)
    {
        $organization = Organization::find($organization);

        if (! $organization) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $organization,
            'status' => 'success',
            'message' => 'Company Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($organization)
    {
        $organization = Organization::find($organization);

        if (! $organization) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $organization,
            'status' => 'success',
            'message' => 'Company Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $organization)
    {
        $validator = Validator::make($request->all(), [
            'registration_no' => 'required|string|max:255|unique:companies',
            'tin_no' => 'required|string|max:255|unique:companies',
            'service_code' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'no_of_staff' => 'required|integer',
            'mobile' => 'required|string|unique:companies',
            'type' => 'required|string|in:contractor,owner',
            'category' => 'required|string|in:nigeria-owned,nigeria-company-owned-by-foreign-company,foreign-owned,government-ministry,government-parastatal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $organization = Organization::find($organization);

        if (! $organization) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $organization->update([
            'service_code' => $request->service_code,
            'no_of_staff' => $request->no_of_staff,
            'registration_no' => $request->registration_no,
            'tin_no' => $request->tin_no,
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'type' => $request->type,
            'category' => $request->category
        ]);

        return response()->json([
            'data' => $organization,
            'status' => 'success',
            'message' => 'Organization has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($organization)
    {
        $organization = Organization::find($organization);

        if (! $organization) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $organization;
        $organization->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Contractor record deleted Successfully!'
        ], 200);
    }
}
