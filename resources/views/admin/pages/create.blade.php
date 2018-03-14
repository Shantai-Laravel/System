@extends('app')
@include('nav-bar')
@include('left-menu')
@section('content')

    @include('speedbar')

    @include('list-elements', [
        'actions' => [
            trans('variables.elements_list') => route('pages.index'),
            trans('variables.add_element') => route('pages.create'),
        ]
    ])



    <div class="list-content">

        <form class="form-reg" role="form" method="POST" action="{{ route('pages.store') }}" id="add-form"
              enctype="multipart/form-data">
            {{ csrf_field() }}

            <ul>
                <li>
                    <label for="alias">Alias</label>
                    <input id="alias" type="text" name="alias">
                </li>
                <li>
                    <label for="">Active</label>
                    <div  style="display: flex; align-items: center; float: left;">
                        <input type="radio" name="active" value="1">Yes
                        <input type="radio" name="active" value="0">No
                    </div>
                </li>
                <li>
                    <label for="position">Position</label>
                    <input id="position" type="text" name="alias">
                </li>
            </ul>

            <div class="tab-area">
                @include('alerts')
                <ul class="nav nav-tabs nav-tabs-bordered">
                    @if (!empty($langs))
                        @foreach ($langs as $lang)
                            <li class="nav-item">
                                <a href="#{{ $lang->lang }}" class="nav-link  {{ $loop->first ? ' open active' : '' }}"
                                   data-target="#{{ $lang->lang }}">{{ $lang->descr }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>


            @if (!empty($langs))
                @foreach ($langs as $lang)
                    <div class="tab-content {{ $loop->first ? ' active-content' : '' }}" id={{ $lang->lang }}>

                        <div class="part left-part">
                            <ul>
                                <li>
                                    <label for="title-{{ $lang->lang }}">{{trans('variables.title_table')}}</label>
                                    <input type="text" name="title_{{ $lang->lang }}" id="title-{{ $lang->lang }}">
                                </li>
                                <li>
                                    <label for="description-{{ $lang->lang }}">{{trans('variables.description')}}</label>
                                    <textarea name="description_{{ $lang->lang }}"
                                              id="description-{{ $lang->lang }}"></textarea>
                                </li>
                                <li class="ckeditor">
                                    <label for="body-{{ $lang->lang }}">{{trans('variables.body')}}</label>
                                    <textarea name="body_{{ $lang->lang }}" id="body-{{ $lang->lang }}"
                                              data-type="ckeditor"></textarea>
                                    <script>
                                        CKEDITOR.replace('body-{{ $lang->lang }}', {
                                            language: '{{$lang}}',
                                        });
                                    </script>
                                </li>
                            </ul>
                        </div>

                        <div class="part right-part">
                            <ul>
                                <input type="hidden" name="lang[{{ $lang->lang }}]" value="{{ $lang->id }}">
                                <li>
                                    <label for="slug.{{ $lang->lang }}">Slug</label>
                                    <input type="text" name="slug_{{ $lang->lang }}" class="slug"
                                           id="slug-{{ $lang->lang }}">
                                </li>
                                <input type="submit" value="{{trans('variables.save_it')}}" data-form-id="add-form">
                            </ul>

                            <ul>
                                <hr>
                                <h6>Seo тексты</h6>
                                <li>
                                    <label for="meta_title_{{ $lang->lang }}">{{trans('variables.meta_title_page')}}</label>
                                    <input type="text" name="meta_title_{{ $lang->lang }}"
                                           id="meta_title_{{ $lang->lang }}">
                                </li>
                                <li>
                                    <label for="meta_keywords_{{ $lang->lang }}">{{trans('variables.meta_keywords_page')}}</label>
                                    <input type="text" name="meta_keywords_{{ $lang->lang }}"
                                           id="meta_keywords_{{ $lang->lang }}">
                                </li>
                                <li>
                                    <label for="meta_description_{{ $lang->lang }}">{{trans('variables.meta_description_page')}}</label>
                                    <input type="text" name="meta_description_{{ $lang->lang }}"
                                           id="meta_description_{{ $lang->lang }}">
                                </li>
                            </ul>

                            <ul>
                                <hr>
                                <h6>Дополнительно</h6>
                                <li>
                                    <label for="img-{{ $lang->lang }}">{{trans('variables.img')}}</label>
                                    <div class='file-div'>
                                        @if ((old('file-'.$lang->lang)))
                                            <input type="hidden" name="file-{{ $lang->lang }}"
                                                   data-url="{{url($lang, ['back', 'upload'])}}"
                                                   path="{{$modules_name->src}}"
                                                   value="{{old('file-'.$lang->lang)}}"/>

                                            <a href="{{ asset(old('file-'.$lang->lang)) }}" data-lightbox="image-2"
                                               data-title="{{ old('title.'.$lang->lang) }}">
                                                <img src="{{ asset(old('file-'.$lang->lang)) }}">
                                            </a>
                                        @else
                                            <button class='btn btn-sm'>
                                                <span class='glyphicon glyphicon-refresh-animate'>{{trans('variables.select_file')}}</span>
                                            </button>
                                            <input type="hidden" name="file-{{ $lang->lang }}"
                                                   data-url="{{url($lang, ['back', 'upload'])}}"
                                                   id="img-{{ $lang->lang }}"/>
                                        @endif
                                    </div>

                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            @endif
        </form>
    </div>

@stop

@section('footer')
    <footer>
        @include('footer')
    </footer>
@stop
