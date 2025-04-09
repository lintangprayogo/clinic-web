<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    //

    public function index()
    {
        $doctors = User::where('role', 'doctor')->with('clinic', 'specialist')->get();
        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    public function store(Request $request)
    {

        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'clinic_id' => 'required',
                'specialist_id' => 'required'
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'clinic_id' => $request->clinic_id,
            'specialist_id' => $request->specialist_id
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = $image->storeAs('doctor', $imageName, 'public');
            $user->image = '/storage/' . $filePath;
            $user->save();
        }

        return response()->json(['status' => 'success', 'data' => $user], 201);
    }


    public function update(Request $request, $id)
    {

        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'clinic_id' => 'required',
                'specialist_id' => 'required'
            ]
        );

        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'success', 'message' => 'Doctor not found'], 404);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->clinic_id = $request->clinic_id;
        $user->specialist_id = $request->specialist_id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = $image->storeAs('doctor', $imageName, 'public');
            $user->image = '/storage/' . $filePath;
            $user->save();
        }

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'success', 'message' => 'Doctor not found'], 404);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Doctor Deleted'], 200);
    }



    public function getActiveDoctors()
    {
        $doctors = User::where('role', 'doctor')->where('status', 'active')->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    public function searchDoctors(Request $request){
        $doctors = User::where('role', 'doctor')
        ->where('name','like','%'.$request->name.'%')
        ->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }


    public function getDoctorById($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'success', 'message' => 'Doctor not found'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $user], 200);
    }


    public function getDoctorByClinic($id){
        $doctors = User::where('role', 'doctor')
        ->where('clinic_id',$id)
        ->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    public function getDoctorBySpecialist($id){
        $doctors = User::where('role', 'doctor')
        ->where('specialist_id',$id)
        ->with('clinic', 'specialist')->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }
}
