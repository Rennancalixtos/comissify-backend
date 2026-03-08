<?php

namespace App\Http\Controllers;

use App\Models\LandingScreenshot;
use App\Models\TokenPackage;

class LandingController extends Controller
{
    public function index()
    {
        $packages    = TokenPackage::active()->orderBy('price')->get();
        $screenshots = LandingScreenshot::active()->get();

        return view('welcome', compact('packages', 'screenshots'));
    }
}
