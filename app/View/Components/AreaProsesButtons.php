<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class AreaProsesButtons extends Component
{
    public $item;
    public $hasGroupUuid;
    public $queryString;
    public $isAuditPage;
    
    public function __construct($item, $isAuditPage = false)
    {
        $this->item = $item;
        $this->isAuditPage = $isAuditPage;
        $this->hasGroupUuid = request('group_uuid') ? true : false;
        $this->queryString = request('group_uuid') ? ('?group_uuid=' . request('group_uuid')) : '';
    }

    public function canAccess()
    {
        return Auth::check() && (
            Auth::user()->hasPermissionTo('view-area-proses') ||
            Auth::user()->hasPermissionTo('edit-area-proses') ||
            Auth::user()->hasPermissionTo('delete-area-proses')
        );
    }

    public function isViewOnlyRole()
    {
        return Auth::check() && 
               Auth::user()->hasPermissionTo('view-area-proses') && 
               !Auth::user()->hasPermissionTo('edit-area-proses') && 
               !Auth::user()->hasPermissionTo('delete-area-proses');
    }

    public function render()
    {
        return view('components.area-proses-buttons');
    }
}
