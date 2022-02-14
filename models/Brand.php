<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {

    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'name', 'image', 'image_url', 'description', 'status'
    ];

    /**
     * The category that belong to the Brand.
     */
    public function categories() {
        return $this->belongsToMany('App\models\Category');
    }

}
