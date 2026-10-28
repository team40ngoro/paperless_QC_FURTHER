<!-- Copy the form sections from create.blade.php -->
<!-- This will be included in both create and edit forms -->

<!-- After Forming Section (Always visible, but content changes based on product type) -->
<div class="card mt-3" id="after-forming-section" style="display: none;">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">After Forming</h3>
    </div>
    <div class="card-body">
        <!-- Regular After Forming Fields (for non-KFC) -->
        <div id="regular-after-forming-fields">
            <div id="after-forming-container">
                <div class="after-forming-entry mb-3">
                    <div class="input-group">
                        <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan berat after forming">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-after-forming" style="display: none;">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-3">
                <button type="button" class="btn btn-success add-after-forming">
                    <i class="fas fa-plus"></i> Tambah After Forming
                </button>
            </div>

            <!-- Rata-rata After Forming -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="rata_rata_after_forming">Rata-rata After Forming</label>
                        <input type="number" step="0.01" name="rata_rata_after_forming" id="rata_rata_after_forming" class="form-control" readonly>
                        <small class="text-muted">Otomatis dihitung dari berat after forming</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- KFC Dry/Wet Fields (for KFC products) -->
        <div id="kfc-dry-wet-fields" style="display: none;">
            <!-- Dry KFC Section -->
            <div class="row">
                <div class="col-md-6">
                    <h5>Berat Dry (KFC)</h5>
                    <div id="dry-kfc-container">
                        <div class="dry-kfc-entry mb-3">
                            <div class="input-group">
                                <input type="number" step="0.01" name="berat_dry_kfc[]" class="form-control dry-kfc-input" placeholder="Masukkan berat dry">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-dry-kfc" style="display: none;">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-success add-dry-kfc">
                            <i class="fas fa-plus"></i> Tambah Dry KFC
                        </button>
                    </div>

                    <!-- Rata-rata Dry KFC -->
                    <div class="form-group">
                        <label for="rata_rata_dry_kfc">Rata-rata Dry KFC</label>
                        <input type="number" step="0.01" name="rata_rata_dry_kfc" id="rata_rata_dry_kfc" class="form-control" readonly>
                        <small class="text-muted">Otomatis dihitung dari berat dry KFC</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>Berat Wet (KFC)</h5>
                    <div id="wet-kfc-container">
                        <div class="wet-kfc-entry mb-3">
                            <div class="input-group">
                                <input type="number" step="0.01" name="berat_wet_kfc[]" class="form-control wet-kfc-input" placeholder="Masukkan berat wet">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-wet-kfc" style="display: none;">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-success add-wet-kfc">
                            <i class="fas fa-plus"></i> Tambah Wet KFC
                        </button>
                    </div>

                    <!-- Rata-rata Wet KFC -->
                    <div class="form-group">
                        <label for="rata_rata_wet_kfc">Rata-rata Wet KFC</label>
                        <input type="number" step="0.01" name="rata_rata_wet_kfc" id="rata_rata_wet_kfc" class="form-control" readonly>
                        <small class="text-muted">Otomatis dihitung dari berat wet KFC</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup After Forming KFC Field (for KFC products) -->
        <div class="row mt-3" id="pickup-after-forming-kfc-field" style="display: none;">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="pickup_after_forming_kfc">Pickup After Forming KFC</label>
                    <input type="number" step="0.01" name="pickup_after_forming_kfc" id="pickup_after_forming_kfc" class="form-control" readonly>
                    <small class="text-muted">
                        <strong>Rumus Pickup After Forming KFC:</strong><br>
                        (rata-rata wet KFC - rata-rata dry KFC) / rata-rata breader × 100
                    </small>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="row mt-3">
            <div class="col-md-4">
                <button type="button" class="btn btn-warning btn-block" id="btn-ke-predusting">
                    <i class="fas fa-arrow-right"></i> Ke Predusting
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-info btn-block" id="btn-ke-battering-after-forming">
                    <i class="fas fa-arrow-right"></i> Ke Battering
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary btn-block" id="btn-ke-breadering">
                    <i class="fas fa-arrow-right"></i> Ke Breadering
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include all other sections from create form -->
<!-- This is a simplified version - you would copy all sections from create.blade.php -->
