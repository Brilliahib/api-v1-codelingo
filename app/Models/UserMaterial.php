<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMaterial extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function userLearningPath()
    {
        return $this->belongsTo(UserLearningPath::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public static function unlockNextMaterial(UserLearningPath $userLearningPath, Material $currentMaterial)
    {
        $nextMaterial = Material::where('learning_path_id', $currentMaterial->learning_path_id)
            ->where('order', '>', $currentMaterial->order)
            ->orderBy('order')
            ->first();

        if ($nextMaterial) {
            self::create([
                'user_learning_path_id' => $userLearningPath->id,
                'material_id' => $nextMaterial->id,
                'is_unlocked' => true,
            ]);
        }
    }
}
