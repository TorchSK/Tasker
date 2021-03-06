<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {

  protected $table = "payment_methods";

	public $timestamps = false;


  	public function deliveryMethods() 
  	{
 		return $this->belongsToMany('App\DeliveryMethod');
 	}

}