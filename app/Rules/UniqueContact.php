<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\ContactCheckService;

class UniqueContact implements ValidationRule
{
    protected $excludeId;
    protected $excludeType;
    protected $ignoreGroupDuplicate;

    /**
     * Create a new rule instance.
     *
     * @param mixed $excludeId
     * @param string|null $excludeType
     * @param bool $ignoreGroupDuplicate
     */
    public function __construct($excludeId = null, $excludeType = null, $ignoreGroupDuplicate = false)
    {
        $this->excludeId = $excludeId;
        $this->excludeType = $excludeType;
        $this->ignoreGroupDuplicate = $ignoreGroupDuplicate;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(ContactCheckService::class);
        $result = $service->checkDuplicate($value, $this->excludeId, $this->excludeType);

        if ($result['exists']) {
            if ($this->ignoreGroupDuplicate && ($result['type'] ?? '') === 'group') {
                return;
            }
            $fail("The :attribute already belongs to {$result['owner']} in {$result['entity']}.");
        }
    }
}
