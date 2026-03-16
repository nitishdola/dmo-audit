{{-- dmo.audit.partials.infra-num-row
     Variables: $num, $label, $name --}}
<div class="obs-card">
    <div class="obs-top">
        <div>
            <span class="obs-num">{{ $num }}</span>
            &nbsp;<span class="obs-label">{{ $label }}</span>
        </div>
        <input type="number"
               name="{{ $name }}"
               value="{{ old($name) }}"
               min="0"
               placeholder="0"
               class="num-input" />
    </div>
</div>
