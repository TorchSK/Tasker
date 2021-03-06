<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

  protected $table = "products";
  protected $fillable = ["category_id", "name", "code", "price", "price_unit", "desc", "link", "stock", "maker", "new", "sale", "sale_price","active","step", "back1", "back2", "back3", "thumbnail_flag"];

  	public function categories() 
  	{
 		 return $this->belongsToMany('App\Category');
 	  }

    public function getParentCategoriesAttribute() {
        $result = collect();
        $parents = function($categories) use(&$result, &$parents) {
            $result = $result->merge($categories->pluck('ancestors')->flatten());
        };
        $parents($this->categories);
        return $result;
    }

      public function getParentBaseCategoriesAttribute() {
        $result = collect();
        $parents = function($parentCategories) use(&$result, &$parents) {
            $result = $result->merge($parentCategories->pluck('ancestors')->flatten());
        };
        $parents($this->parentCategories);
        return $result;
    }

  	public function parameters() 
  	{
 		return $this->hasMany('App\ProductParameter');
 	}

    public function relatedProducts() 
    {
    return $this->hasManyThrough('App\Product', 'App\ProductRelation','product_id','id','id','related_product_id');
  }

  public function allfiles() 
    {
    return $this->hasMany('App\File');
  }

  public function allVariants() 
    {
    return $this->hasManyThrough('App\Product','App\Variant','product_id','id','id','variant_id');
  }

  public function variants() 
    {
    return $this->hasManyThrough('App\Product','App\Variant','product_id','id','id','variant_id')->where('type','Variant');
  }

  public function sizes() 
    {
    return $this->hasMany('App\Size');
  }


  public function colors() 
    {
    return $this->hasManyThrough('App\Product','App\Variant','product_id','id','id','variant_id')->where('type','Color');
  }


  public function stickers() 
    {
    return $this->belongsToMany('App\Sticker');
  }


  public function rowStickers() 
    {
    return $this->belongsToMany('App\Sticker')->where('product_row',1);
  }

    public function detailStickers() 
    {
    return $this->belongsToMany('App\Sticker')->where('product_detail',1);
    }



 	public function image() 
  	{
 		return $this->hasOne('App\File')->where('type','image')->where('primary',1);
 	}

 	public function otherImages() 
  	{
 		return $this->hasMany('App\File')->where('type','image')->where('primary',0);;
 	}

  public function images() 
    {
    return $this->hasMany('App\File')->where('type','image');
  }

  public function videos() 
    {
    return $this->hasMany('App\File')->where('type','video');
  }

  public function files() 
    {
    return $this->hasMany('App\File')->where('type','file');
  }

  public function priceLevels() 
    {
    return $this->hasMany('App\PriceLevel');
  }

 	public function orders() 
  	{
 		return $this->belongsToMany('App\Order');
 	}

  public function ratings()
  {
    return $this->morphMany('App\Rating','ratingable');
  }


	public function setSaleAttribute($value)
  	{
      if ($value === 'on')
      {
        $this->attributes['sale'] = 1;
      }

      elseif($value === 'off')
      {
        $this->attributes['sale'] = 0;
      }

      else
      {
         $this->attributes['sale'] = $value;
      }
  	}
	
	public function setNewAttribute($value)
  	{
      if ($value == 'on' || $value == 1)
      {
        $this->attributes['new'] = 1;
      }
      elseif ($value == 'off' || $value == 0)
      {
        $this->attributes['new'] = 0;
      }
  	}


  public function setActiveAttribute($value)
    {
      if ($value === 'on')
      {
        $this->attributes['active'] = 1;
      }
      
      elseif ($value === 'off')
      {
        $this->attributes['active'] = 0;
      }

      else
      {
         $this->attributes['active'] = $value;
      }
    }

  public function setThumbnailFlagAttribute($value)
    {
      if ($value == 'on' || $value == 1)
      {
        $this->attributes['thumbnail_flag'] = 1;
      }
      elseif ($value == 'off' || $value == 0)
      {
        $this->attributes['thumbnail_flag'] = 0;
      }
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] =  floatval(str_replace(',', '.', $value));
    }
  

}


