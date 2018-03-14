@extends('app')
@include('nav-bar')
@include('left-menu')
@section('content')

@include('speedbar')
@include('list-elements', [
    'actions' => [
        trans('variables.elements_list') => urlForFunctionLanguage($lang, ''),
        trans('variables.add_element') => urlForFunctionLanguage($lang, 'item/create'),
    ]
])

<div class="list-content">
{{--    <form class="form-reg"  role="form" method="POST" action="{{ urlForLanguage($lang, 'save/'.$module->id.'/'.$edited_lang_id) }}" id="add-form" enctype="multipart/form-data">--}}
    <form class="form-reg"  role="form" method="POST" action="" id="add-form" enctype="multipart/form-data">

    {{-- Add new fields here --}}
    <div class="part left-part">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <ul>

            @foreach($lang_list as $lang_key => $one_lang)
            <li>
                <label for="name">{{trans('variables.title_table')}} {{ $one_lang->lang }}</label>
                <input type="text"
                       name="name_{{ $one_lang->lang }}"
                       id="name_{{ $one_lang->lang }}"
{{--                       value="{{ $item->{'name_'.$one_lang->lang} }}">--}} />
            </li>
            {{--<li>--}}
                {{--<label for="descr">{{trans('variables.description')}} {{ $one_lang->lang }}</label>--}}
                {{--<textarea name="descr_{{ $one_lang->lang }}" --}}
                          {{--id="descr_{{ $one_lang->lang }}">{{ $item->{'descr_'.$one_lang->lang} }}</textarea>--}}
            {{--</li>--}}
            @endforeach


        </ul>
    </div>

    {{-- required staic fields --}}
    <div class="part right-part">
        <ul>
            <li>
                <label for="src">Link</label>
                <input type="text" name="src" id="src" value="{{ $module->src }}">
            </li>
            <li>
                <label for="src">Controller</label>
                <input type="controller" name="controller" id="controller" value="{{ $module->controller }}">
            </li>
            <li>
                <label for="table_name">Table name</label>
                <input type="text" name="table_name" id="table_name" value="{{ $module->table_name }}">
            </li>
            <li>
                <label for="icon">Icon</label>
                <input type="text" name="icon" id="icon" value="{{ $module->icon }}">
            </li>
            <div class="text-center alert alert-warning">
                <a target="_blank" class="pull-center" href="http://fontawesome.io/icons/">Font awesome</a>
            </div>

            <input type="submit" value="{{trans('variables.save_it')}}" onclick="saveForm(this)" data-form-id="add-form">
        </ul>
    </div>
    </form>
</div>
@stop

@section('footer')
    <footer>
        @include('footer')
    </footer>
@stop
