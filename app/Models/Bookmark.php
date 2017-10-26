<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public $table = "bookmarks";

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'usertype'
    ];

    /**
     * Check Article Bookmarked or Not
     *
     * @param $articleId
     * @param $userId
     * @param $userType
     * @return bool
     */
    public function checkArticleBookmarked($articleId, $userId, $userType)
    {
        $check  = $this->where([
            'article_id'    => $articleId,
            'user_id'       => $userId,
            'usertype'      => $userType
        ])->count();

        return $check > 0 ? true : false;
    }
}
