<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MatchingPairs extends Model
{
    use HasFactory;
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($matchingPairs) {
            if (empty($matchingPairs->id)) {
                $matchingPairs->id = (string) Str::uuid();
            }
        });

        static::created(function ($matchingPairs) {
            // Ambil user_learning_paths terkait dengan learning_path_id
            $learningPathId = $matchingPairs->learning_path_id;

            $userLearningPaths = UserLearningPath::where('learning_path_id', $learningPathId)->get();

            foreach ($userLearningPaths as $userLearningPath) {
                // Cek apakah ini adalah first matching pair atau semua sudah selesai
                $isFirstPair = !userMatchingPairs::where('user_learning_path_id', $userLearningPath->id)->exists();
                $allPairsCompleted = UserMatchingPairs::where('user_learning_path_id', $userLearningPath->id)
                    ->where('is_completed', false)
                    ->doesntExist();

                $isUnlocked = $isFirstPair || $allPairsCompleted;

                // Buat UserMatchingPair baru
                UserMatchingPairs::create([
                    'user_learning_path_id' => $userLearningPath->id,
                    'matching_pair_id' => $matchingPairs->id,
                    'is_completed' => false,
                    'is_unlocked' => $isUnlocked,
                ]);
            }
        });
    }

    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function userMatchingPairs()
    {
        return $this->hasMany(UserMatchingPairs::class);
    }

    public function items()
    {
        return $this->hasMany(MatchingPairItem::class, 'matching_pair_id');
    }
}
