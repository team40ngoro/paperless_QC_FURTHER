<script>
    document.addEventListener('DOMContentLoaded', function() {
        const $toolbar = $('#bulkApprovalToolbar');
        const $selectAll = $('#bulkApprovalSelectAll');
        const rowSelector = '.bulk-approval-checkbox';

        function updateBulkApprovalSelection() {
            const selectedCount = $(rowSelector + ':checked').length;
            $('#bulkApprovalCount').text(selectedCount);
            $toolbar.toggleClass('d-none', selectedCount === 0)
                .toggleClass('d-flex', selectedCount > 0);
            $selectAll.prop('checked', selectedCount > 0 && selectedCount === $(rowSelector).length);
        }

        $selectAll.on('change', function() {
            $(rowSelector).prop('checked', this.checked);
            updateBulkApprovalSelection();
        });

        $(document).on('change', rowSelector, updateBulkApprovalSelection);

        $(document).on('click', '.bulk-selected-approve', function() {
            const $button = $(this);
            const type = $button.data('type');
            const uuids = $(rowSelector + ':checked').map(function() {
                return $(this).data('uuid');
            }).get();

            if (!uuids.length) {
                return;
            }

            if (!confirm('Setujui ' + uuids.length + ' data terpilih sebagai ' + type.toUpperCase() + '?')) {
                return;
            }

            const originalText = $button.html();
            $('.bulk-selected-approve').prop('disabled', true);
            $button.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

            $.ajax({
                url: $toolbar.data('url'),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type,
                    uuids: uuids
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1600,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert(response.message);
                            location.reload();
                        }
                        return;
                    }

                    alert('Gagal: ' + response.message);
                    $('.bulk-selected-approve').prop('disabled', false);
                    $button.html(originalText);
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Terjadi kesalahan saat memproses approval.';
                    alert(message);
                    $('.bulk-selected-approve').prop('disabled', false);
                    $button.html(originalText);
                }
            });
        });

        updateBulkApprovalSelection();
    });
</script>
