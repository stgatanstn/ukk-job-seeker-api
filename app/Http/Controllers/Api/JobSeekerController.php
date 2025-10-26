<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Society;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobSeekerController extends Controller
{
    // Get Job Seeker Profile
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only job seekers can access this endpoint.',
            ], 403);
        }

        $jobSeeker = $user->jobSeeker()->with('portfolios')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $jobSeeker,
            ]
        ], 200);
    }

    // Update Job Seeker Profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only job seekers can access this endpoint.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'bio' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobSeeker = $user->jobSeeker;

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            // Delete old CV if exists
            if ($jobSeeker->cv_file) {
                Storage::disk('public')->delete($jobSeeker->cv_file);
            }
            
            $cvPath = $request->file('cv_file')->store('cv_files', 'public');
            $jobSeeker->cv_file = $cvPath;
        }

        $jobSeeker->phone = $request->phone ?? $jobSeeker->phone;
        $jobSeeker->address = $request->address ?? $jobSeeker->address;
        $jobSeeker->birth_date = $request->birth_date ?? $jobSeeker->birth_date;
        $jobSeeker->bio = $request->bio ?? $jobSeeker->bio;
        $jobSeeker->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $jobSeeker
        ], 200);
    }

    // Get All Portfolios
    public function getPortfolios(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $portfolios = $user->jobSeeker->portfolios;

        return response()->json([
            'success' => true,
            'data' => $portfolios
        ], 200);
    }

    // Create Portfolio
    public function createPortfolio(Request $request)
{
    try {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('portfolio_images', 'public');
        }

        $portfolio = Portofolio::create([
            'society_id' => $user->society->id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio created successfully',
            'data' => $portfolio
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    // Update Portfolio
    public function updatePortfolio(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $portfolio = Portofolio::where('id', $id)
            ->where('society_id', $user->society->id)
            ->first();

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($portfolio->image) {
                Storage::disk('public')->delete($portfolio->image);
            }
            
            $imagePath = $request->file('image')->store('portfolio_images', 'public');
            $portfolio->image = $imagePath;
        }

        $portfolio->title = $request->title;
        $portfolio->description = $request->description;
        $portfolio->link = $request->link;
        $portfolio->save();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio updated successfully',
            'data' => $portfolio
        ], 200);
    }

    // Delete Portfolio
    public function deletePortfolio(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->role !== 'jobseeker') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $portfolio = Portofolio::where('id', $id)
            ->where('society_id', $user->society->id)
            ->first();

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found',
            ], 404);
        }

        // Delete image if exists
        if ($portfolio->image) {
            Storage::disk('public')->delete($portfolio->image);
        }

        $portfolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio deleted successfully',
        ], 200);
    }
}