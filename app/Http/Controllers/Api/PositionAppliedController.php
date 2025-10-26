<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PositionApplied;
use App\Models\AvailablePosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionAppliedController extends Controller
{
    // Apply for position (Society only)
    public function apply(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Society') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Society can apply.',
            ], 403);
        }

        $society = $user->society;

        if (!$society) {
            return response()->json([
                'success' => false,
                'message' => 'Please create society profile first.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'available_position_id' => 'required|exists:available_position,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if position is still open
        $position = AvailablePosition::find($request->available_position_id);
        $today = now()->toDateString();

        if ($today < $position->submission_start_date || $today > $position->submission_end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Position application period is not active.',
            ], 400);
        }

        // Check if already applied
        $exists = PositionApplied::where('available_position_id', $request->available_position_id)
            ->where('society_id', $society->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this position.',
            ], 400);
        }

        $application = PositionApplied::create([
            'available_position_id' => $request->available_position_id,
            'society_id' => $society->id,
            'apply_date' => now()->toDateString(),
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);
    }

    // Get applications by position (HRD only)
    public function readByAvailablePosition(Request $request, $id)
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

        // Check if position belongs to this company
        $position = AvailablePosition::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found or unauthorized.',
            ], 404);
        }

        $applications = PositionApplied::with('society.user', 'society.portofolios')
            ->where('available_position_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ], 200);
    }

    // Get my applications (Society only)
    public function readMyApplications(Request $request)
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

        $applications = PositionApplied::with('availablePosition.company')
            ->where('society_id', $society->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ], 200);
    }

    // Update application status (HRD only)
    public function updateStatus(Request $request, $id)
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
            'status' => 'required|in:PENDING,ACCEPTED,REJECTED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $application = PositionApplied::with('availablePosition')
            ->find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found.',
            ], 404);
        }

        // Check if position belongs to this company
        if ($application->availablePosition->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this application.',
            ], 403);
        }

        $application->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => $application
        ], 200);
    }

    // Delete application
    public function delete(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role === 'Society') {
            $society = $user->society;

            if (!$society) {
                return response()->json([
                    'success' => false,
                    'message' => 'Society profile not found.',
                ], 404);
            }

            $application = PositionApplied::where('id', $id)
                ->where('society_id', $society->id)
                ->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found or unauthorized.',
            ], 404);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully',
        ], 200);
    }
}