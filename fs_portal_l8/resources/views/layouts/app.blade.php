<!DOCTYPE html>
<html>
<head>
  <title>@yield('title')</title>
</head>
<body>
  @yield('content')

  {{-- Important: Push scripts after content --}}
  @push('scripts')
</body>
</html>
