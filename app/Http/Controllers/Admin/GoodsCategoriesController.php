<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsItem;
use App\Models\GoodsItemId;
use App\Models\GoodsSubject;
use App\Models\GoodsSubjectId;
use App\Models\ParameterId;
use App\Models\Parameter;
use App\Models\ParameterGoods;
use App\Models\Lang;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\GoodsPhoto;
use Illuminate\Support\Facades\File;
use View;


class GoodsCategoriesController extends Controller
{
    public function index()
    {
        $data = [];

        $categoriesId = GoodsSubjectId::orderBy('position', 'asc')->get();

        return view('admin.goodsCategory.show', $data);
    }

    public function change()
    {
        $list = Input::get('list');
        $positon = 1;

        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $positon++;
                GoodsSubjectId::where('id', $value['id']) ->update([ 'p_id' => 0, 'position' => $positon ]);

                if (array_key_exists('children', $value)) {
                    foreach ($value['children'] as $key1 => $value1) {
                        $positon++;
                        GoodsSubjectId::where('id', $value1['id']) ->update([ 'p_id' => $value['id'], 'position' => $positon ]);

                        if (array_key_exists('children', $value1)) {
                            foreach ($value1['children'] as $key2 => $value2) {
                                $positon++;
                                GoodsSubjectId::where('id', $value2['id']) ->update([ 'p_id' => $value1['id'], 'position' => $positon ]);

                                if (array_key_exists('children', $value2)) {
                                    foreach ($value2['children'] as $key3 => $value3) {
                                        $positon++;
                                        GoodsSubjectId::where('id', $value3['id']) ->update([ 'p_id' => $value2['id'], 'position' => $positon ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $langs = Lang::get();
        $view = 'admin.goodsCategory.edit';

        if (!empty($langs)) {
            foreach ($langs as $key => $lang) {
                $item_id = GoodsSubjectId::find($id);
                 $oneItem = GoodsSubject::where('lang_id', $lang->id)
                                    ->where('goods_subject_id', $id)
                                    ->first();
                if (!is_null($oneItem)) {
                    $item[$lang->lang] = $oneItem;
                }else{
                     GoodsSubject::create([ 'goods_subject_id' => $id, 'lang_id' => $lang->id ]);
                     $item[$lang->lang] = GoodsSubject::where('lang_id', $lang->id)
                                        ->where('goods_subject_id', $id)
                                        ->first();
                }
            }
        }

        return view($view, get_defined_vars());
    }

    public function delete($id)
    {
        $langs = Lang::get();
        $item = GoodsSubjectId::where('id', $id)->first();

        if (!is_null($item)) {
            GoodsSubjectId::where('id', $id)->delete();
            foreach ($langs as $key => $lang) {
                GoodsSubject::where('id', $item->id)->delete();
            }
        }

        return redirect()->back();
    }

    public function saveSubject($id, $updated_lang_id)
    {
        $langs = Input::get('lang');

        if (is_null($id)) {
            $data = [
                'title' => Input::get('title'),
                'slug' =>  Input::get('slug')
            ];

            $validate = Validator::make($data, [
                'title.*' => 'required',
                'slug.*' => 'required|unique:pages,slug'
            ]);
        }else{
            $validate = Validator::make(Input::all(), [
                'title.*' => 'required',
                'slug.*' => 'required'
            ]);
        }

        if($validate->fails()){
            Session::flash('errors', $validate->errors()->all());
            return redirect()->back()->withInput();
        }

        $maxPosition = GetMaxPosition('pages_id');

        if(is_null($id)){
            $data = [
                'alias' => str_slug(Input::get('title')['ru']),
                'position' => $maxPosition + 1,
                'active' => 1,
            ];

            $modelId = GoodsSubjectId::create($data);


            if (!empty($langs)) {
                foreach ($langs as $key => $lang) {
                    $key ++;
                    $data = array_filter([
                        'goods_subject_id' => $modelId->id,
                        'lang_id' => $key,
                        'name' => Input::get('title')[$lang],
                        'slug' => Input::get('slug')[$lang],
                    ]);

                    GoodsSubject::create($data);
                }
            }

            Session::flash('success', 'Элемент добавлен!');
            return redirect('ru/back/categories');
        }
        else {
            $modelId = GoodsSubjectId::find($id);

            $data = array_filter([
                'alias' => str_slug(Input::get('title')['ru']),
            ]);

            $modelIdUploaded = GoodsSubjectId::where('id', $id)
                                        ->update($data);

            if (!empty($langs)) {
                foreach ($langs as $key => $lang) {
                    $img = basename(Input::get('file-'.$key));

                    $data = array_filter([
                        'lang_id' => $lang,
                        'name' => Input::get('name')[$key],
                        'descr' => Input::get('descr')[$key],
                        'slug' => Input::get('slug')[$key],
                        'body' => Input::get('body')[$key],
                        'img' => $img,
                        'meta_title' => Input::get('meta_title')[$key],
                        'meta_keywords' => Input::get('meta_keywords')[$key],
                        'meta_descr' => Input::get('meta_descr')[$key]
                    ]);

                    GoodsSubject::where('goods_subject_id', $id)
                        ->where('lang_id', $lang)
                        ->update($data);
                }
            }
        }
        Session::flash('success', 'Информация обновлена!');
        return redirect()->back();
    }


    public function saveSubject1($id, $updated_lang_id)
    {
        dd(Input::all());
        if(is_null($id)){
            $item = Validator::make(Input::all(), [
                'name' => 'required',
                'alias' => 'required|unique:goods_subject_id',
            ]);
        }

        if($item->fails()){
            return response()->json([
                'status' => false,
                'messages' => $item->messages(),
            ]);
        }

        $maxPosition = GetMaxPosition('goods_subject_id');
        $level = GetLevel(Input::get('p_id'), 'goods_subject_id');

        if(is_null($id)){
            $data = [
                'p_id' => Input::get('p_id'),
                'level' => $level + 1,
                'alias' => Input::get('alias'),
                'position' => $maxPosition + 1,
                'active' => 1,
                'deleted' => 0,
            ];

            $subject_id = GoodsSubjectId::create($data);

            $data = array_filter([
                'goods_subject_id' => $subject_id->id,
                'lang_id' => Input::get('lang'),
                'name' => Input::get('name'),
                'body' => Input::get('body'),
                'page_title' => Input::get('title'),
                'h1_title' => Input::get('h1_title'),
                'meta_title' => Input::get('meta_title'),
                'meta_keywords' => Input::get('meta_keywords'),
                'meta_description' => Input::get('meta_description')
            ]);

            GoodsSubject::create($data);

        }
    }
}
