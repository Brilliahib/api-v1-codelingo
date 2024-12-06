<?php

namespace App\Http\Controllers;

use App\Models\UserLearningPath;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserLearningPathController extends Controller
{
    public function getAllUserLearningPaths()
    {
        $userLearningPaths = UserLearningPath::with(['user', 'learningPath', 'userMaterials.material', 'userQuizzes.quiz'])->get();

        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'Data User Learning Paths berhasil diambil',
                'data' => $userLearningPaths,
            ],
            Response::HTTP_OK,
        );
    }

    /**
     * Mendapatkan detail UserLearningPath
     *
     * @param  \App\Models\UserLearningPath  $userLearningPath
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLearningPathDetail(UserLearningPath $userLearningPath)
    {
        // Memuat relasi user, learningPath, userMaterials dan userQuizzes
        $userLearningPath->load(['user', 'learningPath', 'userMaterials.material', 'userQuizzes.quiz']);

        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'Detail User Learning Path berhasil diambil',
                'data' => $userLearningPath,
            ],
            Response::HTTP_OK,
        );
    }
}
