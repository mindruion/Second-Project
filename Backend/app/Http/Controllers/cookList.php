<?php

namespace App\Http\Controllers;

use App\cook;
use App\Http\Resources\mainCollection;
use App\Recipe;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;


class cookList extends Controller
{
    protected $user;
    /**
     * Display a listing of the resource.
     *
     * @return mainCollection
     */
    public function index()
    {
        $lists = [];
        $cooks = cook::all();
        foreach ($cooks as $cook) {
            $list = [
                'id' => $cook->id,
                'title' => $cook->title,
                'photo' => $cook->image_path,
                'recipe' => $cook->recipe->description
            ];
            $lists[] = $list;
        }
        $collection = $this->paginate($lists, $perPage = 6, $page = null, $options = []);
        return new mainCollection($collection);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mainCollection
     */
    public function store(Request $request)
    {
        $cook = new cook();
        $cook->title = $request->title;
        $file = $request->file('image_path');
        if($file) {
            $path = $file->store('images','public');
            $cook->image_path = asset('storage/' . $path);
        }
        $recipe = new Recipe();
        $recipe->description = $request->recipe;
        $recipe->save();
        $cook->recipe_id = $recipe->id;
        $cook->price = $request->price;
        if($cook->save()){
            return response()->json([
                'success' => true,
                'cooks' => $cook,
            ]);
        }
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be added',
            ], 500);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cook = cook::find($id);


        if (!$cook) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, cook with id ' . $id . ' cannot be found',
            ], 400);
        }
        if ($request->title){
            $cook->title = $request->title;
            $updated = $cook->save();
        }
        $file = $request->file('imagePath_id');
        if($file) {
            $name = time() . $file->getClientOriginalName();
            $file->move('imagesIon', $name);
            $myFile = 'imagesIon/' . $cook->photos->path;
            File::delete($myFile);
            $photo = Photo::find($cook->imagePath_id);
            $photo->path = $name;
            $updated = $photo->save();
        }
        if($request->recipe){
            $recipe = Recipe::find($cook->recipe_id);
            $recipe->description = $request->recipe;
            $updated = $recipe->save();
        }
        if ($updated) {
            return response()->json([
                'success' => true,
                'cook' => $cook
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, cook could not be updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cook = cook::find($id);
        if(!$cook){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, cook with' . $id . 'can not be find'
            ]);
        }
        if ($cook->delete()){
            return response()->json([
                'success' => true,
                'message' => 'cook was successfully deleted'
            ]);
        }
        else {
            return response()->json([
               'success' => false,
               'message' => 'cook could not be deleted'
            ]);
        }
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
