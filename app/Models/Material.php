<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function userMaterials()
    {
        return $this->hasMany(UserMaterial::class);
    }

    protected static function booted()
    {
        static::created(function ($material) {
            $learningPathId = $material->learning_path_id;

            $userLearningPaths = UserLearningPath::where('learning_path_id', $learningPathId)->get();

            foreach ($userLearningPaths as $userLearningPath) {
                $isFirstMaterial = !UserMaterial::where('user_learning_path_id', $userLearningPath->id)->exists();

                UserMaterial::create([
                    'user_learning_path_id' => $userLearningPath->id,
                    'material_id' => $material->id,
                    'is_completed' => false,
                    'is_unlocked' => $isFirstMaterial,
                ]);
            }
        });
    }
}
