<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class ActionButtons extends Component
{
    public $item;

    public $routePrefix;

    public $permissionPrefix;

    public $showHistory;

    public $showView;

    public $showPrint;

    public $layout;

    public $isAuditPage;

    public $showQcVerificationLog;

    public function __construct($item, $routePrefix, $permissionPrefix = null, $showHistory = true, $showView = true, $showPrint = false, $layout = 'horizontal', $isAuditPage = null, $showQcVerificationLog = false)
    {
        $this->item = $item;
        $this->routePrefix = $routePrefix;
        $this->permissionPrefix = $permissionPrefix ?: $routePrefix;
        $this->showHistory = $showHistory;
        $this->showView = $showView;
        $this->showPrint = $showPrint;
        $this->layout = $layout;
        $this->showQcVerificationLog = $showQcVerificationLog;

        // Otomatis deteksi halaman audit dari URL jika prop tidak diisi secara manual
        $this->isAuditPage = $isAuditPage ?? (
            request()->is('*audit*') ||
            request()->routeIs('*audit*') ||
            request()->boolean('is_audit')
        );
    }

    protected function checkPermission($permission)
    {
        if (! Auth::check()) {
            return false;
        }
        try {
            return Auth::user()->hasPermissionTo($permission);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function canAccess()
    {
        if (! Auth::check()) {
            return false;
        }

        // Auditor (id_role 6 or role 'auditor') or Audit page always has access to action buttons
        if (Auth::user()->id_role == 6 || Auth::user()->role === 'auditor' || $this->isAuditPage || (in_array($this->permissionPrefix, ['wizard-proses1', 'wizard-packing', 'label-pemeriksaan'], true) && in_array((int) (Auth::user()->id_role ?? 0), [1, 2, 3, 4, 5], true))) {
            return true;
        }

        // View, Edit, or Delete permissions allow access to this action-buttons group at least
        return $this->checkPermission('view-'.$this->permissionPrefix) ||
               $this->checkPermission('edit-'.$this->permissionPrefix) ||
               $this->checkPermission('delete-'.$this->permissionPrefix);
    }

    public function isViewOnlyRole()
    {
        if (! Auth::check()) {
            return false;
        }

        if (Auth::user()->id_role == 6 || Auth::user()->role === 'auditor') {
            return true;
        }

        return $this->checkPermission('view-'.$this->permissionPrefix) &&
               ! $this->checkPermission('edit-'.$this->permissionPrefix) &&
               ! $this->checkPermission('delete-'.$this->permissionPrefix);
    }

    public function shouldShowHistory()
    {
        if (Auth::check() && (Auth::user()->id_role == 6 || Auth::user()->role === 'auditor')) {
            return false;
        }

        return $this->showHistory;
    }

    public function hasRoute($routeName)
    {
        return Route::has($routeName);
    }

    public function hasEditPermission()
    {
        if (Auth::user()->id_role == 6 || Auth::user()->role === 'auditor') {
            return false;
        }
        if (in_array($this->permissionPrefix, ['wizard-proses1', 'wizard-packing', 'label-pemeriksaan'], true) && (int) (Auth::user()->id_role ?? 0) === 3) {
            return false;
        }
        if (in_array($this->permissionPrefix, ['wizard-proses1', 'wizard-packing', 'label-pemeriksaan'], true) && in_array((int) (Auth::user()->id_role ?? 0), [1, 2, 5], true)) {
            return true;
        }

        return $this->checkPermission('edit-'.$this->permissionPrefix);
    }

    public function hasDeletePermission()
    {
        if (Auth::user()->id_role == 6 || Auth::user()->role === 'auditor') {
            return false;
        }

        return $this->checkPermission('delete-'.$this->permissionPrefix);
    }

    public function canEdit()
    {
        if (! $this->hasEditPermission()) {
            return false;
        }

        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        $modules = config('edit_limit.modules', []);

        // We use $this->routePrefix as the module key
        if (in_array(Auth::user()->id_role, $restrictedRoles) && isset($modules[$this->routePrefix])) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = Carbon::parse($this->item->created_at);

            return $createdAt->addHours($timeLimitHours)->isFuture();
        }

        return true;
    }

    public function getRemainingTime()
    {
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        $modules = config('edit_limit.modules', []);

        if (in_array(Auth::user()->id_role, $restrictedRoles) && isset($modules[$this->routePrefix])) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = Carbon::parse($this->item->created_at);
            $deadline = $createdAt->copy()->addHours($timeLimitHours);

            if ($deadline->isPast()) {
                return null; // Return null if expired, so badge shows "Expired"
            }

            $diffHours = now()->diffInHours($deadline, false);
            if ($diffHours < 1) {
                $diffMinutes = now()->diffInMinutes($deadline, false);

                return round($diffMinutes / 60, 1).'h'; // Convert to hours for consistency
            }

            return round($diffHours, 1).'h'; // Short format for badge
        }

        return null; // Return null for unlimited access
    }

    public function getEditDeadline()
    {
        $timeLimitHours = config('edit_limit.time_limit_hours', 48);

        return Carbon::parse($this->item->created_at)->addHours($timeLimitHours)->format('d M Y, H:i');
    }

    public function render()
    {
        return view('components.action-buttons');
    }
}
