<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    public function index()
    {
        $specialists = \App\Models\Specialist::all();
        return response()->json([
            'status' => 'success',
            'data' => $specialists
        ]);
    }
}
