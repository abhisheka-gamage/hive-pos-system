<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThemeController extends Controller
{
    public function logo()
    {
        try {
            $theme = DB::table('themes')->first();
            return $theme->image_path;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update_logo(Request $request)
    {
        $validated = $request->validate([
            'file' => 'file|required'
        ]);

        $path = $request->file('file')->store('logos', 'public');
        $table = DB::table('themes');
        $table->where('status', 1)->update(['status', 1]);
        $table->insert(
            ['image_path' => 'storage/' . $path]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logo updated successfully',
            'path' => asset('storage/' . $path),
        ]);
    }
}
