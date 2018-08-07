<?php 

namespace App\Services;

use App\Services\Contracts\ProductServiceContract;
use Illuminate\Http\Request;

use App\Category;
use App\Product;
use App\ProductParameter;

use Hash;
use Session;
use Auth;
use Cookie;
use Response;
use DB;

class ProductService implements ProductServiceContract {


  public function __construct ()
  {
  }

  public function getUserPriceType()
  {
    if(Auth::check() && Auth::user()->voc)
    {
        $price = "voc_sort_price";

    }
    else
    {
        $price = "moc_sort_price";
    }

    return $price;   
  }

	public function query($filters, $except=[], $children)
    {
        $result = Product::leftjoin('product_parameters',function($leftjoin){
            $leftjoin->on('product_parameters.product_id', '=', 'products.id');
        })
        ->leftjoin('parameters',function($leftjoin){
            $leftjoin->on('parameters.id', '=', 'product_parameters.parameter_id');
        })
        ->where('active',1)
        ->where(function($query) use ($filters, $except, $children){
            foreach ((array)$filters as $key => $temp){
              if ($filters[$key])
              {
                if ($key=='search')
                {   
                    $query->where(function($query)  use ($filters, $except){
                          $query->where("name", "like", "%".$filters['search']."%")->orWhere("desc", "like", "%".$filters['search']."%")->orWhere("code", "like", "%".$filters['search']."%");
                    });
                }
                elseif($key=='category')
                {

                    $query->whereHas('categories', function($query) use ($filters, $children){
                        $query->where('category_id', $filters['category'])->orWhereIn('category_id', $children->pluck('id'));
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
        
        $products = $this->query($filters,[], [])->orderBy($sortBy,$sortOrder)->paginate(28);

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

        $sortBy = $request->get('sortBy');
        $sortOrder = $request->get('sortOrder');    

        if (!$sortBy) {$sortBy = 'name';};
        if (!$sortOrder) {$sortOrder = 'asc';};

        if ($sortBy == 'price')
        {
            $sortBy = $this->getUserPriceType();
        }


        if (isset($filters['search']))
        {

            $searchFilters['search'] = $filters['search'];

            $products = $this->query($searchFilters,[], [])->orderBy($sortBy,$sortOrder)->paginate(28);

            $makers = $products->unique(['maker']); 

            $temp = [];
            $filterCounts['parameters'] = [];

            $filterCounts['parameters']['makers'] = [];
            $filterCountFilters['parameters']['makers'] = [];

            foreach ($makers as $maker)
            {
                $filterCountFilters = $searchFilters;
                
                $filterCountFilters['parameters']['makers'] = [$maker->maker];
                
                $filterCounts['parameters']['makers'][$maker->maker] = $this->query($filterCountFilters,[], [])->get()->count();
                
            }

            $categoryParameters = [];
            $filterValues = [];

            $activeFilters = collect($searchFilters);
            
            $priceRange = [];

        }
        else
        {

            $category = Category::with(['children.products'])->find($request->get('category'));
            $children = $category->children;

            // get all products without any filters for category and all children
            $unfilteredProducts = $category->products;
            foreach ($children as $child)
            {
                 $unfilteredProducts = $unfilteredProducts->merge($child->products); 
            }

            // get all parameters for category and all children 

            
            // set active filters
            $activeFilters = collect($filters);

            // set products
            $products = $this->query($filters,[], $children)->orderBy($sortBy,$sortOrder)->paginate(28);


            // set price range
            $priceRangeFilters = $filters;
            unset($priceRangeFilters['price']);

            $priceRange = [];
            $priceRange[0] = $products->pluck($this->getUserPriceType())->min();
            $priceRange[1] = $products->pluck($this->getUserPriceType())->max();

            $makers = $category->products->unique(['maker']); 

            if($children->count() > 0)
            {
                foreach ($children as $child)
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

            $makers = $makers->unique(['maker']);

            // Parameters for category and all children categories

            $categoryParameters = $category->parameters;

            foreach($children as $child)
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
                
                $filterCounts['parameters']['makers'][$maker->maker] = $this->query($filterCountFilters,[], $children)->get()->count();
                
            }

            $filterValues = [];
            foreach ($categoryParameters as $categoryParameter)
            {
                $filterCounts['parameters'][$categoryParameter->id] = [];
                $filterValues[$categoryParameter->id] = [];
                $filterCountFilters = $filters;

                foreach ($categoryParameter->productParameters->unique('value') as $productParameter)
                {   
                    $filterCountFilters['parameters'][$categoryParameter->key] = $productParameter->value;
                    $filterCounts['parameters'][$categoryParameter->id][$productParameter->value] = $this->query($filterCountFilters, [$categoryParameter->key], $children)->get()->count();
                    unset($filterCountFilters['parameters'][$categoryParameter->key]);
                }
            }

            // get all parameter values

            $unfilteredProductsIds = $unfilteredProducts->pluck('id');
            $unfilteredParameters = ProductParameter::whereIn('product_id', $unfilteredProductsIds)->get();
            
            foreach($unfilteredParameters as $temp)
            {
                  if(!in_array($temp->value, $filterValues[$temp->parameter_id]))
                    {
                        array_push($filterValues[$temp->parameter_id], $temp->value);
                    }
            }

        }

        $data = [
            'makers' => $makers,
            'filters' => $categoryParameters,
            'filterValues' => $filterValues,
            'products' => $products,
            'activeFilters' => $activeFilters,
            'filterCounts' => $filterCounts,
            'priceRange' => $priceRange,
        ];

        return $data;
    }


     public function categoryCounts()
     {
        $categoryCounts = [];
        $categoryCounts['categories'] = [];

        foreach (Category::with(['children','children.products'])->withCount(['products'=>function($query){
             $query->where('active', '1');
        }])->get() as $category)
        {
            $categoryCounts['categories'][$category->id] = $category->products_count;

            if ($category->children->count() > 0)
            {
                foreach ($category->children as $child)
                {
                    $categoryCounts['categories'][$category->id] += $child->products->where('active',1)->count();
                }
            }
        }

        return $categoryCounts;
     }
 
}