<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0F2167">
<title>@yield('title', 'QMAS') — {{ config('app.name') }}</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
@php
  $isQmas = request()->routeIs('qmas.*');
  $isFc   = request()->routeIs('foodcourt.*') || request()->routeIs('menus.*');
  $isDash = request()->routeIs('dashboard');
@endphp
<div id="overlay" hidden></div>
<aside id="sidebar">
  <button id="btnClose" class="close-btn" aria-label="Tutup menu"><i class="fa-solid fa-xmark"></i></button>
  <div class="brand">
    <div class="brand-icon"><img src="{{ asset('logo/logo.jpeg') }}" alt="Logo"></div>
    <div>
      <h1>QMAS<br><span style="font-size:11px;color:#7dd3fc99;letter-spacing:1px">INVENTARIS</span></h1>
    </div>
  </div>
  <nav>
    <a href="{{ route('dashboard') }}" class="{{ $isDash ? 'active' : '' }}">
      <i class="fa-solid fa-house"></i> Portal Utama
    </a>
    @if ($isQmas || $isDash)
      <a href="{{ route('qmas.index') }}" class="{{ request()->routeIs('qmas.*') ? 'active' : '' }}">
        <i class="fa-solid fa-cubes-stacked"></i> Stok QMAS
      </a>
      <a href="{{ route('qmas.create') }}" class="{{ request()->routeIs('qmas.create') ? 'active' : '' }}">
        <i class="fa-solid fa-plus"></i> Tambah Stok
      </a>
    @endif
    @if ($isFc || $isDash)
      <a href="{{ route('foodcourt.index') }}" class="{{ request()->routeIs('foodcourt.*') ? 'active' : '' }}">
        <i class="fa-solid fa-utensils"></i> Foodcourt
      </a>
      <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
        <i class="fa-solid fa-list"></i> Menu Foodcourt
      </a>
    @endif
  </nav>
  <div class="side-user">
    <div class="side-user-box">
      <b>{{ auth()->user()->name ?? 'Admin' }}</b><br>
      <small>{{ auth()->user()->email ?? '' }}</small>
      <div style="margin-top:8px">
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button type="submit" style="background:none;border:none;color:#fff;font-size:12px;cursor:pointer;padding:0" onclick="return confirm('Keluar dari dashboard?')">→ Keluar</button>
        </form>
      </div>
    </div>
  </div>
</aside>

<div class="main">
  <header id="topbar">
    <div class="topbar-row">
      <button id="btnMenu" class="hamburger" aria-label="Buka menu" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
      <div class="tabs">
        <a href="{{ route('dashboard') }}" class="{{ $isDash ? 'active' : '' }}">Portal</a>
        @if ($isQmas || $isDash)
          <a href="{{ route('qmas.index') }}" class="{{ request()->routeIs('qmas.*') ? 'active' : '' }}">QMAS</a>
        @endif
        @if ($isFc || $isDash)
          <a href="{{ route('foodcourt.index') }}" class="{{ request()->routeIs('foodcourt.*') ? 'active' : '' }}">Foodcourt</a>
          <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">Menu</a>
        @endif
      </div>
      <div class="top-right">
        <span class="user">{{ auth()->user()->name ?? 'Admin' }}</span>
      </div>
    </div>
    <div class="page-head">
      <h2>@yield('title', 'QMAS')</h2>
      <div class="actions no-print">
        @yield('actions')
      </div>
    </div>
  </header>

  <div class="content">
    @if (session('success'))
      <div class="card" style="background:#dcfce7;color:#15803d;border-color:#bbf7d0">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="card" style="background:#fee2e2;color:#b91c1c;border-color:#fecaca">
        <ul style="margin:0;padding-left:18px">
          @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif
    @yield('content')
  </div>
</div>

<nav id="bottomnav" class="no-print">
  <a href="{{ route('dashboard') }}" class="{{ $isDash ? 'active' : '' }}"><i class="fa-solid fa-house"></i><span>Portal</span></a>
  @if ($isQmas || $isDash)
    <a href="{{ route('qmas.index') }}" class="{{ request()->routeIs('qmas.index') ? 'active' : '' }}"><i class="fa-solid fa-cubes-stacked"></i><span>Stok</span></a>
    <a href="{{ route('qmas.create') }}" class="{{ request()->routeIs('qmas.create') ? 'active' : '' }}"><i class="fa-solid fa-plus"></i><span>Tambah</span></a>
  @endif
  @if ($isFc || $isDash)
    <a href="{{ route('foodcourt.index') }}" class="{{ request()->routeIs('foodcourt.*') ? 'active' : '' }}"><i class="fa-solid fa-utensils"></i><span>Food</span></a>
    <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}"><i class="fa-solid fa-list"></i><span>Menu</span></a>
  @endif
</nav>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>