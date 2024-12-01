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
}
