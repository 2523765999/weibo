@extends('layouts.default')
@section('title', '所有用户')

@section('content')
    <div class="col-md-offset-2 col-md-8">
        <h1>所有用户</h1>
        <ul class="users">
            @foreach ($users as $user)
{{--                <li>--}}
{{--                    <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="gravatar"/>--}}
{{--                    <a href="{{ route('users.show', $user->id) }}" class="username">{{ $user->name }}</a>--}}
{{--                </li>--}}
                @include('users._user')
            @endforeach
        </ul>
        {!! $users->render() !!}
        {{--    在调用 paginate 方法获取用户列表之后，便可以通过以下代码在用户列表页上渲染分页链接。    --}}
        {{--    由 render 方法生成的 HTML 代码默认会使用 Bootstrap 框架的样式，渲染出来的视图链接也都统一会带上 ?page 参数来设置指定页数的链接。    --}}
    </div>
@stop
