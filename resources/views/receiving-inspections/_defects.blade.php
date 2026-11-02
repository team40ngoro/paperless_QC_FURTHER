@php
    $rows = old('defects', $defects ?? []);
    if (!is_array($rows)) {
        $rows = [];
    }
    $noDefects = (bool) old('fields.qc_no_defects', $data['qc_no_defects'] ?? false);
@endphp

<div class="border-top pt-3 mt-3">
    <div class="custom-control custom-checkbox mb-2">
        <input type="hidden" name="fields[qc_no_defects]" value="0">
        <input type="checkbox" class="custom-control-input" id="field-qc_no_defects" name="fields[qc_no_defects]" value="1"
               {{ $noDefects ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
        <label class="custom-control-label font-weight-bold" for="field-qc_no_defects">
            No defects found <small class="text-muted">(Tidak ada defect)</small>
        </label>
    </div>

    <div id="defect-rows" class="{{ $noDefects ? 'd-none' : '' }}">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-2">
                <thead class="bg-light">
                    <tr>
                        <th>Defect type / Jenis defect</th>
                        <th style="width: 140px;">Quantity / Jumlah</th>
                        @if(!$readonly)<th style="width: 60px;"></th>@endif
                    </tr>
                </thead>
                <tbody id="defect-rows-body">
                    @forelse($rows as $index => $row)
                        <tr>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="defects[{{ $index }}][defect_type]"
                                       value="{{ $row['defect_type'] ?? '' }}" {{ $readonly ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <input type="number" min="0" step="1" class="form-control form-control-sm" name="defects[{{ $index }}][quantity]"
                                       value="{{ $row['quantity'] ?? '' }}" {{ $readonly ? 'disabled' : '' }}>
                            </td>
                            @if(!$readonly)
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-defect-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!$readonly)
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-defect-row">
                <i class="fas fa-plus mr-1"></i> Add defect row
            </button>
        @endif
    </div>

    @error('qc_defects')
        <span class="text-danger small d-block">{{ $message }}</span>
    @enderror
    @error('defects')
        <span class="text-danger small d-block">{{ $message }}</span>
    @enderror
    @foreach($errors->get('defects.*') as $messages)
        @foreach($messages as $message)
            <span class="text-danger small d-block">{{ $message }}</span>
        @endforeach
    @endforeach
</div>

@if(!$readonly)
    <template id="defect-row-template">
        <tr>
            <td><input type="text" class="form-control form-control-sm" name="defects[__INDEX__][defect_type]" value=""></td>
            <td><input type="number" min="0" step="1" class="form-control form-control-sm" name="defects[__INDEX__][quantity]" value=""></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-defect-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endif
