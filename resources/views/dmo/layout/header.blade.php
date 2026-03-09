<header class="bg-white border-b border-slate-200/80 sticky top-0 z-10 backdrop-blur-sm ">
      <div class="px-6 md:px-8 py-4 flex items-center justify-between">
          <!-- left: logo + district badge -->
          <div class="flex items-center gap-3">
              <div class="h-10 w-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-sm shadow-emerald-200">
                  <i class="fas fa-stethoscope text-white text-lg"></i>
              </div>
              <div>
                  <h1 class="text-xl font-semibold tracking-tight text-slate-800">PMJAY Assam <span class="text-sm font-normal text-slate-400 ml-1 hidden sm:inline-block">· DMO advanced</span></h1>
                  <p class="text-xs text-emerald-600 flex items-center gap-1"><i class="fas fa-map-marker-alt text-[10px]"></i> Kamrup district · Dr. {{ auth()->user()->name }}</p>
              </div>
          </div>
          <!-- right: date & notifications / profile chip -->
          <div class="flex items-center gap-4">
              
              
              <div class="flex items-center gap-2 bg-slate-100 pl-3 pr-2 py-1.5 rounded-full">
                  <span class="text-sm font-medium text-slate-700 hidden sm:block">DMO</span>
                  <span class="h-8 w-8 rounded-full bg-emerald-700 text-white flex items-center justify-center text-sm font-semibold">AS</span>
              </div>
          </div>
      </div>
  </header>