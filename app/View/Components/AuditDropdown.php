<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuditDropdown extends Component
{
    public $item;
    public $routePrefix;
    public $permissionPrefix;
    public $uuid;
    public $hasAuditVersion;

    public function __construct($item, $routePrefix, $permissionPrefix = null, $uuid = null, $hasAuditVersion = false)
    {
        $this->item          = $item;
        $this->routePrefix   = $routePrefix;
        $this->permissionPrefix = $permissionPrefix ?: $routePrefix;
        $this->uuid          = $uuid;
        $this->hasAuditVersion = $hasAuditVersion;
    }

    public function getItemUuid(): string
    {
        if ($this->uuid) {
            return $this->uuid;
        }
        if (is_object($this->item) && isset($this->item->uuid)) {
            return $this->item->uuid;
        }
        return '';
    }

    public function isAuditItem(): bool
    {
        if (is_object($this->item) && property_exists($this->item, 'is_audit')) {
            return (bool) $this->item->is_audit;
        }
        return false;
    }

    public function canCopyAudit(): bool
    {
        return Auth::check() &&
               Auth::user()->hasPermissionTo('create-' . $this->permissionPrefix);
    }

    public function canEditAudit(): bool
    {
        return Auth::check() &&
               Auth::user()->hasPermissionTo('edit-' . $this->permissionPrefix);
    }

    public function hasRoute(string $routeName): bool
    {
        return Route::has($routeName);
    }

    public function render()
    {
        return view('components.audit-dropdown');
    }
}
