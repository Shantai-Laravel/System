<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['short_code'];

    public function translation() {
        return $this->hasMany(FormTranslation::class);
    }
}
