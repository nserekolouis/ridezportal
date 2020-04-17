<?php

//class WalkerReview extends Eloquent {

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalkerReview extends Model {

    protected $table = 'review_walker';

    public function dog()
    {
        return $this->belongsTo('Dog');
    }

}
