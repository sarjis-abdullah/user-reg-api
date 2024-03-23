@extends('layouts.app')
@section('logo')
    @include('layouts.logo')
@endsection
@section('content')
    @include('user.password-reset.body', ['user' => $user, 'pin' => $pin])
@endsection
