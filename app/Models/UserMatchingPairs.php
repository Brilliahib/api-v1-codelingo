<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserMatchingPairs extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function userLearningPath()
    {
        return $this->belongsTo(UserLearningPath::class);
    }

    public function matchingPair()
    {
        return $this->belongsTo(MatchingPairs::class);
    }

    protected static function booted()
    {
        static::updated(function ($userMatchingPair) {
            if ($userMatchingPair->is_completed) {
                $learningPathId = $userMatchingPair->matchingPair->learningPath->id;

                // Cek apakah semua matching pairs selesai
                $allPairsCompleted = UserMatchingPairs::where('user_learning_path_id', $userMatchingPair->user_learning_path_id)
                    ->where('is_completed', false)
                    ->doesntExist();

                if ($allPairsCompleted) {
                    // Unlock material berikutnya
                    $firstQuiz = Quiz::where('learning_path_id', $learningPathId)->orderBy('id')->first();

                    if ($firstQuiz) {
                        UserQuiz::firstOrCreate(
                            [
                                'user_learning_path_id' => $userMatchingPair->user_learning_path_id,
                                'quiz_id' => $firstQuiz->id,
                            ],
                            [
                                'is_unlocked' => true,
                            ],
                        );
                    }
                }
            }
        });
    }
}
