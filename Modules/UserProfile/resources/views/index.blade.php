@extends('userprofile::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('userprofile.name') !!}</p>
@endsection
