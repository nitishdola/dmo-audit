@extends('dmo.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-3 mb-7">
    
    <div>
        <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight">
            Audit & field overview
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Real-time completion metrics · PMJAY Assam district dashboard
        </p>
    </div>

    <a href="{{ route('dmo.audits.field.all') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md hover:shadow-lg transition">
        <i class="fas fa-bolt"></i>
        Conduct Live Audit
    </a>

</div>
@endsection

@section('main_content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-10">

  <!-- 1. Total Assigned / Total completed (generic) -->
  <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
      <div class="flex items-start justify-between">
          <div>
              <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">total assigned</span>
              <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                  {{$total_assigned}}
                  <span class="text-sm font-normal text-slate-400 ml-1">tasks</span>
              </div>
          </div>
          <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
              <i class="fas fa-clipboard-list"></i>
          </div>
      </div>
      <div class="mt-5 flex items-center justify-between">
          <div>
              <p class="text-xs text-slate-400">completed</p>
              <p class="text-xl font-semibold text-slate-800">{{ $total_completed }}</p>
          </div>
            @php 
                $pc = ($total_assigned > 0) ? ($total_completed / $total_assigned) * 100 : 0;
                $pc = round($pc);
            @endphp

            <div class="flex-1 mx-4 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pc }}%;"></div>
            </div>
          <span class="text-sm font-medium text-indigo-600">{{ $pc }} %</span>
      </div>

      <div class="mt-3 text-xs text-rose-500 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $total_assigned - $total_completed}} Overdue Audits</div>
      
  </div>

  <!-- 2. Total Telephonic Audit & completed -->
  <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
      <div class="flex items-start justify-between">
          <div>
              <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">telephonic audit</span>
              <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                  {{ $total_tele_assigned }}
                  <span class="text-sm font-normal text-slate-400 ml-1">calls</span>
              </div>
          </div>
          <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
              <i class="fas fa-phone-alt"></i>
          </div>
      </div>
      <div class="mt-5 flex items-center justify-between">
          <div>
              <p class="text-xs text-slate-400">completed</p>
              <p class="text-xl font-semibold text-slate-800">{{ $total_tele_completed }}</p>
          </div>

            @php $tpc = ($total_tele_assigned > 0) ? ($total_tele_completed / $total_tele_assigned) * 100 : 0; $tpc = round($tpc); @endphp
            
          <div class="flex-1 mx-4 h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $tpc }}%;"></div>
          </div>

            

          <span class="text-sm font-medium text-emerald-600">{{ $tpc }} %</span>
      </div>

      <div class="mt-3 text-xs text-rose-500 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $total_tele_assigned - $total_tele_completed}} Overdue Telephonic Audits</div>
      
  </div>


   @php 
        $fpc = ($total_field_assigned > 0) ? ($total_field_completed / $total_field_assigned) * 100 : 0;
        $fpc = round($fpc);
    @endphp

  <!-- 3. Total Field Visits & completed -->
  <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
      <div class="flex items-start justify-between">
          <div>
              <span class="text-sm font-medium text-slate-400 uppercase tracking-wider">field visits</span>
              <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                  {{ $total_field_assigned }}
                  <span class="text-sm font-normal text-slate-400 ml-1">visits</span>
              </div>
          </div>
          <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
              <i class="fas fa-people-arrows"></i>
          </div>
      </div>
      <div class="mt-5 flex items-center justify-between">
          <div>
              <p class="text-xs text-slate-400">completed</p>
              <p class="text-xl font-semibold text-slate-800">{{ $total_field_completed }}</p>
          </div>
          <div class="flex-1 mx-4 h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-amber-500 rounded-full" style="width: {{ $fpc }}%;"></div>
          </div>
          <span class="text-sm font-medium text-amber-600">{{  $fpc }}%</span>
      </div>
      <div class="mt-3 text-xs text-rose-500 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $total_field_assigned - $total_field_completed  }} overdue visits</div>
  </div>
</div>


<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
  <!-- table header with filter & title -->
  <div class="px-6 py-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100">
      <div class="flex items-center gap-3">
          <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
              <i class="fas fa-map-marked-alt text-emerald-600"></i> 
              Recent field visits
          </h3>
          
      </div>
      
  </div>

  <!-- table (modern, clean, with tags & progress visuals) -->
  <div class="overflow-x-auto">
      <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-500 text-xs font-medium">
              <tr>
                  <th class="px-6 py-4 text-left">Facility / location</th>
                  <th class="px-6 py-4 text-left">Medical officer</th>
                  <th class="px-6 py-4 text-left">Date</th>
                  <th class="px-6 py-4 text-left">Visit type</th>
                  <th class="px-6 py-4 text-left">Findings</th>
                  <th class="px-6 py-4 text-left">Status</th>
                  <th class="px-6 py-4 text-right">Actions</th>
              </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
              <!-- row 1 -->
              <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-6 py-4 font-medium text-slate-800">GNRC, Guwahati</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Borah</td>
                  <td class="px-6 py-4 text-slate-500">22 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1.5 rounded-full">Routine audit</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">Equipment ok, 2 records mismatch</td>
                  <td class="px-6 py-4"><span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1.5 rounded-full font-medium">Completed</span></td>
                  <td class="px-6 py-4 text-right">
                      <button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button>
                  </td>
              </tr>
              <!-- row 2 -->
              <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-6 py-4 font-medium text-slate-800">Diphu Medical College (PHC)</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Das</td>
                  <td class="px-6 py-4 text-slate-500">21 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-amber-50 text-amber-700 text-xs px-3 py-1.5 rounded-full">Unannounced</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">Staff attendance, pending packages</td>
                  <td class="px-6 py-4"><span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1.5 rounded-full">Completed</span></td>
                  <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button></td>
              </tr>
              <!-- row 3 -->
              <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-6 py-4 font-medium text-slate-800">Nalbari Civil Hospital</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Barman</td>
                  <td class="px-6 py-4 text-slate-500">19 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1.5 rounded-full">Follow-up</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">Previous issues resolved, IT upgrade needed</td>
                  <td class="px-6 py-4"><span class="bg-slate-200 text-slate-600 text-xs px-3 py-1.5 rounded-full">Draft</span></td>
                  <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button></td>
              </tr>
              <!-- row 4 (in-progress) -->
              <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-6 py-4 font-medium text-slate-800">Barpeta District Hospital</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Sarma</td>
                  <td class="px-6 py-4 text-slate-500">18 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1.5 rounded-full">Routine audit</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">Pending verification of 23 benf. records</td>
                  <td class="px-6 py-4"><span class="bg-amber-100 text-amber-700 text-xs px-3 py-1.5 rounded-full">In progress</span></td>
                  <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button></td>
              </tr>
              <!-- row 5 - more recent -->
              <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-6 py-4 font-medium text-slate-800">Sonapur CHC</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Neog</td>
                  <td class="px-6 py-4 text-slate-500">17 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-amber-50 text-amber-700 text-xs px-3 py-1.5 rounded-full">Spot check</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">Fake ABHA cards? – flagged for review</td>
                  <td class="px-6 py-4"><span class="bg-rose-100 text-rose-700 text-xs px-3 py-1.5 rounded-full">Escalated</span></td>
                  <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button></td>
              </tr>
              <!-- row 6 -->
              <tr class="hover:bg-slate-50/60 transition border-b-0">
                  <td class="px-6 py-4 font-medium text-slate-800">Mangaldai (FRU)</td>
                  <td class="px-6 py-4 text-slate-600">Dr. Deka</td>
                  <td class="px-6 py-4 text-slate-500">15 Mar 2025</td>
                  <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1.5 rounded-full">Routine audit</span></td>
                  <td class="px-6 py-4 max-w-45 truncate text-slate-500">All ok, 100% PMJAY records updated</td>
                  <td class="px-6 py-4"><span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1.5 rounded-full">Completed</span></td>
                  <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-emerald-600"><i class="fas fa-eye"></i></button></td>
              </tr>
          </tbody>
      </table>
  </div>

  <!-- table footer (pagination / summary) -->
  <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-sm">
      <div class="text-slate-500">Showing 6 of 24 field visits</div>
      <div class="flex items-center gap-3">
          <button class="text-slate-400 hover:text-slate-700 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white"><i class="fas fa-chevron-left text-xs"></i></button>
          <span class="text-slate-600 text-sm">1 · 2 · 3 · 4</span>
          <button class="text-slate-400 hover:text-slate-700 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white"><i class="fas fa-chevron-right text-xs"></i></button>
      </div>
  </div>
</div>
@endsection