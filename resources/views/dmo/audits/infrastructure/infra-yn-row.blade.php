{{-- dmo.audit.partials.infra-yn-row
     Variables: $num, $label, $name, $remark (optional) --}}
<div class="obs-card">
    <div class="obs-top">
        <div>
            <span class="obs-num">{{ $num }}</span>
            &nbsp;<span class="obs-label">{{ $label }}</span>
        </div>
        <div class="radio-group">
            <input type="radio" name="{{ $name }}" id="{{ $name }}_y" value="Yes"
                   {{ old($name) === 'Yes' ? 'checked' : '' }}>
            <label for="{{ $name }}_y">Yes</label>

            <input type="radio" name="{{ $name }}" id="{{ $name }}_n" value="No"
                   {{ old($name) === 'No' ? 'checked' : '' }}>
            <label for="{{ $name }}_n">No</label>

            <input type="radio" name="{{ $name }}" id="{{ $name }}_na" value="NA"
                   {{ old($name) === 'NA' ? 'checked' : '' }}>
            <label for="{{ $name }}_na">NA</label>
        </div>
    </div>
    @if(isset($remark))
    <div class="obs-remark">
        <input type="text" name="{{ $remark }}"
               value="{{ old($remark) }}"
               placeholder="Remarks (optional)" />
    </div>
    @endif
</div>
