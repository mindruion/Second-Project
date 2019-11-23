<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cook extends Model
{
    protected $fillable = [
        'imagePath_id', 'title'
    ];

    public function photos(){
        return $this->belongsTo('App\Photo', 'imagePath_id');
    }
    public function recipe(){
        return $this->belongsTo('App\Recipe', 'recipe_id');
    }
}
