@extends('app')
@include('nav-bar')
@include('left-menu')
@section('content')

    @include('speedbar')

    @include('list-elements', [
        'actions' => [
            trans('variables.elements_list') => route('forms.index'),
            trans('variables.add_element') => route('forms.create'),
        ]
    ])

    <div class="list-content">


        <div class="tab-area">
            @include('alerts')
            <ul class="nav nav-tabs nav-tabs-bordered">
                @if (!empty($langs))
                    @foreach ($langs as $key => $tabLang)
                        <li class="nav-item">
                            <a href="#{{ $tabLang->lang }}" class="nav-link  {{ $key == 0 ? ' open active' : '' }}"
                               data-target="#{{ $tabLang->lang }}">{{ $tabLang->descr }}</a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <form class="form-reg" role="form" method="POST" action="" id="add-form" enctype="multipart/form-data">

            @if (!empty($langs))
                @foreach ($langs as $key => $tabLang)
                    @if ($key == 0)

                        <div class="tab-content {{ $key == 0 ? ' active-content' : '' }}" id={{ $tabLang->lang }}>

                            <div class="part full-part">
                                <ul>
                                    <li>
                                        <label for="name-{{ $tabLang->lang }}">{{trans('variables.title_table')}}</label>
                                        <input type="text" name="title[{{ $tabLang->lang }}]" class="name"
                                               id="title-{{ $tabLang->lang }}" data-lang="{{ $tabLang->lang }}">
                                    </li>
                                    <li>
                                        <label for="slug.{{ $tabLang->lang }}">Short-code</label>
                                        <input type="text" name="slug[{{ $tabLang->lang }}]" class="slug"
                                               id="slug-{{ $tabLang->lang }}">
                                    </li>
                                </ul>
                                <hr>

                                <div id="" class="build-wrap"></div>


                                @if (!empty($item[$tabLang->lang]->itemId->type))
                                    <button id="showData" type="button">Show Data</button>
                                    <button id="clearFields" type="button">Clear All Fields</button>
                                    <button id="getData" type="button">Get Data</button>
                                    <button id="getXML" type="button">Get XML Data</button>
                                    <button id="getJS" type="button">Get JS Data</button>

                                    <button id="setData" type="button">Set Data</button>

                                    <button id="addField" type="button">Add Field</button>
                                    <button id="removeField" type="button">Remove Field</button>
                                    <button id="testSubmit" type="submit">Test Submit</button>
                                    <button id="resetDemo" type="button">Reset Demo</button>
                                @endif
                                <ul>
                                    <li>
                                        <br>
                                        <input type="hidden" id="JSON" name="JSON">
                                    </li>
                                    <ul>
                                        <input type="hidden" name="lang[{{ $tabLang->lang }}]"
                                               value="{{ $tabLang->id }}">
                                        <input type="submit" id="getJSON" value="{{trans('variables.save_it')}}"
                                               data-form-id="add-form">
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    @endif

                @endforeach
            @endif
        </form>
    </div>





    <script src="{{  asset('js/formBuilder/vendor.js') }}"></script>
    <script src="{{  asset('js/formBuilder/form-builder.min.js') }}"></script>
    <script src="{{  asset('js/formBuilder/form-render.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.1/jquery.rateyo.min.js"></script>
    <script src="{{  asset('js/formBuilder/demo.js') }}"></script>

        @stop

        @section('footer')
            <footer>
                @include('footer')
            </footer>
@stop
