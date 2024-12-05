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

    // Mendapatkan pengguna dalam liga Bronze
    public function getBronzeUsers()
    {
        return $this->getUsersByLeague('bronze');
    }

    public function getSilverUsers()
    {
        return $this->getUsersByLeague('silver');
    }

    public function getGoldUsers()
    {
        return $this->getUsersByLeague('gold');
    }

    public function getEmeraldUsers()
    {
        return $this->getUsersByLeague('emerald');
    }

    public function getDiamondUsers()
    {
        return $this->getUsersByLeague('diamond');
    }

    private function getUsersByLeague(string $league)
    {
        $users = User::where('league', $league)
            ->orderBy('exp', 'desc')
            ->take(10)
            ->get();

        if ($users->isNotEmpty()) {
            return response()->json([
                'statusCode' => 200,
                'message' => "10 user dengan liga $league ditemukan",
                'data' => $users
            ]);
        }

        return response()->json([
            'statusCode' => 404,
            'message' => "Tidak ada user dalam liga $league ditemukan",
            'data' => []
        ]);
    }
}
