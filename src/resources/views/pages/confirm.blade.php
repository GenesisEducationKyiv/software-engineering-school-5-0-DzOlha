@extends('layouts.app')

@section('title', 'Confirm Subscription')

@section('content')
    @include('components.page-component', [
        'heading' => 'Processing Subscription Confirmation...',
        'showMessageDiv' => true,
        'script' => 'resources/js/handlers/confirm-page.js',
    ])
@endsection
