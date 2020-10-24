<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        
        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {   
        // añade la relación con duplicados
        // $product->categories()->attach($category->id);

        // sincroniza con el id y borra otras relaciones si existen
        // $product->categories()->sync($category->id);

        // Añade la relación y si ya existe no duplica
        $product->categories()->syncWithoutDetaching($category->id);

        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)){
            return $this->errorResponse('La categoría no está asociada a este producto', 404);
        }

        $product->categories()->detach($category->id);

        return $this->showAll($product->categories);

    }
}
