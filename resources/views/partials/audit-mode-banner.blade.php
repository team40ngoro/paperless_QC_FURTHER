@php
    // Primary check: $isAuditMode variable explicitly passed from controller (highest priority)
    if (!isset($isAuditMode)) {
        $isAuditMode = false;
    }

    // Secondary check: is_audit query parameter (backward compatibility)
    if (!$isAuditMode && request()->has('is_audit') && request('is_audit') == 1) {
        $isAuditMode = true;
    }

    // Tertiary check: Check model data is_audit field if on edit/create routes
    $currentRouteName = request()->route()?->getName() ?? '';
    $isEditRoute = str_contains($currentRouteName, '.edit') || str_contains($currentRouteName, '.create');
    
    if (!$isAuditMode && $isEditRoute) {
        $primaryVarNames = ['item', 'data', 'pembuatanSample', 'pemeriksaanRiceBites', 'pemeriksaanRheonMachine', 'areaProses', 'pembekuan', 'ketidaksesuaianPlastik', 'produkForming', 'produkNonForming', 'pemeriksaanBendaAsing'];
        foreach ($primaryVarNames as $varName) {
            if (isset($$varName) && is_object($$varName) && isset($$varName->is_audit) && $$varName->is_audit) {
                $isAuditMode = true;
                break;
            }
        }
    }
@endphp

@if($isAuditMode)
    <div id="audit-banner-container" class="container-fluid pt-4 px-3">
        <div class="alert alert-purple text-white shadow-sm mb-2 d-flex align-items-center justify-content-between flex-wrap" 
             style="background: linear-gradient(135deg, #6f42c1 0%, #4c2882 100%); border-radius: 12px; border: none; padding: 14px 20px; box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3) !important;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-white text-purple d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 42px; height: 42px; color: #6f42c1; font-size: 1.3rem; flex-shrink: 0;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold text-white"><i class="fas fa-edit mr-1"></i> MODE EDIT DATA AUDIT</h5>
                    <small style="color: #e9d8fd; font-size: 0.88rem;">Anda sedang mengedit salinan data khusus untuk keperluan <strong>Audit QC System</strong>. Data operasional utama tidak terpengaruh.</small>
                </div>
            </div>
            <div class="mt-2 mt-sm-0">
                <span class="badge badge-light px-3 py-2 text-purple font-weight-bold shadow-sm" style="color: #4c2882; font-size: 0.85rem; letter-spacing: 0.5px;">
                    <i class="fas fa-check-double mr-1 text-success"></i> AUDIT PERSISTENCE ACTIVE
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Pindahkan banner ke posisi paling atas di dalam .content-wrapper atau .main-panel agar tidak tertutup fixed navbar
            var $banner = $('#audit-banner-container');
            if ($('.content-wrapper').length) {
                $('.content-wrapper').prepend($banner);
            } else if ($('.main-panel').length) {
                $('.main-panel').prepend($banner);
            }

            // Otomatis ubah tombol simpan pada form edit audit menjadi gaya visual khas audit (Ungu)
            $('#submit-btn, button[type="submit"]').each(function() {
                if ($(this).text().toLowerCase().includes('simpan') || $(this).text().toLowerCase().includes('update')) {
                    $(this).removeClass('btn-primary btn-success').addClass('btn-purple')
                        .css({'background-color': '#6f42c1', 'border-color': '#5a32a3', 'color': '#ffffff'})
                        .html('<i class="fas fa-save mr-1"></i> Simpan Data Audit');
                }
            });

            // Tambahkan penanda badge pada header card form
            $('.card-title').each(function() {
                if ($(this).text().toLowerCase().includes('edit') && !$(this).find('.badge-audit-tag').length) {
                    $(this).append(' <span class="badge badge-purple badge-audit-tag ml-2" style="background-color: #6f42c1; color: white;"><i class="fas fa-shield-alt"></i> AUDIT MODE</span>');
                }
            });
        });
    </script>
    @endpush
@endif
