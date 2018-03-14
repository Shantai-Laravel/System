<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lang;
use App\Models\Module;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use DB;

class ModulesController extends Controller
{
    public function index()
    {
        $rows = Module::with('translation')->orderBy('position', 'asc')->get();

        return view('admin.modules.index', get_defined_vars());
    }

    // ajax response for position
    public function changePosition()
    {
        $neworder = Input::get('neworder');
        $i = 1;
        $neworder = explode("&", $neworder);

        foreach ($neworder as $k => $v) {
            $id = str_replace("tablelistsorter[]=", "", $v);
            if (!empty($id)) {
                Module::where('id', $id)->update(['position' => $i]);
                $i++;
            }
        }
    }

    public function create()
    {
        return view('admin.modules.create');
    }

    public function edit($id)
    {
        $module = Module::with('translation')->findOrFail($id);

        return view('admin.modules.edit', compact('module'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'src' => 'required',
            'controller' => 'required',
            'table_name' => 'required'
        ]);

        $maxPosition = GetMaxPosition('modules');

        $module = new Module();
        $module->src = $request->src;
        $module->controller = $request->controller;
        $module->position = $maxPosition;
        $module->table_name = $request->table_name;
        $module->icon = $request->icon;
        $module->save();

        foreach ($this->langs as $lang) :
            $module->translation()->create([
                'lang_id' => $lang->id,
                'name' => request('name_' . $lang->lang),
                'description' => request('descr_' . $lang->lang)
            ]);
        endforeach;

        Session::flash('message', 'New item has been created!');

        return redirect()->route('modules.index');
    }

    public function update(Request $request) {

    }

    // save post (update or create)
//    public function save($id, $updated_lang_id)
//    {
////        dd(request()->all());
//
//        $validate = Validator::make(Input::all(), [
//            'src' => 'required',
//            'controller' => 'required',
//            'table_name' => 'required',
//        ]);
//
//        if($validate->fails()){
//            return response()->json([
//                'status' => false,
//                'messages' => $validate->messages(),
//            ]);
//        }
//
//        $maxPosition = GetMaxPosition('modules');
//
//        if(is_null($id)){
//
//            $module = new Module();
//            $module->src = request('src');
//            $module->controller = request('controller');
//            $module->position = $maxPosition;
//            $module->table_name = request('table_name');
//            $module->save();
//
//            foreach ($this->langs as $lang) :
//                $module->translation()->create([
//                   'lang_id' => $lang->id,
//                   'name' => request('name_'. $lang->lang),
//                   'description' => request('descr_'.$lang->lang)
//                ]);
//            endforeach;
//
//
////            $module->translation()->create()
//
//
//        }
//        else {
//            $model = $this->model::find($id);
//
//            $data = array_filter([
//                'name_ru' => Input::get('name_ru'),
//                'name_ro' => Input::get('name_ro'),
//                'name_en' => Input::get('name_en'),
//                'descr_ru' => Input::get('descr_ru'),
//                'descr_ro' => Input::get('descr_ro'),
//                'descr_en' => Input::get('descr_en'),
//                'controller' => Input::get('controller'),
//                'src' => Input::get('src'),
//                'table_name' => Input::get('table_name'),
//                'icon' => Input::get('icon'),
//            ]);
//
//            $modelUploaded = $this->model::where('id', $id)
//                                        ->update($data);
//        }
//
//        if(is_null($id)){
//            return response()->json([
//                'status' => true,
//                'messages' => ['Save'],
//                'redirect' => urlForFunctionLanguage($this->lang()['lang'], '')
//            ]);
//        }
//        return response()->json([
//            'status' => true,
//            'messages' => ['Updated'],
//            'redirect' => urlForLanguage($this->lang()['lang'], 'edit/'.$id.'/'.$updated_lang_id)
//        ]);
//    }


    public function destroy($id) {

        Module::findOrFail($id)->delete();

        Session::flash('message', 'Item was successful deleted! ');

        return redirect()->route('modules.index');
    }

    public function getTableCols($table)
    {
        $colums = [];
        $tables = ['tablename'];

        foreach ($tables as $table) {
            $table_info_columns = \DB::select(DB::raw('SHOW COLUMNS FROM' . $table));

            foreach ($table_info_columns as $key => $column) {
                if (($column->Field !== 'id') && ($column->Field !== 'created_at') && ($column->Field !== 'updated_at')) {
                    $colums[$key][] = $column->Field;
                    $colums[$key][] = $column->Type;
                }
            }
        }
        return $colums;
    }

}
