<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }} - Log In</title>

    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap"
      rel="stylesheet"
    />
    @vite('resources/css/app.css')
  </head>
  <body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
      <!-- Header with Logo -->
      <div
        class="bg-linear-to-r from-indigo-950 to-indigo-800 border-b border-gray-200"
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-20">
            <div class="flex items-center space-x-3">
              <!-- Government Logo Placeholder -->
              <div
                class="w-12 h-12 bg-blue-900 rounded-lg flex items-center justify-center text-white font-bold text-xl"
              >
                G
              </div>
              <div>
                <h1 class="text-xl font-bold text-gray-900">
                  {{ config('app.name', 'MyGov') }}
                </h1>
                <p class="text-sm text-gray-600">Government of India</p>
              </div>
            </div>
            <div class="text-sm text-white">Atal Amrit Abhiyan Society</div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div
        class="grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
      >
        <div class="max-w-md w-full space-y-8">
          <!-- Session Status & Errors -->
          @if (session('status'))
          <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <p class="text-sm text-green-700">{{ session('status') }}</p>
          </div>
          @endif @if ($errors->any())
          <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="text-sm text-red-700">
              {{ __('Whoops! Something went wrong.') }}
            </div>
            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <!-- Login Header -->
          <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">
              Log In to your account
            </h2>
          </div>

          <!-- OTP Login Tab (MyGov's primary option) -->
          <div class="bg-white py-8 px-6 shadow rounded-lg">
            <div class="mb-6">
              <div class="flex border-b border-gray-200">
                <button
                  class="py-2 px-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600"
                >
                  Log In With OTP
                </button>
              </div>
            </div>

            <!-- Login Form -->
              <form method="POST" action="{{ route('auth.otp.verify') }}" class="space-y-6">
              @csrf

              <!-- OTP Field -->
              <div>

                <input type="hidden" name="mobile"
                      value="{{ request('mobile') }}">

                <p class="mb-2 text-sm text-gray-600">
                    OTP sent to {{ request('mobile') }}
                </p>
                <label
                  for="username"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Enter 6-digit OTP
                </label>
                <input
                  type="number"
                  name="otp"
                  id="otp"
                  value="{{ old('otp') }}"
                  required
                  autofocus
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder=""
                />
              </div>


              <!-- Submit Button -->
              <div>
                <button
                  type="submit"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Verify & Login
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-xs text-gray-600 space-y-2">
            <p>
              © Content owned, updated and maintained by the {{
              config('app.name', 'MyGov') }} Cell. This website belongs to {{
              config('app.name', 'MyGov') }}, Ministry of Electronics &
              Information Technology, Government of India.
            </p>
            <p>
              Platform is designed, developed and hosted by National Informatics
              Centre.
            </p>
            <p class="text-gray-400">
              © कॉन्टेंट का स्वामित्व, अपडेट और रखरखाव माईगव सेल के पास है। यह
              वेबसाइट माईगव, इलेक्ट्रॉनिक्स और सूचना प्रौद्योगिकी मंत्रालय, भारत
              सरकार की है।
            </p>
            <p>
              प्लेटफ़ॉर्म को राष्ट्रीय सूचना विज्ञान केंद्र द्वारा डिज़ाइन, विकसित
              और होस्ट किया गया है|
            </p>
            <p class="text-gray-400 mt-2">
              auth-{{ rand(100, 999) }} - Last Updated: {{
              now()->format('d/m/y') }}
            </p>
          </div>
        </div>
      </footer>
    </div>
  </body>
</html>
