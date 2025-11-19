@extends('layouts.auth')

@section('content')

@php
$mode = request('mode', 'login');
@endphp

<form method="POST" action="{{ route('auth.submit') }}" class="auth-form">
    @csrf

    {{-- LOGIN --}}
    @if ($mode === 'login')
    <h4 class="text-center mb-4">Masuk</h4>

    @if(session('error'))
    <p class="text-danger mt-2">{{ session('error') }}</p>
    @endif

    <input type="email" name="email" class="form-control mb-3"
        placeholder="Email" value="{{ old('email') }}">

    <input type="password" name="password" class="form-control mb-3"
        placeholder="Password">

    <a href="{{ url('?mode=verify-email') }}">
        Lupa Password?
    </a>

    <button class="btn btn-primary w-100">
        Masuk
    </button>
    @endif


    {{-- VERIFY EMAIL --}}
    @if ($mode === 'verify-email')
    <h4 class="text-center mb-4">Verifikasi Email</h4>

    @if(session('error'))
    <p class="text-danger mt-2">{{ session('error') }}</p>
    @endif

    <input type="email" name="email" class="form-control mb-3"
        placeholder="Masukkan email Anda" value="{{ old('email') }}">

    <button class="btn btn-primary w-100">
        Kirim Kode
    </button>
    @endif


    {{-- OTP VERIFICATION --}}
    @if ($mode === 'verify-token')
    <h4 class="text-center mb-4">Masukkan Kode OTP</h4>

    @if(session('error'))
    <p class="text-danger mt-2">{{ session('error') }}</p>
    @endif

    <div class="d-flex justify-content-center gap-3 mb-3">
        @for ($i = 0; $i < 6; $i++)
            <input type="text" maxlength="1" class="otp-input text-center"
            name="otp[]" oninput="otpHandle({{ $i }})"
            id="otp{{ $i }}">
            @endfor
    </div>

    <p class="text-center mt-2">
        <a href="{{ url('?mode=verify-email') }}">Kirim Ulang Kode</a>
    </p>
    @endif


    {{-- RESET PASSWORD --}}
    @if ($mode === 'reset-password')
    <h4 class="text-center mb-4">Reset Password</h4>

    @if(session('error'))
    <p class="text-danger mt-2">{{ session('error') }}</p>
    @endif

    <input type="password" name="password" class="form-control mb-3"
        placeholder="Kata Sandi Baru">

    <input type="password" name="password_confirmation" class="form-control mb-3"
        placeholder="Konfirmasi Kata Sandi">

    <button class="btn btn-primary w-100">
        Kirim
    </button>
    @endif

</form>

@endsection
@section('scripts')
<script>
    function otpHandle(index) {
        let current = document.getElementById('otp' + index);
        if (current.value.length === 1 && index < 5) {
            document.getElementById('otp' + (index + 1)).focus();
        }
    }
</script>
@endsection