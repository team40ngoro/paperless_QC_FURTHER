@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Incoming Inspection</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('receiving-inspections.index') }}">Incoming Inspection</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> New Record</h3>
                        </div>
                        <form method="POST" action="{{ route('receiving-inspections.store') }}">
                            @csrf
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="area">Area <span class="text-danger">*</span></label>
                                    <select name="area" id="area" class="form-control" required>
                                        <option value="">Select area...</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area }}" {{ old('area') === $area ? 'selected' : '' }}>{{ $registry->areaLabel($area) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="inspection_date">Day / Date <span class="text-danger">*</span></label>
                                    <input type="date" name="inspection_date" id="inspection_date" class="form-control"
                                           value="{{ old('inspection_date', now()->format('Y-m-d')) }}" required>
                                    <small class="text-muted">The weekday is derived automatically and displayed in Indonesian.</small>
                                </div>

                                <div class="alert alert-light border small mb-0" id="form-code-preview">
                                    <strong>Form codes:</strong> <span id="preview-codes">Select an area.</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Create Record
                                </button>
                                <a href="{{ route('receiving-inspections.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Role ownership</h3></div>
                        <div class="card-body small">
                            <p><strong>Production Input Checker</strong> creates the record and owns the Production section (<code>FP-01</code>).</p>
                            <p><strong>QC Auditor</strong> owns the QC section (<code>E-QF 01/02/03</code>) and can read Production values to verify them.</p>
                            <p class="mb-0">Each section has its own status. The record is complete when both sections are submitted.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    const formCodes = @json(collect($areas)->mapWithKeys(fn ($area) => [$area => $registry->formCodes($area)]));
    document.getElementById('area')?.addEventListener('change', function () {
        const codes = formCodes[this.value];
        document.getElementById('preview-codes').textContent = codes
            ? 'QC ' + codes.qc_form_code + ' · Production ' + codes.prod_form_code
            : 'Select an area.';
    });
</script>
@endsection
