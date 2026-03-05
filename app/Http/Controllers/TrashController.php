<?php

namespace App\Http\Controllers;

use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\Church;
use App\Models\Pcf;
use App\Models\User;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Map of type slugs to their model classes and display names.
     */
    protected array $typeMap = [
        'categories' => ['model' => ChurchCategory::class, 'label' => 'Church Categories'],
        'groups' => ['model' => ChurchGroup::class, 'label' => 'Church Groups'],
        'churches' => ['model' => Church::class, 'label' => 'Churches'],
        'pcfs' => ['model' => Pcf::class, 'label' => 'PCFs'],
        'users' => ['model' => User::class, 'label' => 'Users'],
    ];

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'categories');
        $trashed = [];

        foreach ($this->typeMap as $key => $config) {
            $trashed[$key] = [
                'label' => $config['label'],
                'items' => $config['model']::onlyTrashed()->latest('deleted_at')->get(),
                'count' => $config['model']::onlyTrashed()->count(),
            ];
        }

        $totalTrashed = collect($trashed)->sum('count');

        return view('trash.index', compact('trashed', 'activeTab', 'totalTrashed'));
    }

    public function restore(string $type, int $id)
    {
        if (!isset($this->typeMap[$type])) {
            return redirect()->back()->with('error', 'Invalid type.');
        }

        $model = $this->typeMap[$type]['model']::onlyTrashed()->findOrFail($id);
        $model->restore();

        return redirect()->back()->with('success', $this->typeMap[$type]['label'] . ' item restored successfully.');
    }

    public function forceDelete(string $type, int $id)
    {
        if (!isset($this->typeMap[$type])) {
            return redirect()->back()->with('error', 'Invalid type.');
        }

        $model = $this->typeMap[$type]['model']::onlyTrashed()->findOrFail($id);
        $model->forceDelete();

        return redirect()->back()->with('success', $this->typeMap[$type]['label'] . ' item permanently deleted.');
    }
}
