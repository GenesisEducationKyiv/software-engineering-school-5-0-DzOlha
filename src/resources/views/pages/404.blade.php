@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
    @include('components.page-component', [
        'heading' => 'Page Not Found!',
        'showMessageDiv' => false,
        'script' => null,
    ])
@endsection
