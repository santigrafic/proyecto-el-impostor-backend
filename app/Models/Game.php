<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = [
        'theme',
        'word',
        'winner',
        'started_at',
        'finished_at',
    ];

     // RELACIONES ELOQUENT
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
