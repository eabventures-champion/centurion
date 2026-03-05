<?php

namespace App\Http\Controllers;

use App\Models\FoundationProgress;
use App\Models\FoundationClass;
use App\Http\Requests\StoreFoundationProgressRequest;
use Illuminate\Http\Request;

class FoundationProgressController extends Controller
{
    /**
     * Display a listing of foundation progress.
     */
    public function index()
    {
        $progress = FoundationProgress::with(['foundationClass', 'firstTimer', 'retainedMember', 'markedBy'])->get();
        return response()->json($progress);
    }

    /**
     * Store a new foundation progress record.
     */
    public function store(StoreFoundationProgressRequest $request)
    {
        $data = $request->validated();
        $data['marked_by'] = auth()->id();

        // Check for existing progress to prevent duplicates if necessary
        $progress = FoundationProgress::updateOrCreate(
            [
                'foundation_class_id' => $data['foundation_class_id'],
                'first_timer_id' => $data['first_timer_id'] ?? null,
                'retained_member_id' => $data['retained_member_id'] ?? null,
            ],
            $data
        );

        return response()->json($progress, 201);
    }

    /**
     * Show details of a progress record.
     */
    public function show(FoundationProgress $foundationProgress)
    {
        return response()->json($foundationProgress->load(['foundationClass', 'firstTimer', 'retainedMember']));
    }
}
