@extends('layouts.app')

@section('title', 'Login · Dewanda Motor')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center bg-gray-50 p-4" x-data="{ show: false }">
    <div class="w-full max-w-md">
        <div class="flex flex-col items-center mb-8">
            <div class="h-14 w-14 rounded-2xl bg-red-600 flex items-center justify-center shadow-lg shadow-red-200">
                <i data-lucide="bike" class="h-7 w-7 text-white"></i>
            </div>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Dewanda Motor</h1>
            <p class="text-sm text-gray-500">Sistem Kasir · Point of Sale</p>
        </div>

        <form method="POST" action="/login" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-7">
            @csrf

            <div class="flex items-center gap-2 mb-6">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                    <i data-lucide="user" class="h-3.5 w-3.5"></i> Login sebagai Kasir
                </span>
            </div>

            <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
            <div class="relative mb-4">
                <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                <input name="username" value="{{ old('username', 'kasir') }}" placeholder="Masukkan username"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-3 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
            </div>

            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <div class="relative mb-5">
                <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                <input name="password" value="kasir123" placeholder="Masukkan password" :type="show ? 'text' : 'password'"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-10 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                <button type="button" x-on:click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <span x-show="!show"><i data-lucide="eye" class="h-4 w-4"></i></span>
                    <span x-show="show" x-cloak><i data-lucide="eye-off" class="h-4 w-4"></i></span>
                </button>
            </div>

            @error('login')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full rounded-xl bg-red-600 py-3 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.99]">
                Masuk
            </button>

            <p class="mt-4 text-center text-xs text-gray-400">
                Akun demo · <span class="font-medium text-gray-500">kasir / kasir123</span>
            </p>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400">
            © 2026 Dewanda Motor. Hak akses terbatas untuk Kasir.
        </p>
    </div>
</div>
@endsection