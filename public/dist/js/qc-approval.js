/**
 * QC Sistem - Universal Approval Button Handler
 * Handles .approve-btn click for all QC modules based on current URL.
 */
$(document).on('click', '.approve-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const type = $(this).data('type');
    const button = $(this);

    const typeNames = { 'qc': 'QC', 'produksi': 'Produksi', 'spv': 'SPV' };

    if (!confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
        return;
    }

    button.prop('disabled', true);
    button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    // Determine approve URL based on current page path
    const currentPath = window.location.pathname;
    const routeMap = [
        ['input-metal-detector', 'input-metal-detector'],
        ['persiapan-bahan-forming', 'persiapan-bahan-forming'],
        ['persiapan-bahan-emulsi', 'persiapan-bahan-emulsi'],
        ['persiapan-bahan-better', 'persiapan-bahan-better'],
        ['ketidaksesuaian-plastik', 'ketidaksesuaian-plastik'],
        ['ketidaksesuaian-benda-asing', 'ketidaksesuaian-benda-asing'],
        ['dokumentasi', 'dokumentasi'],
        ['chillroom', 'chillroom'],
        ['seasoning', 'seasoning'],
        ['shoestring', 'shoestring'],
        ['rebox', 'rebox'],
        ['produk-non-forming', 'produk-non-forming'],
        ['produk-forming', 'produk-forming'],
        ['produk-yum', 'produk-yum'],
        ['timbangan', 'timbangan'],
        ['thermometer', 'thermometer'],
    ];

    let segment = 'persiapan-bahan-forming'; // default fallback
    for (const [pattern, route] of routeMap) {
        if (currentPath.includes(pattern)) {
            segment = route;
            break;
        }
    }

    const approveUrl = `/paperless_futher/qc-sistem/${segment}/${id}/approve`;

    $.ajax({
        url: approveUrl,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            type: type
        },
        success: function (response) {
            if (response.success) {
                button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                    .addClass('btn-success')
                    .html('<i class="fas fa-check-circle"></i> Approved');
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                alert('Gagal menyetujui data: ' + response.message);
                button.prop('disabled', false);
                button.html('<i class="fas fa-check"></i> ' + typeNames[type].toUpperCase());
            }
        },
        error: function (xhr) {
            let errorMessage = 'Terjadi kesalahan saat menyetujui data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
            button.prop('disabled', false);
            button.html('<i class="fas fa-check"></i> ' + typeNames[type].toUpperCase());
        }
    });
});

/**
 * Universal Delete Button Handler (.delete-btn)
 * Detects module from URL to build the correct DELETE route.
 */
$(document).on('click', '.delete-btn', function (e) {
    e.preventDefault();
    const uuid = $(this).data('uuid');
    if (!uuid || !confirm('Yakin ingin menghapus data ini?')) return;

    const currentPath = window.location.pathname;
    const routeMap = [
        ['persiapan-bahan-emulsi', 'persiapan-bahan-emulsi'],
        ['persiapan-bahan-forming', 'persiapan-bahan-forming'],
        ['persiapan-bahan-better', 'persiapan-bahan-better'],
    ];

    let segment = 'persiapan-bahan-forming';
    for (const [pattern, route] of routeMap) {
        if (currentPath.includes(pattern)) {
            segment = route;
            break;
        }
    }

    const form = $('<form>', {
        'method': 'POST',
        'action': `/paperless_futher/qc-sistem/${segment}/${uuid}`
    });

    form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': $('meta[name="csrf-token"]').attr('content') }));
    form.append($('<input>', { 'type': 'hidden', 'name': '_method', 'value': 'DELETE' }));
    $('body').append(form);
    form.submit();
});
