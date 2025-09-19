<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\CommissionLevelFactory;

class CommissionLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['level'];

    protected $levels = [
        'LEVEL_1' => 'level 1',
        'LEVEL_2' => 'level 2',
        'LEVEL_3' => 'level 3',
        'LEVEL_3' => 'level 3',
        'LEVEL_4' => 'level 4',
    ];

    public function createLevel()
    {
        $levels = $this->levels;
        foreach ($levels as $level) {
            self::updateOrCreate([
                'level' => $level
            ]);
        }
    }
}
