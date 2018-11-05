<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Table name is already posts but i can change it if i want like this but we will name it posts too
    protected $title= 'posts';
    //Primary key
    public $primaryKey = 'id';
    //Timestamps "it is already set to true"
    public $timestamps = true;

// what this means that a single post has a relationship with a user and belongs to a user
 public function user(){

    return $this->belongsTo('App\User');
 }

}
