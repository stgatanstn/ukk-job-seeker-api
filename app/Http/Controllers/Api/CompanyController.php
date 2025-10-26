<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    // Create company (first time)
    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'HRD') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only HRD can create company.',
            ], 403);
        }

        // Check if already has company
        if ($user->company) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a company profile.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company = Company::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    // Read all companies
    public function read()
    {
        $companies = Company::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $companies
        ], 200);
    }

    // Read company by ID
    public function readById($id)
    {
        $company = Company::with('user', 'availablePositions')->find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    // Read my company
    public function readMe(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'HRD') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $company = $user->company()->with('availablePositions')->first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found. Please create one first.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    // Update my company
    public function updateMe(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'HRD') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $company = $user->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $company->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ], 200);
    }
}