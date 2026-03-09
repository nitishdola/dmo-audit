<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;

class DmoDistrictController extends Controller
{
    public function assignForm($userId)
    {
        $dmo = User::findOrFail($userId);

        $districts = District::orderBy('name')->get();

        $assigned = $dmo->districts->pluck('id')->toArray();

        return view('admin.dmo.assign', compact(
            'dmo',
            'districts',
            'assigned'
        ));
    }

    public function assignDistricts(Request $request, $userId)
    {
        $dmo = User::findOrFail($userId);

        $districts = $request->input('districts', []);

        $dmo->districts()->sync($districts);

        return redirect()
            ->back()
            ->with('success','Districts assigned successfully');
    }
}
