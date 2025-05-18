@extends('authentication::layouts.master')

@section('content')
<h1>Hello World</h1>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda veritatis esse blanditiis commodi id dolorum eos? Dicta nesciunt, ea eveniet architecto deleniti alias fugiat impedit. Quas cupiditate omnis similique fugiat.</p>
<p>Module: {!! config('authentication.name') !!}</p>
@endsection