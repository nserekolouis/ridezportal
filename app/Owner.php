<?php

// use Illuminate\Auth\UserTrait;
// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableTrait;
// use Illuminate\Auth\Reminders\RemindableInterface;

// class Owner extends Eloquent  implements UserInterface, RemindableInterface{

// 	use UserTrait, RemindableTrait, SoftDeletingTrait;

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $table = 'owner';

}
