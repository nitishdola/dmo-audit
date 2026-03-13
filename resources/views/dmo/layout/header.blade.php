<header class="bg-white border-b border-slate-200/80 sticky top-0 z-10 backdrop-blur-sm">
    <div class="px-6 md:px-8 py-4 flex items-center justify-between">
        <!-- left: logo + district badge -->
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-sm shadow-emerald-200">
                <i class="fas fa-stethoscope text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-slate-800">PMJAY Assam <span class="text-sm font-normal text-slate-400 ml-1 hidden sm:inline-block">· DMO Dashboard</span></h1>
                <p class="text-xs text-emerald-600 flex items-center gap-1"><i class="fas fa-map-marker-alt text-[10px]"></i> {{ auth()->user()->name }}</p>
            </div>
        </div>

        <!-- right: profile chip with dropdown -->
        <div class="flex items-center gap-4">
            <div class="relative" x-data="{ open: false }">
                <!-- Trigger -->
                <button @click="open = !open"
                    class="flex items-center gap-2 bg-slate-100 pl-3 pr-2 py-1.5 rounded-full hover:bg-slate-200 transition-colors focus:outline-none">
                    <span class="text-sm font-medium text-slate-700 hidden sm:block">DMO</span>
                    <span class="h-8 w-8 rounded-full bg-emerald-700 text-white flex items-center justify-center text-sm font-semibold">AS</span>
                    <i class="fas fa-chevron-down text-slate-400 text-xs ml-1 transition-transform duration-200"
                       :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown -->
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                     style="display: none;">

                    <!-- User info -->
                    <div class="px-4 py-2.5 border-b border-slate-100">
                        <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">District Medical Officer</p>
                    </div>

                    <!-- Logout -->
                    <a href="{{ route('auth.logout') }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors w-full">
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>