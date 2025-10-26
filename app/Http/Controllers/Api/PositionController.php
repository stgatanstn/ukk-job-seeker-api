<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvailablePosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    // Insert new position (HRD only)
    public function insertPosition(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'HRD') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only HRD can create positions.',
            ], 403);
        }

        $company = $user->company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Please create company profile first.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'position_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'submission_start_date' => 'required|date',
            'submission_end_date' => 'required|date|after:submission_start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $position = AvailablePosition::create([
            'company_id' => $company->id,
            'position_name' => $request->position_name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'submission_start_date' => $request->submission_start_date,
            'submission_end_date' => $request->submission_end_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Position created successfully',
            'data' => $position
        ], 201);
    }

    // Get all positions (from my company for HRD, all active positions for Society)
    public function getPosition(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'HRD') {
            // HRD sees only their company's positions
            $company = $user->company;

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company profile not found.',
                ], 404);
            }

            $positions = $company->availablePositions()->with('company')->get();
        } else {
            // Society sees all active positions
            $today = now()->toDateString();
            $positions = AvailablePosition::with('company')
                ->where('submission_start_date', '<=', $today)
                ->where('submission_end_date', '>=', $today)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $positions
        ], 200);
    }

    // Update position (HRD only)
    public function updatePosition(Request $request, $id)
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

        $position = AvailablePosition::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found or unauthorized.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'position_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'submission_start_date' => 'required|date',
            'submission_end_date' => 'required|date|after:submission_start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $position->update([
            'position_name' => $request->position_name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'submission_start_date' => $request->submission_start_date,
            'submission_end_date' => $request->submission_end_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Position updated successfully',
            'data' => $position
        ], 200);
    }

    // Delete position (HRD only)
    public function deletePosition(Request $request, $id)
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

        $position = AvailablePosition::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found or unauthorized.',
            ], 404);
        }

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Position deleted successfully',
        ], 200);
    }
}