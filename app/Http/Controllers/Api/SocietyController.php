<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocietyController extends Controller
{
    // Create society profile
    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Society') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Society can create profile.',
            ], 403);
        }

        if ($user->society) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a society profile.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $society = Society::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Society profile created successfully',
            'data' => $society
        ], 201);
    }

    // Read all societies
    public function read()
    {
        $societies = Society::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $societies
        ], 200);
    }

    // Read society by ID
    public function readById($id)
    {
        $society = Society::with('user', 'portofolios')->find($id);

        if (!$society) {
            return response()->json([
                'success' => false,
                'message' => 'Society not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $society
        ], 200);
    }

    // Read my profile
    public function readMe(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Society') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $society = $user->society()->with('portofolios')->first();

        if (!$society) {
            return response()->json([
                'success' => false,
                'message' => 'Society profile not found. Please create one first.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $society
        ], 200);
    }

    // Update my profile
    public function updateMe(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Society') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $society = $user->society;

        if (!$society) {
            return response()->json([
                'success' => false,
                'message' => 'Society profile not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $society->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Society profile updated successfully',
            'data' => $society
        ], 200);
    }
}