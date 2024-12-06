<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuiz extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function userLearningPath()
    {
        return $this->belongsTo(UserLearningPath::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public static function unlockQuizAfterMaterials(UserLearningPath $userLearningPath)
    {
        $unlockedMaterials = UserMaterial::where('user_learning_path_id', $userLearningPath->id)
            ->where('is_completed', true)
            ->count();

        $totalMaterials = Material::where('learning_path_id', $userLearningPath->learning_path_id)->count();

        if ($unlockedMaterials === $totalMaterials) {
            Quiz::where('learning_path_id', $userLearningPath->learning_path_id)
                ->get()
                ->each(function ($quiz) use ($userLearningPath) {
                    self::create([
                        'user_learning_path_id' => $userLearningPath->id,
                        'quiz_id' => $quiz->id,
                        'is_unlocked' => true,
                    ]);
                });
        }
    }
}
