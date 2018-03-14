@extends('app')
@include('nav-bar')
@include('left-menu')
@section('content')

@include('speedbar')

@include('list-elements', [
    'actions' => [
        trans('variables.elements_list') => urlForFunctionLanguage($lang, ''),
    ]
])

@include('alerts')


<div class="list-content">
    <form class="form-reg" role="form" method="POST" action="{{ route('languages.store') }}" id="add-form">
      {{ csrf_field() }}

    <div class="part full-part">

        <ul>
            <li>
                <label for="name">{{trans('variables.title_table')}}</label>
                <input type="text" name="name" id="name" placeholder="en" value="{{ old('name')}}">
            </li>
            <li>
                <label for="description">{{ trans('variables.description') }}</label>
                <input type="text" name="description" id="description" placeholder="English" value="{{ old('description')}}">
            </li>

            <input type="submit" value="{{trans('variables.save_it')}}" data-form-id="add-form">




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
