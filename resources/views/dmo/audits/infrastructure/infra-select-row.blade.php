{{-- dmo.audit.partials.infra-select-row
     Variables: $num, $label, $name, $options (array), $remark (optional) --}}
<div class="obs-card">
    <div class="obs-top">
        <div>
            <span class="obs-num">{{ $num }}</span>
            &nbsp;<span class="obs-label">{{ $label }}</span>
        </div>
        <select name="{{ $name }}" class="sm-select">
            <option value="">Select…</option>
            @foreach($options as $opt)
            <option value="{{ $opt }}" {{ old($name) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
    @if(isset($remark))
    <div class="obs-remark">
        <input type="text" name="{{ $remark }}"
               value="{{ old($remark) }}"
               placeholder="Remarks (optional)" />
    </div>
    @endif
</div>
