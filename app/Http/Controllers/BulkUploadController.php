<?php

namespace App\Http\Controllers;

use App\Exports\FirstTimersTemplateExport;
use App\Imports\FirstTimersImport;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadController extends Controller
{
    /**
     * Display the bulk upload view.
     */
    public function index()
    {
        $user = auth()->user();
        $query = \App\Models\Pcf::query();

        if ($user->hasRole('Admin')) {
            $churchGroupIds = \App\Models\Church::where('leader_contact', $user->contact)
                ->pluck('church_group_id');
            $query->whereIn('church_group_id', $churchGroupIds);
        }

        $pcfs = $query->get();
        return view('bulk-upload.index', compact('pcfs'));
    }

    /**
     * Export the Excel template.
     */
    public function exportTemplate()
    {
        return Excel::download(new FirstTimersTemplateExport, 'first_timers_template.xlsx');
    }

    /**
     * Handle the Excel import.
     */
    public function import(Request $request, \App\Services\ContactCheckService $contactCheckService)
    {
        $user = auth()->user();

        $request->validate([
            'pcf_id' => $user->hasRole('Admin') ? 'nullable|exists:pcfs,id' : 'required|exists:pcfs,id',
            'file' => 'required|mimes:xlsx,xls,csv|max:4096',
        ]);

        // Security check for Admins if pcf_id is provided
        if ($request->pcf_id && $user->hasRole('Admin')) {
            $pcf = \App\Models\Pcf::find($request->pcf_id);
            $myChurchGroupIds = \App\Models\Church::where('leader_contact', $user->contact)
                ->pluck('church_group_id')
                ->toArray();

            if (!in_array($pcf->church_group_id, $myChurchGroupIds)) {
                return redirect()->back()->with('error', 'Unauthorized: You can only upload to PCFs within your church groups.');
            }
        }

        $pcfId = $request->pcf_id;
        $churchId = null;

        if ($user->hasRole('Admin')) {
            $myChurch = \App\Models\Church::where('leader_contact', $user->contact)->first();
            if (!$myChurch) {
                return redirect()->back()->with('error', 'You are not assigned to any church. Please contact the administrator.');
            }
            $churchId = $myChurch->id;
        }

        DB::beginTransaction();
        try {
            $import = new FirstTimersImport($pcfId, $contactCheckService, $churchId);
            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                DB::rollBack();
                return redirect()->back()->with('failures', $import->failures());
            }

            DB::commit();
            return redirect()->back()->with('success', 'First timers imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
