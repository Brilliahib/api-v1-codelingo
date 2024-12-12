<?php

namespace App\Http\Controllers;

use App\Models\UserLearningPath;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserLearningPathController extends Controller
{
    // Get All Learning Path from Users
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

    public function getUserLearningPathDetail(UserLearningPath $userLearningPath)
    {
        $userLearningPath->load(['learningPath', 'userMaterials.material', 'userQuizzes.quiz']);
    
        $completedMaterials = $userLearningPath->userMaterials->where('is_completed', true)->count();
        $totalMaterials = $userLearningPath->userMaterials->count();
    
        $completedQuizzes = $userLearningPath->userQuizzes->where('is_completed', true)->count();
        $totalQuizzes = $userLearningPath->userQuizzes->count();
    
        $totalCompleted = $completedMaterials + $completedQuizzes;
        $totalItems = $totalMaterials + $totalQuizzes;
        $progress_status = $totalItems > 0 ? ($totalCompleted / $totalItems) * 100 : 0;
    
        $userLearningPath->progress_status = round($progress_status, 2);
    
        $combinedItems = $userLearningPath->userMaterials
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'type' => 'material',
                    'title' => $material->material->title,
                    'is_completed' => $material->is_completed,
                    'is_unlocked' => $material->is_unlocked,
                    'created_at' => $material->created_at,
                    'updated_at' => $material->updated_at,
                    'material_image' => $material->material->material_image,
                    'material_text' => $material->material->material_text,
                ];
            })
            ->merge(
                $userLearningPath->userQuizzes->map(function ($quiz) {
                    return [
                        'id' => $quiz->id,
                        'type' => 'quiz',
                        'title' => $quiz->quiz->title,
                        'is_completed' => $quiz->is_completed,
                        'is_unlocked' => $quiz->is_unlocked,
                        'created_at' => $quiz->created_at,
                        'updated_at' => $quiz->updated_at,
                        'quiz_description' => $quiz->quiz->description,
                    ];
                }),
            );
    
        $combinedItems = $combinedItems->sortBy('created_at');
    
        $userLearningPath = $userLearningPath->only('id', 'user_id', 'learning_path_id', 'progress_status', 'created_at', 'updated_at');
        
        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'Detail User Learning Path berhasil diambil',
                'data' => [
                    'learning_details' => $userLearningPath,
                    'learning_items' => $combinedItems,
                ],
            ],
            Response::HTTP_OK,
        );
    }
    

    // Get progress learning path users
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

    // Get Completed Learning Path from Users
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
