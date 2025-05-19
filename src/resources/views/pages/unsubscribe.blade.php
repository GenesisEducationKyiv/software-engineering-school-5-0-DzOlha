@extends('layouts.app')

@section('title', 'Cancel Subscription')

@section('content')
    @include('components.page-component', [
        'heading' => 'Processing Subscription Cancellation...',
        'showMessageDiv' => true,
        'script' => 'resources/js/handlers/unsubscribe-page.js',
    ])
@endsection
