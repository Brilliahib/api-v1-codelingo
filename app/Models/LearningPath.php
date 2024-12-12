<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function userLearningPaths()
    {
        return $this->hasMany(UserLearningPath::class);
    }

    protected static function booted()
    {
        static::created(function ($learningPath) {
            $users = User::all();

            foreach ($users as $user) {
                UserLearningPath::create([
                    'user_id' => $user->id,
                    'learning_path_id' => $learningPath->id,
                ]);
            }
        });
    }
}
