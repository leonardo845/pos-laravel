@extends('layouts.app')

@section('title', __('nav.dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('nav.dashboard') }}</h4>
</div>
<div class="card">
    <div class="card-body">
        <p class="text-muted mb-0">Welcome to Simple POS, <strong>{{ Auth::user()->name }}</strong>.</p>
    </div>
</div>
@endsection
