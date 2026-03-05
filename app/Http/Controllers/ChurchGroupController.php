<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChurchGroupRequest;
use App\Models\ChurchGroup;
use App\Models\ChurchCategory;
use App\Services\ChurchService;
use Illuminate\Http\Request;

class ChurchGroupController extends Controller
{
    protected $churchService;
    protected $contactCheckService;

    public function __construct(ChurchService $churchService, \App\Services\ContactCheckService $contactCheckService)
    {
        $this->churchService = $churchService;
        $this->contactCheckService = $contactCheckService;
    }

    public function index()
    {
        $categories = ChurchCategory::with('churchGroups')->get();
        return view('church-groups.index', compact('categories'));
    }

    public function store(StoreChurchGroupRequest $request)
    {
        $this->churchService->createGroup($request->validated());
        return redirect()->back()->with('success', 'Group created successfully!');
    }

    public function edit(ChurchGroup $churchGroup)
    {
        $categories = ChurchCategory::all();
        return view('church-groups.edit', compact('churchGroup', 'categories'));
    }

    public function update(Request $request, ChurchGroup $churchGroup)
    {
        $validated = $request->validate([
            'church_category_id' => 'required|exists:church_categories,id',
            'group_name' => 'required|string|max:255',
            'pastor_name' => 'required|string|max:255',
            'pastor_contact' => [
                \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                    $category = \App\Models\ChurchCategory::find($request->church_category_id);
                    $isZonalCategory = $category && $category->name === 'ZONAL CHURCH';
                    $isZonalGroupName = strtoupper($request->group_name) === 'ZONAL CHURCH GROUP 1';

                    return !$isZonalCategory && !$isZonalGroupName;
                }),
                'nullable',
                'string',
                'unique:church_groups,pastor_contact,' . $churchGroup->id,
            ],
        ]);

        $churchGroup->update($validated);

        return redirect()->route('church-groups.index')->with('success', 'Group updated successfully!');
    }

    public function checkContact(Request $request)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $contact = $request->query('contact');
        $excludeId = $request->query('exclude_id');
        $excludeType = $request->query('exclude_type');

        \Illuminate\Support\Facades\Log::info('Contact Check Start', [
            'contact' => $contact,
            'exclude_id' => $excludeId,
            'exclude_type' => $excludeType,
            'ip' => $request->ip()
        ]);

        $result = $this->contactCheckService->checkDuplicate($contact, $excludeId, $excludeType);

        \Illuminate\Support\Facades\Log::info('Contact Check End', $result);

        return response()->json($result);
    }

    public function destroy(ChurchGroup $churchGroup)
    {
        $churchGroup->delete();
        return redirect()->back()->with('success', 'Group deleted successfully!');
    }

    public function getPcfs(ChurchGroup $churchGroup)
    {
        return response()->json($churchGroup->pcfs);
    }

    public function getChurches(ChurchGroup $churchGroup)
    {
        return response()->json($churchGroup->churches);
    }
}
