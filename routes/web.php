<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChurchCategoryController;
use App\Http\Controllers\ChurchGroupController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\PcfController;
use App\Http\Controllers\FirstTimerController;
use App\Http\Controllers\BringerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FoundationProgressController;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\RetainedMemberController;
use App\Http\Controllers\Admin\HomepageSettingsController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\CredentialsController;
use App\Http\Controllers\LocalAssemblyController;
use App\Models\HomepageSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = HomepageSetting::first();
    return view('welcome', compact('settings'));
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('church-groups/check-contact', [ChurchGroupController::class, 'checkContact'])->name('church-groups.check-contact');
Route::get('first-timers/check-contact', [FirstTimerController::class, 'checkContact'])->name('first-timers.check-contact');
Route::get('church-groups/{churchGroup}/pcfs', [ChurchGroupController::class, 'getPcfs'])->name('church-groups.pcfs');
Route::get('church-groups/{churchGroup}/churches', [ChurchGroupController::class, 'getChurches'])->name('church-groups.churches');
Route::get('local-assemblies/check-name', [LocalAssemblyController::class, 'checkName'])->name('local-assemblies.check-name');

use App\Http\Controllers\PerformanceController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('performance', [PerformanceController::class, 'index'])->name('performance.index');
    Route::get('performance/export/excel', [PerformanceController::class, 'downloadExcel'])->name('performance.export.excel');
    Route::get('performance/export/pdf', [PerformanceController::class, 'downloadPdf'])->name('performance.export.pdf');

    Route::resource('church-categories', ChurchCategoryController::class);
    Route::resource('church-groups', ChurchGroupController::class);
    Route::resource('churches', ChurchController::class);
    Route::resource('pcfs', PcfController::class);
    Route::get('reporting/export/excel', [ReportingController::class, 'downloadExcel'])->name('reporting.export.excel');
    Route::get('reporting/export/pdf', [ReportingController::class, 'downloadPdf'])->name('reporting.export.pdf');
    Route::get('reporting', [ReportingController::class, 'index'])->name('reporting.index');

    Route::resource('officials', OfficialController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('officials/{official}/set-default', [OfficialController::class, 'setDefault'])->name('officials.set-default');

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::post('users/{user}/approve', [\App\Http\Controllers\UserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/cancel-deletion', [\App\Http\Controllers\UserController::class, 'cancelDeletion'])->name('users.cancel-deletion');

    // Credentials Management (Super Admin only)
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('credentials', [CredentialsController::class, 'index'])->name('credentials.index');
        Route::get('credentials/challenge', [CredentialsController::class, 'showChallenge'])->name('credentials.challenge');
        Route::post('credentials/verify', [CredentialsController::class, 'verify'])->name('credentials.verify');

        Route::resource('local-assemblies', LocalAssemblyController::class)->only(['index', 'store', 'update', 'destroy']);

        // Trash Management
        Route::get('trash', [\App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
        Route::post('trash/{type}/{id}/restore', [\App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
        Route::delete('trash/{type}/{id}', [\App\Http\Controllers\TrashController::class, 'forceDelete'])->name('trash.force-delete');
    });

    Route::get('first-timers/export/excel', [FirstTimerController::class, 'downloadExcel'])->name('first-timers.export.excel');
    Route::get('first-timers/export/pdf', [FirstTimerController::class, 'downloadPdf'])->name('first-timers.export.pdf');
    Route::resource('first-timers', FirstTimerController::class);

    Route::get('retained-members/export/excel', [RetainedMemberController::class, 'downloadExcel'])->name('retained-members.export.excel');
    Route::get('retained-members/export/pdf', [RetainedMemberController::class, 'downloadPdf'])->name('retained-members.export.pdf');
    Route::post('retained-members/acknowledge-all', [RetainedMemberController::class, 'acknowledgeAll'])->name('retained-members.acknowledge-all');
    Route::post('retained-members/{retained_member}/acknowledge', [RetainedMemberController::class, 'acknowledge'])->name('retained-members.acknowledge');
    Route::resource('retained-members', RetainedMemberController::class)->only(['index', 'show', 'update']);
    Route::post('retained-members/toggle-attendance', [RetainedMemberController::class, 'toggleAttendance'])->name('retained-members.toggle-attendance');

    Route::get('bringers/export/excel', [BringerController::class, 'downloadExcel'])->name('bringers.export.excel');
    Route::get('bringers/export/pdf', [BringerController::class, 'downloadPdf'])->name('bringers.export.pdf');
    Route::get('bringers/check', [BringerController::class, 'check'])->name('bringers.check');
    Route::resource('bringers', BringerController::class);


    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/pcf/{pcf}', [AttendanceController::class, 'showPcfAttendance'])->name('attendance.pcf');
    Route::get('attendance/church/{church}', [AttendanceController::class, 'showChurchAttendance'])->name('attendance.church');
    Route::post('attendance/toggle', [AttendanceController::class, 'toggle'])->name('attendance.toggle');

    Route::resource('foundation-progress', FoundationProgressController::class);

    Route::get('bulk-upload', [BulkUploadController::class, 'index'])->name('bulk-upload.index');
    Route::get('bulk-upload/export', [BulkUploadController::class, 'exportTemplate'])->name('bulk-upload.export');
    Route::post('bulk-upload/import', [BulkUploadController::class, 'import'])->name('bulk-upload.import');

    Route::get('homepage-settings', [HomepageSettingsController::class, 'edit'])->name('homepage-settings.edit');
    Route::post('homepage-settings', [HomepageSettingsController::class, 'update'])->name('homepage-settings.update');
});

require __DIR__ . '/auth.php';
