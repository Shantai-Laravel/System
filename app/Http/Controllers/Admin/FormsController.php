<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormTranslation;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Lang;
use App\Models\Widget;
use App\Models\WidgetId;
use App\Models\Form;
use View;
use DB;


class FormsController extends Controller
{
    public $createView = 'admin.forms.create';
    public $showView = 'admin.forms.show';
    public $editView = 'admin.forms.edit';

    public $foreignKey = 'form_id';
    public $modelTrans = 'App\Models\FormTranslation';
    public $model = 'App\Models\Form';

    public function index()
    {
        $forms = Form::with(['translation' => function($query) {
            $query->where('lang_id', $this->lang);
        }])->get();

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $form = new Form();
        $form->short_code = $request->short_code;
        $form->save();

        foreach ($this->langs as $lang) :
            $form->translation()->create([
                'lang_id' => $lang->id,
                'title' => request('title_' . $lang->lang),
                'description' => request('body_' . $lang->lang),
                'code' => request('build_wrap_' . $lang->lang)
            ]);
        endforeach;

        Session::flash('message', 'New item has been created!');

        return redirect()->route('forms.index');
    }

    public function edit($id) {

        $form = Form::with('translation')->findOrFail($id);

        return view('admin.forms.edit', compact('form'));
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
                $this->model::where('id', $id)->update(['position' => $i]);
                $i++;
            }
        }
    }

    // ajax response for active
    public function changeActive()
    {
        $active = Input::get('active');
        $id = Input::get('id');

        if ($active == 1) {
            $change_active = 0;
        } else {
            $change_active = 1;
        }

        $this->model::where('id', $id)->update(['active' => $change_active]);

    }



    public function addWidget($contents)
    {
        $wigetsId = WidgetId::get();
        $content_new = $contents;

        if (!empty($wigetsId)) {
            foreach ($wigetsId as $key => $widgetId) {
                if (!empty($contents)) {
                    foreach ($contents as $key => $content) {
                        echo "[" . $widgetId->short_code . "]";
                        $lang = Lang::where('lang', $key)->first();
                        $widget = Widget::where('lang_id', $lang->id)->where('widget_id', $widgetId->id)->first();
                        if (!is_null($widget)) {
                            if (strpos($content, "[$widgetId->short_code]") == true) {
                                // $content_new[$key] = str_replace("[". $widgetId->short_code ."]", 'vdf,l', '<p>[short_code1]</p>');
                                $content_new[$key] = str_replace("[$widgetId->short_code]", $widget->content, $content);
                            }
                            // else{
                            //     $content_new[$key] = $content;
                            // }

                        }
                    }
                }
            }
        }

        // dd($content_new);
        return $content_new;
    }



    public function update(Request $request)
    {
        dd($request->all());
    }

    // save post (update or create)
    public function save($id, $updated_lang_id)
    {
        $langs = Input::get('lang');


        if (is_null($id)) {
            $data = [
                'title' => Input::get('title'),
            ];

            $validate = Validator::make($data, [
                'title.*' => 'required',
            ]);
        } else {
            $validate = Validator::make(Input::all(), [
                'title.*' => 'required',
            ]);
        }

        if ($validate->fails()) {
            // dd($validate->errors()->all());
            Session::flash('errors', $validate->errors());
            return redirect()->back()->withInput();
        }

        $maxPosition = GetMaxPosition('pages_id');

        if (is_null($id)) {
            $data = [
                'short_code' => Input::get('short_code')['ru'],
                'type' => Input::get('JSON')
            ];

            $modelId = $this->model::create($data);

            if (!empty($langs)) {
                foreach ($langs as $key => $lang) {
                    $data = array_filter([
                        $this->foreignKey => $modelId->id,
                        'lang_id' => $lang,
                        'title' => Input::get('title')[$key],
                    ]);

                    $this->modelTrans::create($data);
                }
            }

            Session::flash('success', 'Элемент добавлен!');
            return redirect('ru/back/pages');
        } else {
            // dd(Input::all());

            $modelId = $this->model::find($id);
            $data = array_filter([
                'short_code' => Input::get('short_code')['ru'],
                'type' => Input::get('JSON')
            ]);

            $modelIdUploaded = $this->model::where('id', $id)
                ->update($data);

            if (!empty($langs)) {
                foreach ($langs as $key => $lang) {
                    $img = basename(Input::get('file-' . $key));

                    $data = array_filter([
                        'lang_id' => $lang,
                        'title' => Input::get('title')[$key],
                    ]);

                    $this->modelTrans::where($this->foreignKey, $id)
                        ->where('lang_id', $lang)
                        ->update($data);
                }
            }
        }
        Session::flash('success', 'Информация обновлена!');
        return redirect()->back();
    }


    public function delete($id)
    {
        $itemId = $this->model::findOrFail($id);
        if (!is_null($itemId)) {
            $items = $this->modelTrans::where($this->foreignKey, $itemId->id)->get();
            if (!empty($items)) {
                foreach ($items as $key => $item) {
                    if (File::exists('upfiles/' . $this->menu()['modules_name']->src . '/s/' . $item->img))
                        File::delete('upfiles/' . $this->menu()['modules_name']->src . '/s/' . $item->img);

                    if (File::exists('upfiles/' . $this->menu()['modules_name']->src . '/m/' . $item->img))
                        File::delete('upfiles/' . $this->menu()['modules_name']->src . '/m/' . $item->img);

                    if (File::exists('upfiles/' . $this->menu()['modules_name']->src . '/' . $item->img))
                        File::delete('upfiles/' . $this->menu()['modules_name']->src . '/' . $item->img);

                    $this->modelTrans::where('id', $item->id)->delete();
                }
                $this->model::where('id', $id)->delete();
                Session::flash('success', 'Элемент "' . $item->title . '" удален!');
            }
        }

        return redirect()->back();
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
