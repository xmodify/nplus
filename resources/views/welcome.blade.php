@extends('layouts.hnplus') {{-- หรือสร้าง layout เฉพาะ hrims ก็ได้ --}}

@section('content')
<div class="container">
    <h3 class="text-center text-danger">
        <strong>ยินดีต้อนรับเข้าสู่</strong>...
    </h3>
    <h1 class="text-center text-success">       
        <img src="{{ asset('images/logo_hnplus1.png') }}" alt="HN-Plus Logo" height="200">
    </h1>
    <h3 class="text-center text-hnplus">
        <strong>Nurse Plus </strong>
    </h3>
</div>
@endsection