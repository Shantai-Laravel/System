<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = ['alias', 'position', 'active'];

    public function translation()
    {
        return $this->hasOne(PageTranslation::class);
    }
}
