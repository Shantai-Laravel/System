@extends('app')
@include('nav-bar')
@include('left-menu')

@section('content')
@include('speedbar')

@include('list-elements', [
    'actions' => [
        trans('variables.elements_list') => urlForFunctionLanguage($lang, '')
    ]
])
@include('alerts')


<div class="list-content">
    <div class="part left-part min-height">
        <h6>Список категории</h6><hr>
        <div id="container">
          <hr>
        </div>

        <div class="dd" id="nestable-output">
            {{-- {{ dd(SelectGoodsCatsTree($lang_id, 0, $curr_id=null)) }} --}}
            {!! SelectGoodsCatsTree($lang_id, 0, $curr_id=null) !!}
        </div>

        <script>

        $('#nestable-output').nestable();

        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            }
        });

        $(document).ready(function () {
        var updateOutput = function (e) {
            var list = e.length ? e : $(e.target), output = list.data('output');

            $.ajax({
                method: "POST",
                url: "/ru/back/categories/xsa/change",
                data: {
                    list: list.nestable('serialize')
                }
            }).fail(function(jqXHR, textStatus, errorThrown){
                alert("Unable to save new list order: " + errorThrown);
            });
        };

        $('#nestable-output').nestable({
            group: 1,
            maxDepth: 7,
        }).on('change', updateOutput);
    });

        $('#container').on("changed.jstree", function (e, data) {
            console.log("The selected nodes are:");
            console.log(data.selected);
          });

        </script>
    </div>
    <div class="right-part">

        <ul class="nav nav-tabs nav-tabs-bordered">
            <h6>Добавить категорию</h6>
            @if (!empty($langs))
                @foreach ($langs as $key => $tabLang)
                    <li class="nav-item">
                        <a href="#{{ $tabLang->lang }}" class="nav-link  {{ $key == 0 ? ' open active' : '' }}" data-target="#{{ $tabLang->lang }}">{{ $tabLang->descr }}</a>
                    </li>
                @endforeach
            @endif
        </ul>


        <form class="form-reg"  role="form" method="POST" action="{{ url($lang.'/back/categories/xsa/saveSubject') }}" id="add-form" enctype="multipart/form-data">
        @if (!empty($langs))
            @foreach ($langs as $key => $tabLang)
                <div class="tab-content part {{ $key == 0 ? ' active-content' : '' }}" id={{ $tabLang->lang }}>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <ul>
                        <input type="hidden" name="lang[]" value="{{ $tabLang->lang }}">
                        <li>
                            <label for="name-{{ $tabLang->lang }}">{{trans('variables.title_table')}}</label>
                            <input type="text" name="title[{{ $tabLang->lang }}]" class="name" id="title-{{ $tabLang->lang }}" data-lang="{{ $tabLang->lang }}" value="{{!is_null(old('title.'.$tabLang->lang)) ? old('title.'.$tabLang->lang) : ''}}">
                        </li>
                        <li>
                            <label for="p_id">Родитель</label>
                            <select name="p_id" id="p_id">
                                <option value="0" >{{trans('variables.home')}}</option>
                                {!! SelectGoodsSubjectTree($tabLang->id, 0 ,0) !!}
                            </select>
                        </li>
                        <li>
                            <label for="slug.{{ $tabLang->lang }}">Сылка</label>
                            <input type="text" name="slug[{{ $tabLang->lang }}]" class="slug" id="slug-{{ $tabLang->lang }}" value="{{ !is_null(old('slug.'.$tabLang->lang)) ? old('slug.'.$tabLang->lang) : ''}}">
                        </li>
                        @if($groupSubRelations->save == 1)
                            <input type="submit" value="{{trans('variables.save_it')}}" data-form-id="add-form">
                        @endif
                    </ul>
                </div>
            @endforeach
        @endif
        </form>
    </div>
</div>

@stop

@section('footer')
<footer>
    @include('footer')
</footer>
@stop
