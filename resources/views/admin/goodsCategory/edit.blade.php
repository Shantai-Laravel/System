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
    <div class="tab-area">
        @include('alerts')
        <ul class="nav nav-tabs nav-tabs-bordered">
            @if (!empty($langs))
                @foreach ($langs as $key => $tabLang)
                    <li class="nav-item">
                        <a href="#{{ $tabLang->lang }}" class="nav-link  {{ $key == 0 ? ' open active' : '' }}" data-target="#{{ $tabLang->lang }}">{{ $tabLang->descr }}</a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    <form class="form-reg"  role="form" method="POST" action="{{ urlForLanguage($lang, 'saveSubject/'.$item['ru']->itemId->id.'/1') }}" id="add-form" enctype="multipart/form-data">


@if (!empty($langs))
    @foreach ($langs as $key => $tabLang)

    <div class="tab-content {{ $key == 0 ? ' active-content' : '' }}" id={{ $tabLang->lang }}>
        {{-- Add new fields here --}}
        <div class="part left-part">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <ul>
                <li>
                    <label for="name-{{ $tabLang->lang }}">{{trans('variables.title_table')}}</label>
                    <input type="text" name="name[{{ $tabLang->lang }}]" class="name" id="name-{{ $tabLang->lang }}" data-lang="{{ $tabLang->lang }}" value="{{!is_null(old('name.'.$tabLang->lang)) ? old('name.'.$tabLang->lang) : $item[$tabLang->lang]->name}}">
                </li>
                <li>
                    <label for="descr-{{ $tabLang->lang }}">{{trans('variables.description')}}</label>
                    <textarea name="descr[{{ $tabLang->lang }}]" id="descr-{{ $tabLang->lang }}">{{ !is_null(old('descr.'.$tabLang->lang)) ? old('descr.'.$tabLang->lang) : $item[$tabLang->lang]->descr}}</textarea>
                </li>
                <li class="ckeditor">
                   <label for="body-{{ $tabLang->lang }}">{{trans('variables.body')}}</label>
                    <textarea name="body[{{ $tabLang->lang }}]" id="body-{{ $tabLang->lang }}" data-type="ckeditor">{{ !is_null(old('body.'.$tabLang->lang)) ? old('body.'.$tabLang->lang) : $item[$tabLang->lang]->body}}</textarea>
                    <script>
                        CKEDITOR.replace( 'body-{{ $tabLang->lang }}', {
                            language: '{{$lang}}',
                        } );
                    </script>
                </li>
            </ul>
        </div>

        {{-- required staic fields --}}
        <div class="part right-part">
            <ul>
                <input type="hidden" name="lang[{{ $tabLang->lang }}]" value="{{ $tabLang->id }}">
                <li>
                    <label for="slug.{{ $tabLang->lang }}">Slug</label>
                    <input type="text" name="slug[{{ $tabLang->lang }}]" class="slug" id="slug-{{ $tabLang->lang }}" value="{{  !is_null(old('slug.'.$tabLang->lang)) ? old('slug.'.$tabLang->lang) : $item[$tabLang->lang]->slug}}">
                </li>

                @if($groupSubRelations->save == 1)
                    <input type="submit" value="{{trans('variables.save_it')}}" data-form-id="add-form">
                @endif
            </ul>

            <ul>
                <hr><h6>Seo тексты</h6>
                <li>
                    <label for="meta_title-{{ $tabLang->lang }}">{{trans('variables.meta_title_page')}}</label>
                    <input type="text" name="meta_title[{{ $tabLang->lang }}]" id="meta_title-{{ $tabLang->lang }}" value="{{ !is_null(old('meta_title.'.$tabLang->lang)) ?old('meta_title.'.$tabLang->lang): $item[$tabLang->lang]->meta_title}}">
                </li>
                <li>
                    <label for="meta_keywords-{{ $tabLang->lang }}">{{trans('variables.meta_keywords_page')}}</label>
                    <input type="text" name="meta_keywords[{{ $tabLang->lang }}]" id="meta_keywords-{{ $tabLang->lang }}" value="{{ !is_null(old('meta_keywords.'.$tabLang->lang)) ? old('meta_keywords.'.$tabLang->lang) : $item[$tabLang->lang]->meta_keywords}}">
                </li>
                <li>
                    <label for="meta_description-{{ $tabLang->lang }}">{{trans('variables.meta_description_page')}}</label>
                    <input type="text" name="meta_descr[{{ $tabLang->lang }}]" id="meta_description-{{ $tabLang->lang }}" value="{{ !is_null(old('meta_descr.'.$tabLang->lang)) ? old('meta_descr.'.$tabLang->lang) : $item[$tabLang->lang]->meta_descr}}">
                </li>
            </ul>

            <ul>
                <hr><h6>Дополнительно</h6>
                <li>
                    <label for="img">{{trans('variables.img')}}</label>
                    <div class='file-div'>
                        <button class='btn btn-sm'>
                            <span class='glyphicon glyphicon-refresh-animate'>{{trans('variables.select_file')}}</span>
                        </button>
                        @if (!is_null(old('file-'.$tabLang->lang)))
                            <input type="hidden" name="file-{{ $tabLang->lang }}" data-url="{{url($lang, ['back', 'upload'])}}" path="{{$modules_name->src}}" value="upfiles/{{$modules_name->src}}/{{old('file-'.$tabLang->lang)}}" />

                            <a href="/upfiles/{{$modules_name->src}}/{{old('file-'.$tabLang->lang)}}" data-lightbox="image-2" data-title="{{ old('title.'.$tabLang->lang) }}">
                                <img src="/upfiles/{{$modules_name->src}}/{{old('file-'.$tabLang->lang)}}">
                            </a>
                        @elseif(strlen($item[$tabLang->lang]->img) > 0)
                            <input type="hidden" name="file-{{ $tabLang->lang }}" data-url="{{url($lang, ['back', 'upload'])}}" path="{{$modules_name->src}}" value="upfiles/{{$modules_name->src}}/{{$item[$tabLang->lang]->img}}" />

                            <a href="/upfiles/{{$modules_name->src}}/{{$item[$tabLang->lang]->img}}" data-lightbox="image-1" data-title="{{ $item[$tabLang->lang]->title }}">
                                <img src="/upfiles/{{$modules_name->src}}/{{$item[$tabLang->lang]->img}}">
                            </a>
                        @else
                            <input type="hidden" name="file-{{ $tabLang->lang }}" data-url="{{url($lang, ['back', 'upload'])}}" path="{{$modules_name->src}}" />
                        @endif
                    </div>
                </li>
            </ul>

        </div>
        </div>
    @endforeach
@endif
    </form>

@stop

@section('footer')
    <footer>
        @include('footer')
    </footer>
@stop
