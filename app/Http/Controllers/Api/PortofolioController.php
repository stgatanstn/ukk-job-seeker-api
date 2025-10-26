<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PortofolioController extends Controller
{
    // Insert portfolio (Society only)
    public function insertportofolio(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Society') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Society can create portfolio.',
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
            'skill' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('portofolio_files', 'public');
        }

        $portofolio = Portofolio::create([
            'society_id' => $society->id,
            'skill' => $request->skill,
            'description' => $request->description,
            'file' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio created successfully',
            'data' => $portofolio
        ], 201);
    }

    // Get my portfolios
    public function myPortofolio(Request $request)
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

        $portofolios = $society->portofolios;

        return response()->json([
            'success' => true,
            'data' => $portofolios
        ], 200);
    }

    // Update portfolio
    public function update(Request $request, $id)
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

        $portofolio = Portofolio::where('id', $id)
            ->where('society_id', $society->id)
            ->first();

        if (!$portofolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found or unauthorized.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'skill' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($portofolio->file) {
                Storage::disk('public')->delete($portofolio->file);
            }

            $filePath = $request->file('file')->store('portofolio_files', 'public');
            $portofolio->file = $filePath;
        }

        $portofolio->skill = $request->skill;
        $portofolio->description = $request->description;
        $portofolio->save();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio updated successfully',
            'data' => $portofolio
        ], 200);
    }

    // Delete portfolio
    public function destroy(Request $request, $id)
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

        $portofolio = Portofolio::where('id', $id)
            ->where('society_id', $society->id)
            ->first();

        if (!$portofolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found or unauthorized.',
            ], 404);
        }

        // Delete file if exists
        if ($portofolio->file) {
            Storage::disk('public')->delete($portofolio->file);
        }

        $portofolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio deleted successfully',
        ], 200);
    }
}