<?php 

namespace App\Services;

use App\Services\Contracts\ProductServiceContract;
use Illuminate\Http\Request;

use App\Category;
use App\Product;

use Hash;
use Session;
use Auth;
use Cookie;
use Response;

class ProductService implements ProductServiceContract {


  public function __construct ()
  {
  }

  public function getUserPriceType()
  {
    if(Auth::user()->voc)
    {
        $price = "voc_sort_price";

    }
    else
    {
        $price = "moc_sort_price";
    }

    return $price;   
  }

	public function query($filters, $except=[])
    {
        //dd($filters);
        $result = Product::leftjoin('product_parameters',function($leftjoin){
            $leftjoin->on('product_parameters.product_id', '=', 'products.id');
        })
        ->leftjoin('parameters',function($leftjoin){
            $leftjoin->on('parameters.id', '=', 'product_parameters.parameter_id');
        })
        ->where('active',1)
        ->where(function($query) use ($filters, $except){
            foreach ((array)$filters as $key => $temp){
              if ($filters[$key])
              {
                if ($key=='search')
                {   
                    $query->where(function($query)  use ($filters, $except){
                          $query->where("name", "like", "%".$filters['search']."%")->orWhere("desc", "like", "%".$filters['search']."%");
                    });
                }
                elseif($key=='category')
                {

                    $query->whereHas('categories', function($query) use ($filters){
                        $query->where('category_id', $filters['category']);

                        if (Category::find($filters['category'])->children->count() > 0)
                        {
                            $query->orWhereIn('category_id', Category::find($filters['category'])->children->pluck('id'));
                        }

                        if (Category::find($filters['category'])->children->count() > 0)
                        {
                            foreach(Category::find($filters['category'])->children as $child)
                            {
                                $query->orWhereIn('category_id', Category::find($child->id)->children->pluck('id'));
                            }
                        }

                        //dd(Category::find($filters['category'])->parent->id);
                    });
                }
                elseif($key=='price')
                {
                    $array = explode(",",$filters['price']);

                    $query->whereHas('priceLevels', function($query) use ($array){
                        $query->whereBetween($this->getUserPriceType(), $array);
                    });

                    //old price parameter
                    //$query->whereBetween('price', $array);

                }
                elseif($key=='parameters')
                {
                    foreach ((array)$filters['parameters'] as $categoryParameter => $value)
                    {
                        if ($categoryParameter == 'makers')
                        {
                             $query->whereIn('maker', $value);
                        }
                        else
                        {
                            $query->whereHas('parameters', function ($query) use ($categoryParameter, $value) {
                                $query->whereIn('value',(array)$value)->whereHas('definition', function ($query)  use ($categoryParameter, $value){
                                       $query->where('key', $categoryParameter);
                                });
                            }); 
                        }
                   

                    }
                };
              }
            }
        });

        return $result->groupBy(['products.id'])->select(['products.*']);
    }

    public function makerList(Request $request){
        
        $filters['parameters']['makers'] = [$request->get('maker')];  

        $sortBy = $request->get('sortBy');
        $sortOrder = $request->get('sortOrder');    

        if (!$sortBy) {$sortBy = 'name';};
        if (!$sortOrder) {$sortOrder = 'asc';};

        if ($sortBy == 'price')
        {
            $sortBy = $this->getUserPriceType();
        }
        
        $products = $this->query($filters)->orderBy($sortBy,$sortOrder)->get();

        // set price range
        $priceRangeFilters = $filters;
        unset($priceRangeFilters['price']);

        $priceRange = [];
        $priceRange[0] = $products->pluck($this->getUserPriceType())->min();
        $priceRange[1] = $products->pluck($this->getUserPriceType())->max();

        $categories = [];
        foreach ($products as $product)
        {
            array_push($categories, $product->categories->first()->id);
        }


      $data = [
            'products' => $products,
            'priceRange' => $priceRange,
            'categories' => array_unique($categories),
            'maker' => $request->get('maker')
        ];

        return $data;

    }

    public function list(Request $request)
    {
        $filters = $request->get('filters');
        $filters['category'] = $request->get('category');

        if (!isset($filters['search']))
        {
            $filters['search'] = '';
        }

        $category = Category::find($request->get('category'));
        $sortBy = $request->get('sortBy');
        $sortOrder = $request->get('sortOrder');    

        if (!$sortBy) {$sortBy = 'name';};
        if (!$sortOrder) {$sortOrder = 'asc';};

        if ($sortBy == 'price')
        {
            $sortBy = $this->getUserPriceType();
        }

        
        // set active filters
        $activeFilters = collect($filters);

        // set products
        $products = $this->query($filters)->orderBy($sortBy,$sortOrder)->get();
        // set price range
        $priceRangeFilters = $filters;
        unset($priceRangeFilters['price']);

        $priceRange = [];
        $priceRange[0] = $products->pluck($this->getUserPriceType())->min();
        $priceRange[1] = $products->pluck($this->getUserPriceType())->max();

        if ($filters['search'] && !$filters['category'])
        {
            $makers = [];
            $params = [];
            $filterCounts = [];
        }
        else
        {
            
            $makers = $category->products->unique(['maker']); 

            if($category->children->count() > 0)
            {
                foreach ($category->children as $child)
                {
                    $makers = $makers->merge($child->products->unique(['maker'])); 

                    if($child->children->count() > 0)
                    {
                        foreach ($child->children as $child2)
                        {
                            $makers = $makers->merge($child2->products->unique(['maker'])); 
                        }
                    }
                }
            }


            $categoryParameters = $category->parameters;

            foreach($category->children as $child)
            {
                $categoryParameters = $categoryParameters->merge($child->parameters);
                $makers = $makers->union($child->products->unique(['maker']));

                foreach($child->children as $child2)
                {
                    $categoryParameters = $categoryParameters->merge($child2->parameters);
                    $makers = $makers->union($child2->products->unique(['maker']));
                }
            }


            $temp = [];
            $filterCounts['parameters'] = [];

            $filterCounts['parameters']['makers'] = [];
            $filterCountFilters['parameters']['makers'] = [];
            foreach ($makers as $maker)
            {
                $filterCountFilters = $filters;
                
                $filterCountFilters['parameters']['makers'] = [$maker->maker];
                
                $filterCounts['parameters']['makers'][$maker->maker] = $this->query($filterCountFilters)->get()->count();
                
            }

            foreach ($categoryParameters as $categoryParameter)
            {
                $filterCounts['parameters'][$categoryParameter->id] = [];
                $filterCountFilters = $filters;

                foreach ($categoryParameter->productParameters as $productParameter)
                {   
                    $filterCountFilters['parameters'][$categoryParameter->key] = $productParameter->value;
                    array_push($temp, $filterCountFilters);
                    $filterCounts['parameters'][$categoryParameter->id][$productParameter->value] = $this->query($filterCountFilters, [$categoryParameter->key])->get()->count();
                    unset($filterCountFilters['parameters'][$categoryParameter->key]);
                }
            }

            $params = $category->parameters;

            if($category->children->count() > 0)
            {
                foreach ($category->children as $child)
                {
                    $params = $params->merge($child->parameters); 

                    if($child->children->count() > 0)
                    {
                        foreach ($child->children as $child2)
                        {
                            $params = $params->merge($child2->parameters); 
                        }
                    }
                }
            }
        }

        $data = [
            'makers' => $makers,
            'filters' => $params,
            'products' => $products,
            'activeFilters' => $activeFilters,
            'filterCounts' => $filterCounts,
            'priceRange' => $priceRange
        ];

        return $data;
    }


     public function categoryCounts()
     {
        $categoryCounts = [];
        $categoryCounts['categories'] = [];

        foreach (Category::all() as $category)
        {
            $categoryCounts['categories'][$category->id] = $category->products->where('active',1)->count();

            if ($category->children->count() > 0)
            {
                foreach ($category->children as $child)
                {
                    $categoryCounts['categories'][$category->id] += $child->products->where('active',1)->count();

                    if ($child->children->count() > 0)
                    {
                        foreach ($child->children as $subchild)
                        {
                            $categoryCounts['categories'][$category->id] += $subchild->products->where('active',1)->count();
                        }
                    }
                }
            }
        }

        return $categoryCounts;
     }
 
}