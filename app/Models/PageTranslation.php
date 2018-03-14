<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $table = 'pages_translation';

    protected $fillable = [
        'page_id', 'lang_id', 'slug', 'title', 'descr', 'body', 'img', 'meta_title', 'meta_descr', 'meta_keywords',
    ];
}
