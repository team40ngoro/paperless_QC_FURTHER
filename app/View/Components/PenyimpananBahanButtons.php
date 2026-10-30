<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class PenyimpananBahanButtons extends Component
{
    public $item;
    public $hasGroupUuid;
    public $queryString;
    public $canAccess;
    public $isViewOnlyRole;
    
    public function __construct($item)
    {
        $this->item = $item;
        $this->hasGroupUuid = request('group_uuid') ? true : false;
        $this->queryString = request('group_uuid') ? ('?group_uuid=' . request('group_uuid')) : '';
        
        // Set computed properties
        $this->canAccess = $this->checkCanAccess();
        $this->isViewOnlyRole = $this->checkIsViewOnlyRole();
    }

    private function checkCanAccess()
    {
        $allowedRoles = ['superadmin', 'admin', 'spv qc'];
        $userRole = strtolower(Auth::user()->role ?? '');
        return in_array($userRole, $allowedRoles);
    }

    private function checkIsViewOnlyRole()
    {
        $viewOnlyRoles = ['qc inspector', 'produksi'];
        $userRole = strtolower(Auth::user()->role ?? '');
        return in_array($userRole, $viewOnlyRoles);
    }

    public function render()
    {
        return view('components.penyimpanan-bahan-buttons');
    }
}
