<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function getPreferences(Request $request) {
        $preferences = $request->user()->preference;
        return response()->json($preferences);
    }

    public function setPreferences(Request $request) {
        $data = $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array',
        ]);

        $preference = Preference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json($preference);
    }
}