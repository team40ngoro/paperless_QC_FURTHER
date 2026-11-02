<div id="bulkApprovalToolbar" class="d-none align-items-center flex-wrap mb-3 p-3 bg-light border rounded"
     data-url="{{ route($bulkApproveRoute) }}">
    @php
        $approvalRoleMap = $bulkApprovalRoleMap ?? [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5],
        ];
    @endphp
    <strong class="mr-3 mb-2 mb-md-0">
        <i class="fas fa-tasks mr-1 text-primary"></i>
        <span id="bulkApprovalCount">0</span> data dipilih
    </strong>
    @if(in_array(auth()->user()->id_role, $approvalRoleMap['qc'] ?? []))
        <button type="button" class="btn btn-success btn-sm mr-2 mb-2 mb-md-0 bulk-selected-approve" data-type="qc">
            <i class="fas fa-check mr-1"></i> Approve QC
        </button>
    @endif
    @if(in_array(auth()->user()->id_role, $approvalRoleMap['produksi'] ?? []))
        <button type="button" class="btn btn-primary btn-sm mr-2 mb-2 mb-md-0 bulk-selected-approve" data-type="produksi">
            <i class="fas fa-check mr-1"></i> Approve Produksi
        </button>
    @endif
    @if(in_array(auth()->user()->id_role, $approvalRoleMap['spv'] ?? []))
        <button type="button" class="btn btn-dark btn-sm mb-2 mb-md-0 bulk-selected-approve" data-type="spv">
            <i class="fas fa-check mr-1"></i> Approve SPV
        </button>
    @endif
</div>