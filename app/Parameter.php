<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model {

  	public function product() 
  	{
 		return $this->belongsTo('App\Product');
 	}

  	public function categoryParameter() 
  	{
 		return $this->belongsToMany('App\CategoryParameter');
 	}
}