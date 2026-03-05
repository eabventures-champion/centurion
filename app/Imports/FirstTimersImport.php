<?php

namespace App\Imports;

use App\Models\FirstTimer;
use App\Models\Bringer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\DB;

class FirstTimersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $pcfId;
    protected $churchId;
    protected $contactCheckService;
    protected $processedContacts = [];

    public function __construct($pcfId, $contactCheckService, $churchId = null)
    {
        $this->pcfId = $pcfId;
        $this->churchId = $churchId;
        $this->contactCheckService = $contactCheckService;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $pcfId = $this->pcfId;

            $bringerId = null;
            $bringerContact = $row['bringer_contact'] ?? null;
            $bringerSeniorCell = $row['bringer_senior_cell_name'] ?? 'Bulk Import';
            $bringerCell = $row['bringer_cell_name'] ?? 'Bulk Import';

            if ($bringerContact) {
                $bringer = Bringer::where('contact', $bringerContact)->first();

                if ($bringer) {
                    // Claim the bringer if they weren't assigned yet (safe here because validation passed)
                    if (!$bringer->pcf_id && !$bringer->church_id) {
                        $bringer->update([
                            'pcf_id' => $this->pcfId,
                            'church_id' => $this->churchId
                        ]);
                    }
                } else {
                    // Create new bringer assigned to this context
                    $bringer = Bringer::create([
                        'pcf_id' => $this->pcfId,
                        'church_id' => $this->churchId,
                        'name' => $row['bringer_name'] ?? 'Unknown (Bulk Import)',
                        'contact' => $bringerContact,
                        'senior_cell_name' => $bringerSeniorCell,
                        'cell_name' => $bringerCell,
                    ]);
                }
                $bringerId = $bringer->id;
            } else {
                // Self-bringer logic
                $bringer = Bringer::where('contact', $row['primary_contact'])->first();

                if ($bringer) {
                    if (!$bringer->pcf_id && !$bringer->church_id) {
                        $bringer->update([
                            'pcf_id' => $this->pcfId,
                            'church_id' => $this->churchId
                        ]);
                    }
                } else {
                    $bringer = Bringer::create([
                        'pcf_id' => $this->pcfId,
                        'church_id' => $this->churchId,
                        'contact' => $row['primary_contact'],
                        'name' => $row['full_name'],
                        'senior_cell_name' => $bringerSeniorCell,
                        'cell_name' => $bringerCell
                    ]);
                }
                $bringerId = $bringer->id;
            }

            $firstTimer = FirstTimer::create([
                'church_id' => $this->churchId,
                'pcf_id' => $pcfId,
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'primary_contact' => $row['primary_contact'],
                'alternate_contact' => $row['alternate_contact'],
                'gender' => $row['gender'] ?? 'Male',
                'date_of_birth' => $row['date_of_birth'],
                'residential_address' => $row['residential_address'],
                'occupation' => $row['occupation'],
                'date_of_visit' => $row['date_of_visit'],
                'marital_status' => $row['marital_status'] ?? 'Single',
                'born_again' => $row['born_again'] ?? 0,
                'water_baptism' => $row['water_baptism'] ?? 0,
                'prayer_requests' => $row['prayer_requests'],
                'bringer_id' => $bringerId,
                'service_count' => 1,
            ]);

            // Create an actual attendance log for the registration date
            \App\Models\AttendanceLog::create([
                'first_timer_id' => $firstTimer->id,
                'service_date' => $firstTimer->date_of_visit,
                'marked_by' => auth()->id(),
            ]);

            $this->processedContacts[] = $row['primary_contact'];

            return $firstTimer;
        });
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'primary_contact' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                function ($attribute, $value, $fail) {
                    if (in_array($value, $this->processedContacts)) {
                        $fail("The contact $value is a duplicate within this Excel sheet.");
                        return;
                    }

                    $result = $this->contactCheckService->checkDuplicate($value);
                    if ($result['exists']) {
                        $fail("The contact $value already exists in the system ({$result['entity']}: {$result['owner']}).");
                    }
                },
            ],
            'residential_address' => 'required|string',
            'gender' => 'required|in:Male,Female',
            'date_of_visit' => 'required|date',
            'marital_status' => 'required|in:Single,Married,Widowed,Divorced',
            'bringer_name' => 'nullable|string|max:255',
            'bringer_senior_cell_name' => 'required|string|max:255',
            'bringer_cell_name' => 'required|string|max:255',
            'bringer_contact' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
                function ($attribute, $value, $fail) {
                    $bringer = Bringer::where('contact', $value)->first();
                    if ($bringer) {
                        if (
                            ($bringer->pcf_id && $bringer->pcf_id != $this->pcfId) ||
                            ($bringer->church_id && $bringer->church_id != $this->churchId)
                        ) {
                            $assignedTo = $bringer->pcf ? "PCF: {$bringer->pcf->name}" : "Church: {$bringer->church->name}";
                            $fail("The bringer with contact $value is already assigned to {$assignedTo}.");
                        }
                    }
                }
            ],
        ];
    }
}
