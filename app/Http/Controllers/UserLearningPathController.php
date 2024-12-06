<?php

namespace App\Http\Controllers;

use App\Models\UserLearningPath;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserLearningPathController extends Controller
{
    public function getAllUserLearningPaths()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(
                [
                    'statusCode' => 401,
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $userLearningPaths = UserLearningPath::with(['learningPath', 'userMaterials', 'userQuizzes'])
            ->where('user_id', $user->id)
            ->get();

        $userLearningPaths->transform(function ($userLearningPath) {
            $completedMaterials = $userLearningPath->userMaterials->where('is_completed', true)->count();
            $totalMaterials = $userLearningPath->userMaterials->count();

            $completedQuizzes = $userLearningPath->userQuizzes->where('is_completed', true)->count();
            $totalQuizzes = $userLearningPath->userQuizzes->count();

            $totalCompleted = $completedMaterials + $completedQuizzes;
            $totalItems = $totalMaterials + $totalQuizzes;
            $progress_status = $totalItems > 0 ? ($totalCompleted / $totalItems) * 100 : 0;

            $userLearningPath->progress_status = round($progress_status, 2);
            return $userLearningPath;
        });

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

    public function getUserLearningPathProgress(UserLearningPath $userLearningPath)
    {
        $userLearningPath->load(['userMaterials', 'userQuizzes']);

        $completedMaterials = $userLearningPath->userMaterials->where('is_completed', true)->count();
        $totalMaterials = $userLearningPath->userMaterials->count();

        $completedQuizzes = $userLearningPath->userQuizzes->where('is_completed', true)->count();
        $totalQuizzes = $userLearningPath->userQuizzes->count();

        $totalCompleted = $completedMaterials + $completedQuizzes;
        $totalItems = $totalMaterials + $totalQuizzes;
        $progress = $totalItems > 0 ? ($totalCompleted / $totalItems) * 100 : 0;

        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'Progress User Learning Path berhasil dihitung',
                'data' => [
                    'progress_status' => round($progress, 2),
                    'completedMaterials' => $completedMaterials,
                    'totalMaterials' => $totalMaterials,
                    'completedQuizzes' => $completedQuizzes,
                    'totalQuizzes' => $totalQuizzes,
                ],
            ],
            Response::HTTP_OK,
        );
    }

    public function getCompletedUserLearningPaths()
    {
        $userLearningPaths = UserLearningPath::with(['learningPath', 'userMaterials', 'userQuizzes'])->get();

        $completedUserLearningPaths = $userLearningPaths->filter(function ($userLearningPath) {
            $completedMaterials = $userLearningPath->userMaterials->where('is_completed', true)->count();
            $totalMaterials = $userLearningPath->userMaterials->count();

            $completedQuizzes = $userLearningPath->userQuizzes->where('is_completed', true)->count();
            $totalQuizzes = $userLearningPath->userQuizzes->count();

            $totalCompleted = $completedMaterials + $completedQuizzes;
            $totalItems = $totalMaterials + $totalQuizzes;

            $progress_status = $totalItems > 0 ? ($totalCompleted / $totalItems) * 100 : 0;

            return round($progress_status, 2) === 100.0;
        });

        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'User Learning Paths dengan progress 100% berhasil diambil',
                'data' => $completedUserLearningPaths->values(),
            ],
            Response::HTTP_OK,
        );
    }
}
