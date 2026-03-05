<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Models\LocalAssembly;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PastorRegistrationController extends Controller
{
    /**
     * Display the pastor registration view.
     */
    public function create(): View
    {
        $groups = \App\Models\ChurchGroup::whereHas('churchCategory', function ($q) {
            $q->where('name', 'OTHER CHURCHES');
        })->whereNotIn('group_name', ['LAA', 'AVENOR'])->get();

        $assemblies = \App\Models\LocalAssembly::orderBy('name')->get();

        return view('auth.pastor-register', compact('groups', 'assemblies'));
    }

    /**
     * Handle an incoming pastor registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Pastor Profile Fields
            'title' => ['required', 'in:Bro,Sis,Pastor,Dcn,Dcns,Mr,Mrs'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'contact' => ['required', 'string', 'max:255', new \App\Rules\UniqueContact(null, null, $request->boolean('ignore_group_duplicate'))],
            'gender' => ['nullable', 'in:Male,Female'],
            'birth_day_day' => ['nullable', 'string', 'size:2'],
            'birth_day_month' => ['nullable', 'string', 'size:2'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Church Fields
            'church_group_id' => ['required', 'exists:church_groups,id'],
            'church_name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::exists('local_assemblies', 'name')->where(function ($query) use ($request) {
                    $query->where('church_group_id', $request->church_group_id);
                }),
                'unique:churches,name'
            ],
            'venue' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profiles', 'public');
        }

        $birthDay = null;
        if ($request->birth_day_day && $request->birth_day_month) {
            $birthDay = $request->birth_day_day . '-' . $request->birth_day_month;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'birth_day' => $birthDay,
            'occupation' => $request->occupation,
            'marital_status' => $request->marital_status,
            'profile_picture' => $profilePicturePath,
            'title' => $request->title,
            'plain_password' => $request->password,
            'is_approved' => false,
        ]);

        $user->assignRole('Admin'); // Assign Pastor role

        // Create the Church automatically
        \App\Models\Church::create([
            'church_group_id' => $request->church_group_id,
            'name' => $request->church_name,
            'venue' => $request->venue,
            'location' => $request->location,
            'title' => $request->title,
            'leader_name' => $user->name,
            'leader_contact' => $request->contact,
        ]);

        event(new Registered($user));

        // Redirect to login page with a pending approval message
        return redirect()->route('login')->with('status', 'Your registration is successful! However, your account is pending approval by an administrator. You will be able to log in once approved.');
    }
}
