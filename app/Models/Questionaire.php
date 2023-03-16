<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionaire extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function article_users()
    {
        return $this->hasMany(ArticleUserQuestionaire::class);
    }
}
