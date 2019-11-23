<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'ingredients', 'pieces', 'description'
    ];

    public function ingredients() {
        $this->belongsTo('App\Ingredient');
    }
}
