<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\RequestContributor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\RequestContributor as ModelsRequestContributor;

class UserController extends Controller
{
    protected $roles;

    public function __construct()
    {
        // Inisialisasi nilai enum dari model
        $this->roles = implode(',', User::getRoleOptions());
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = User::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '<button href="#" class="btn bg-back-primary editUser" data-id="' . $data->id . ' "><span class="ri-edit-box-line" title="Edit"></span></button>
                <button type="submit" class="btn bg-back-error deleteUser" data-id="' . $data->id . ' "><span class="ri-delete-bin-line" title="Delete"></span></button>';
                })
                ->addColumn('photo', function ($data) {
                    return '<img src="' . asset($data->profile_photo_path) . '" width="30">';
                })
                ->editColumn('created_at', function ($data) {
                    return $data->created_at ? $data->created_at->format("d M Y") : '-';
                })
                ->editColumn('email_verified_at', function ($data) {
                    return $data->email_verified_at ? $data->email_verified_at->format("d M Y") : '-';
                })
                ->editColumn('role', function ($data) {
                    return '<span class="badge bg-back-' .
                        ($data->role === 'superadmin' ? 'success' : ($data->role === 'admin' ? 'primary' : 'secondary'))
                        . '">' . $data->role . '</span>';
                })
                ->rawColumns(['role', 'photo', 'action'])
                ->removeColumn(['profile_photo_path', 'updated_at', 'id'])
                ->make(true);
        }

        $data = [
            'title' => 'Users Management',
        ];

        // Ambil data enum role dari model
        $roles = explode(',', $this->roles);

        return view('pages.dashboard.users.index', compact('data', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|min:4|max:25|alpha_dash|unique:users,username',
            'role' => 'required|in:' . $this->roles,
            'email' => 'required|email|unique:users,email',
            'email_verified_at' => 'nullable',
            'password' => 'required|min:6',
        ]);
        $user = User::create($validated);
        $data = User::where('id', $user->id)->first();
        $data['created_at'] = $data->created_at->format('d M Y');

        return response()->json([
            'user' => $data,
            'message' => 'User created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|min:4|max:25|alpha_dash|unique:users,username,' . $user?->id,
            'role' => 'required|in:' . $this->roles,
            'email' => 'required|email|unique:users,email,' . $user?->id,
            'email_verified_at' => 'nullable',
            'password' => 'nullable|min:6',
        ]);
        // Cek jika username adalah admin atau superadmin dan mencoba mengubah role
        if (in_array($user->username, ['admin', 'superadmin']) && $request->has('role') && $request->role !== $user->role) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Role cannot be changed.',
                'errors' => ['403' => ['Role cannot be changed for admin or superadmin users.']],
            ], 403);
        }
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }
        $userFromDB = User::where('id', $user->id)->first();
        // Cek jika username adalah admin atau superadmin dan mencoba mengubah email_verified_at selain ke true
        if (in_array($user->username, ['admin', 'superadmin']) && isset($validated['email_verified_at']) && $validated['email_verified_at'] !== "1") {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Email verification status cannot be changed.',
                'errors' => ['403' => ['Email verification status cannot be changed for admin or superadmin users unless setting to verified.']],
            ], 403);
        }
        if ($validated['email_verified_at'] == "1") {
            if (is_null($userFromDB->email_verified_at)) {
                $validated['email_verified_at'] = now();
            } else {
                unset($validated['email_verified_at']);
            }
        } elseif ($validated['email_verified_at'] == "0") {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);
        $user = User::where('id', $user->id)->first();

        return response()->json([
            'user' => $user,
            'status' => $userFromDB->email_verified_at?->toDateTimeString() ?? false,
            'message' => 'User updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $admin = User::where('username', 'admin')->first();
        $adminId = $admin->id;

        User::where('id', $user->id)->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Display a listing of the users who requested to join as a contributor.
     *
     * @return \Illuminate\View\View The view displaying the contributor requests.
     */
    public function requestContributor()
    {
        $data = [
            'title' => 'Requested Join Contributor',
        ];

        $query = ModelsRequestContributor::with('user')
            ->orderBy(request("sort_field", 'request_contributors.created_at'), request("sort_direction", "desc"));

        if (request('search')) {
            $query->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', '%' . request()->query('search') . '%')
                        ->orWhere('email', 'like', '%' . request()->query('search') . '%');
                })->orWhere('code', 'like', '%' . request()->query('search') . '%');
            });
        }

        $query = $query->get();

        return view('pages.dashboard.contributor.index', compact('data', 'query'));
    }

    /**
     * Store a newly created contributor code request in storage.
     *
     * @return \Illuminate\Http\RedirectResponse The response after storing the data.
     */
    public function storeRequestContributor()
    {
        $user = Auth::user();
        $email = $user->email;
        $code = rand(1000, 9999);
        $now = now();
        $validUntil = $now->addMinutes(30);

        // Data untuk email
        $contentMail = [
            'username' => $user->username,
            'body' => 'You have been requested as Contributor or Writer',
            'code' => $code,
            'valid' => $validUntil->format('d M Y H:i')
        ];

        // Cek jika ini adalah permintaan resend
        if (request()->has('resend')) {
            $resendId = request('resend');

            // Throttle key (per user)
            $throttleKey = 'resend-code:' . $resendId;
            if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return redirect()->back()->with('error', 'Please wait ' . $seconds . ' seconds before requesting again.');
            }
            // Allow resend and set cooldown
            RateLimiter::hit($throttleKey, 300); // lock for 60 seconds

            $requestContributor = ModelsRequestContributor::where('user_id', $resendId)->first();
            if ($requestContributor) {
                $requestContributor->code = $code;
                $requestContributor->valid_code_until = $validUntil->format('Y-m-d H:i:s');
                $requestContributor->is_confirmed = 0;
                $requestContributor->save();

                Mail::to($email)->send(new RequestContributor($contentMail));
                return redirect()->back()->with(['success' => 'Verification code resent, please check the email']);
            } else {
                return redirect()->back()->with('error', 'Request not found for resend');
            }
        }

        // Jika bukan resend, buat permintaan baru atau update yang lama
        $requestContributor = ModelsRequestContributor::firstOrNew([
            'user_id' => $user->id,
        ]);
        $requestContributor->fill([
            'code' => $code,
            'valid_code_until' => $validUntil->format('Y-m-d H:i:s'),
            'is_confirmed' => 0
        ]);

        if ($requestContributor->save()) {
            Mail::to($email)->send(new RequestContributor($contentMail));
            return redirect()->back()->with(['success' => 'Request sent successfully, please check your email']);
        }

        Log::error('Request contributor failed');

        return redirect()->back()->with('error', 'Request failed');
    }


    /**
     * Delete a ModelsRequestContributor.
     *
     * @param ModelsRequestContributor $requestContributor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyRequestContributor(ModelsRequestContributor $requestContributor)
    {
        $requestContributor->delete();

        return redirect()->back()->with('success', 'Deleted successfully');
    }

    public function confirmCodeContributor(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input must be not empty and number',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $code = $request->code;
            $saved = ModelsRequestContributor::where(['user_id' => Auth::user()->id, 'code' => $code])
                ->where('valid_code_until', '>', now()->format('Y-m-d H:i:s'))
                ->update(['is_confirmed' => 1]);

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code does not match or has expired'
                ], 404);
            }

            User::where('id', Auth::user()->id)->update(['role' => 'writer']);

            return response()->json([
                'success' => true,
                'message' => 'Code confirmed successfully. You can now start contributing and write articles.',
                'info' => 'The page will automatically refresh after 3 seconds.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Confirm contributor code failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
