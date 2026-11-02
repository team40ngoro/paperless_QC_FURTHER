@php
    $key = $field['key'];
    $type = $field['type'];
    $current = old('fields.' . $key, $value);
    $isRequired = ($field['required'] ?? false)
        || !empty($field['required_when'])
        || !empty($field['required_if_any_not_ok'])
        || !empty($field['required_unless']);
    $visible = true;
    if (!empty($field['visibleIf'])) {
        $visible = (bool) ($production[$field['visibleIf']['field']] ?? false) === (bool) ($field['visibleIf']['equals'] ?? false);
    }
    $verifies = $field['verifies'] ?? [];
@endphp

<div class="form-group receiving-field {{ $visible ? '' : 'd-none' }}"
     data-visible-if-field="{{ $field['visibleIf']['field'] ?? '' }}"
     data-visible-if-value="{{ isset($field['visibleIf']['equals']) ? ($field['visibleIf']['equals'] ? '1' : '0') : '' }}">
    <label for="field-{{ $key }}" class="font-weight-bold">
        {{ $field['label_id'] ?? $field['label'] }}
        @if($isRequired)<span class="text-danger">*</span>@endif
        <small class="text-muted d-block">{{ $field['label'] }}</small>
    </label>

    @if(!empty($verifies) && $section === 'QC')
        <div class="alert alert-light border py-2 px-3 mb-2 small">
            <strong>Production value:</strong>
            @foreach($verifies as $verifiedKey)
                @php $verifiedField = $registry->field($area, $verifiedKey); @endphp
                <span class="mr-3">
                    {{ $verifiedField['label_id'] ?? $verifiedKey }}:
                    <span class="text-dark font-weight-bold">{{ $production[$verifiedKey] ?? '—' }}</span>
                </span>
            @endforeach
        </div>
    @endif

    @if($type === 'boolean')
        <input type="hidden" name="fields[{{ $key }}]" value="0">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="field-{{ $key }}" name="fields[{{ $key }}]" value="1"
                   {{ $current ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
            <label class="custom-control-label" for="field-{{ $key }}">{{ $field['label'] }}</label>
        </div>
    @elseif($type === 'ok_not_ok')
        <div class="d-flex flex-wrap" style="gap: .5rem;">
            <label class="btn btn-outline-success mb-0 qc-toggle {{ (string) $current === 'OK' ? 'active' : '' }}">
                <input type="radio" name="fields[{{ $key }}]" value="OK" class="d-none"
                       {{ (string) $current === 'OK' ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
                <span class="h5 mb-0">✔</span> OK
            </label>
            <label class="btn btn-outline-danger mb-0 qc-toggle {{ (string) $current === 'NOT_OK' ? 'active' : '' }}">
                <input type="radio" name="fields[{{ $key }}]" value="NOT_OK" class="d-none"
                       {{ (string) $current === 'NOT_OK' ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
                <span class="h5 mb-0">✘</span> NOT OK
            </label>
        </div>
    @elseif($type === 'enum')
        <select class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]" {{ $readonly ? 'disabled' : '' }}>
            <option value="">Select...</option>
            @foreach(($field['validation']['enumValues'] ?? []) as $option)
                <option value="{{ $option }}" {{ (string) $current === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    @elseif($type === 'status')
        <select class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]" {{ $readonly ? 'disabled' : '' }}>
            <option value="">Select...</option>
            <option value="ACCEPTED" {{ (string) $current === 'ACCEPTED' ? 'selected' : '' }}>ACCEPTED</option>
            <option value="REJECTED" {{ (string) $current === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
        </select>
    @elseif($type === 'shift')
        <select class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]" {{ $readonly ? 'disabled' : '' }}>
            <option value="">Select shift...</option>
            @foreach(($shiftOptions ?? []) as $option)
                <option value="{{ $option }}" {{ (string) $current === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
            @if($current && !in_array((string) $current, array_map('strval', $shiftOptions ?? []), true))
                <option value="{{ $current }}" selected>{{ $current }}</option>
            @endif
        </select>
    @elseif($type === 'text')
        <textarea class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]" rows="3"
                  {{ $readonly ? 'disabled' : '' }}>{{ $current }}</textarea>
    @elseif($type === 'date')
        <input type="date" class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]"
               value="{{ $current }}" {{ $readonly ? 'disabled' : '' }}>
    @elseif($type === 'time')
        <input type="time" class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]"
               value="{{ $current }}" {{ $readonly ? 'disabled' : '' }}>
    @elseif($type === 'decimal' || $type === 'temp_c')
        <div class="input-group">
            <input type="number" step="0.1" class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]"
                   value="{{ $current }}" {{ $readonly ? 'disabled' : '' }}>
            @if(!empty($field['validation']['unit']))
                <div class="input-group-append"><span class="input-group-text">{{ $field['validation']['unit'] }}</span></div>
            @endif
        </div>
    @elseif($type === 'integer')
        <div class="input-group">
            <input type="number" step="1" class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]"
                   value="{{ $current }}" {{ $readonly ? 'disabled' : '' }}>
            @if(!empty($field['validation']['unit']))
                <div class="input-group-append"><span class="input-group-text">{{ $field['validation']['unit'] }}</span></div>
            @endif
        </div>
    @else
        <input type="text" class="form-control" id="field-{{ $key }}" name="fields[{{ $key }}]"
               value="{{ $current }}" {{ $readonly ? 'disabled' : '' }}>
    @endif

    @error('fields.' . $key)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
