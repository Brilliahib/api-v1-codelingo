<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Update league based on exp before saving.
     */
    protected static function booted()
    {
        static::saving(function ($user) {
            $user->level = self::determineLevel($user->exp);
            $user->league = self::determineLeague($user->exp);
        });
    }

    /**
     * Determine the league based on exp.
     */
    public static function determineLeague($exp): string
    {
        if ($exp >= 10000) {
            return 'diamond';
        } elseif ($exp >= 5000) {
            return 'emerald';
        } elseif ($exp >= 2000) {
            return 'gold';
        } elseif ($exp >= 1000) {
            return 'silver';
        }
        return 'bronze';
    }

    public static function determineLevel($exp): int
    {
        return min(99, intdiv($exp, 1000) + 1);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function completedSections()
    {
        return $this->belongsToMany(Section::class, 'user_section_progress')
            ->withPivot('is_completed')
            ->withTimestamps();
    }
}
