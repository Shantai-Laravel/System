@extends('app')
@include('nav-bar')
@include('left-menu')

@section('content')
    @include('speedbar')

    @include('list-elements', [
        'actions' => [
            trans('variables.elements_list') => route('pages.index'),
            trans('variables.add_element') =>  route('pages.create')
        ]
    ])
    @include('alerts')

    @if(!$pages->isEmpty())
        <table class="el-table" id="tablelistsorter">
            <thead>
            <tr>
                <th>{{trans('variables.title_table')}}</th>
                <th>Создано</th>
                <th>{{trans('variables.position_table')}}</th>
                <th>{{trans('variables.active_table')}}</th>
                <th>{{trans('variables.edit_table')}}</th>
                <th>{{trans('variables.delete_table')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pages as $page)

                {{ $page->translation->title }}

                <tr id="{{ $page->id }}">
                    <td>
                        {{ $page->tranlation->title ?? trans('variables.another_name') }}
                    </td>
                    <td>{{ $page->created_at->format('d/m/Y') }}</td>
                    <td class="dragHandle" nowrap style="cursor: move;">
                        <a class="top-pos" href=""></a>
                        <a class="bottom-pos" href=""></a>
                    </td>
                    <td>
                        <a href="" class="change-active {{ $page->active == 1 ? '' : 'negative' }}"
                           active="{{ $page->active }}"
                           element-id="{{$page->translation->id}}">{{ $page->active == 1 ? '+' : '-' }}</a>
                    </td>
                    <td>
                        <a href="{{ route('pages.edit', $page->id) }}">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                    @if(empty(IfHasChild($page->translation->id, 'menu_id')))
                        <td class="destroy-element">
                            <a href="{{ route('pages.destroy', $page->id) }}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    @else
                        <td>{{trans('variables.delete_inner_modules')}}</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan=7></td>
            </tr>
            </tfoot>
        </table>
    @else
        <div class="empty-response">{{trans('variables.list_is_empty')}}</div>
    @endif

@stop

@section('footer')
    <footer>
        @include('footer')
    </footer>
@stop
