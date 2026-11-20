<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class StaffController extends Controller
{
    public function index() { $records = User::orderBy('id')->paginate(10); return view('staff.index', compact('records')); }
    public function create() { return view('staff.form', ['staff' => new User]); }
    public function store(Request $request, ImageStorageService $images)
    {
        $data = $request->validate(['full_name'=>['required','string','max:100'],'username'=>['required','string','min:3','max:50','regex:/^[a-z0-9_.-]+$/','unique:users,username'],'password'=>['required','string','min:8','max:72','confirmed'],'avatar'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:2048']]);
        try { if ($name=$images->save($request->file('avatar'), true)) $data['avatar']=$name; } catch (InvalidArgumentException $e) { throw ValidationException::withMessages(['avatar'=>$e->getMessage()]); }
        $data['password']=Hash::make($data['password']); User::create($data); return redirect()->route('staff.index')->with('success','Staff account added.');
    }
    public function edit(User $staff) { return view('staff.form', compact('staff')); }
    public function update(Request $request, User $staff, ImageStorageService $images)
    {
        $data = $request->validate(['full_name'=>['required','string','max:100'],'username'=>['required','string','min:3','max:50','regex:/^[a-z0-9_.-]+$/',Rule::unique('users','username')->ignore($staff->id)],'password'=>['nullable','string','min:8','max:72','confirmed'],'avatar'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:2048']]);
        try { if ($name=$images->save($request->file('avatar'), true)) { $images->delete($staff->avatar); $data['avatar']=$name; } } catch (InvalidArgumentException $e) { throw ValidationException::withMessages(['avatar'=>$e->getMessage()]); }
        if (filled($data['password'] ?? null)) $data['password']=Hash::make($data['password']); else unset($data['password']);
        $staff->update($data); return redirect()->route('staff.index')->with('success','Staff account updated.');
    }
    public function destroy(User $staff, ImageStorageService $images)
    {
        if ((int)$staff->id === (int)Auth::id()) return back()->with('error','You cannot delete the account you are signed in with.');
        if ($staff->sales()->exists()) return back()->with('error','This staff account has recorded sales and cannot be deleted.');
        $images->delete($staff->avatar); $staff->delete(); return redirect()->route('staff.index')->with('success','Staff account deleted.');
    }
}
