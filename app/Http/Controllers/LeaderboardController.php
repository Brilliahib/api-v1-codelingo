<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function getTopUsers()
    {
        // Mengambil 10 user dengan exp tertinggi
        $topUsers = User::orderBy('exp', 'desc')->take(10)->get();

        if ($topUsers->isNotEmpty()) {
            return response()->json([
                'statusCode' => 200,
                'message' => '10 user dengan exp tertinggi ditemukan',
                'data' => $topUsers
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => 'Tidak ada user ditemukan',
            'data' => []
        ]);
    }
}
