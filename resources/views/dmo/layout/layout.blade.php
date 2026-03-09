<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMJAY Assam · DMO advanced dashboard</title>
    <!-- Tailwind + Inter font via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #f6f9fc; }
        /* smooth subtle scroll */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #e9eef3; border-radius: 12px; }
        ::-webkit-scrollbar-thumb { background: #b9c4d1; border-radius: 12px; }
    </style>

    @yield('pageCss')
</head>
<body class="antialiased text-slate-700">
  @include('dmo.layout.header')
  <main class="max-w-7xl mx-auto px-4 md:px-8 py-8">
      @yield('main_title')
      @yield('main_content')
      <div class="text-xs text-slate-400 text-center mt-10 border-t border-slate-200 pt-6">
          PMJAY Assam · District Medical Officer dashboard · secure login ·
      </div>
  </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  @yield('pageJs')
</body>
</html>