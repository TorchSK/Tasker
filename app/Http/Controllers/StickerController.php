<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;

use File;
use App\Sticker;
use Image;

class StickerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function update($id, Request $request)
    {
        $sticker = Sticker::find($id);

        foreach ($request->except('_token') as $key => $value)
        {
            $sticker->$key = $value;
        }

        $sticker->save();

        return $sticker;
    }

    public function store(Request $request)
    {
        if(!$request->has('do_not_upload'))
        {
            $file = $request->file('file');
            $destinationPath = 'uploads/files';
            $extension = $file->getClientOriginalExtension(); 
            $filename = $file->getClientOriginalName();
            $fullpath = $destinationPath.'/'.$filename;

            $success = $file->move($destinationPath, $filename);


            if ($success)
            {
                $sticker = new Sticker();
                $sticker->path = $fullpath;
                $sticker->width = 70;
                $sticker->save();
            }

            return $sticker;

        }
        else
        {
            $sticker = new Sticker();
            $sticker->path = $request->get('path');
            $sticker->width = 70;
            $sticker->save();

            return $sticker;
        }

    }

    public function changeCatalogueImage(Request $request)
    {
        $catalogue = FileDB::find($request->get('catalogue_id'));
        
        $file = $request->file('file');
        $destinationPath = 'uploads/files';
        $extension = $file->getClientOriginalExtension(); 
        $filename = $file->getClientOriginalName();
        $fullpath = $destinationPath.'/'.$filename;

        $success = $file->move($destinationPath, $filename);

        if ($success)
        {
            $image = Image::make($destinationPath.'/'.$filename);

            $image->resize(100, null , function ($constraint) {
                    $constraint->aspectRatio();
            });
            
            $image->save($destinationPath.'/'.$filename);

            $thumb = new FileDB();
            $thumb->type = "thumb";
            $thumb->path = $fullpath;

            $thumb->save();
            $catalogue->thumb_id = $thumb->id;
            $catalogue->save();

        }

        return 1;


    }

    public function stickerAttach(Request $request)
    {
        $products = $request->get('products');
        $stickers = $request->get('stickers');

        foreach($products as $productId)
        {
            $product = Product::find($productId);
            $product->stickers()->attach($stickers);
        }
          
    }


    public function removeStickerFromProduct($productid, $stickerid)
    {
        $product = Product::find($productid);
        $sticker = Sticker::find($stickerid);

        $product->stickers()->detach($sticker);
    }

    public function destroy($id)
    {
        $sticker = Sticker::find($id);
        $sticker->delete();
    }

}
