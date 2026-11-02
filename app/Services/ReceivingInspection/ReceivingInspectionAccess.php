<?php

namespace App\Services\ReceivingInspection;

use App\Exceptions\ReceivingInspectionException;
use App\Models\ReceivingInspection;
use App\Models\User;

class ReceivingInspectionAccess
{
    public const PRODUCTION_INPUT_CHECKER = 'PRODUCTION_INPUT_CHECKER';
    public const QC_AUDITOR = 'QC_AUDITOR';
    public const ADMIN = 'ADMIN';

    public function roleFor(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $role = strtolower(trim((string) ($user->role ?? '')));
        if ($role !== '') {
            foreach (config('receiving_inspection.roles', []) as $resolvedRole => $aliases) {
                if (in_array($role, $aliases, true)) {
                    return $resolvedRole;
                }
            }
        }

        // Fallback by id_role if available
        $idRole = (int) ($user->id_role ?? 0);
        if (in_array($idRole, [2, 9], true)) {
            return self::PRODUCTION_INPUT_CHECKER;
        }
        if (in_array($idRole, [3, 4, 6, 10], true)) {
            return self::QC_AUDITOR;
        }
        if (in_array($idRole, [1, 5, 7], true)) {
            return self::ADMIN;
        }

        return null;
    }

    public function isAdmin(?User $user): bool
    {
        return $this->roleFor($user) === self::ADMIN;
    }

    public function isProduction(?User $user): bool
    {
        return $this->roleFor($user) === self::PRODUCTION_INPUT_CHECKER;
    }

    public function isQc(?User $user): bool
    {
        return $this->roleFor($user) === self::QC_AUDITOR;
    }

    public function canCreate(?User $user): bool
    {
        return in_array($this->roleFor($user), [self::PRODUCTION_INPUT_CHECKER, self::ADMIN], true);
    }

    public function canEditSection(?User $user, ReceivingInspection $record, string $section): bool
    {
        $role = $this->roleFor($user);
        if ($section === 'PRODUCTION') {
            return $role === self::PRODUCTION_INPUT_CHECKER && $record->prod_status === 'DRAFT';
        }
        if ($section === 'QC') {
            return $role === self::QC_AUDITOR && $record->qc_status === 'DRAFT';
        }
        return false;
    }

    public function canSubmitSection(?User $user, ReceivingInspection $record, string $section): bool
    {
        return $this->canEditSection($user, $record, $section);
    }

    public function canReopen(?User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function canDelete(?User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function canViewSection(?User $user, string $section): bool
    {
        $role = $this->roleFor($user);
        if ($role === self::ADMIN) {
            return true;
        }
        if ($section === 'PRODUCTION') {
            return $role === self::PRODUCTION_INPUT_CHECKER;
        }
        if ($section === 'QC') {
            return $role === self::QC_AUDITOR;
        }
        return false;
    }

    public function canViewAudit(?User $user, ReceivingInspection $record): bool
    {
        $role = $this->roleFor($user);
        if ($role === self::ADMIN || $role === self::QC_AUDITOR) {
            return true;
        }
        if ($role === self::PRODUCTION_INPUT_CHECKER) {
            return (int) $record->created_by === (int) $user->id
                || (int) $record->prod_filled_by === (int) $user->id;
        }
        return false;
    }

    public function permissions(?User $user, ReceivingInspection $record): array
    {
        return [
            'canEditProduction' => $this->canEditSection($user, $record, 'PRODUCTION'),
            'canEditQc' => $this->canEditSection($user, $record, 'QC'),
            'canSubmitProduction' => $this->canSubmitSection($user, $record, 'PRODUCTION'),
            'canSubmitQc' => $this->canSubmitSection($user, $record, 'QC'),
            'canReopen' => $this->canReopen($user),
            'canDelete' => $this->canDelete($user),
            'canCreate' => $this->canCreate($user),
            'canViewProduction' => $this->canViewSection($user, 'PRODUCTION'),
            'canViewQc' => $this->canViewSection($user, 'QC'),
            'canViewAudit' => $this->canViewAudit($user, $record),
            'role' => $this->roleFor($user),
        ];
    }

    public function assertCanCreate(?User $user): void
    {
        if (!$this->canCreate($user)) {
            throw new ReceivingInspectionException('Only a Production Input Checker or Admin can create an inspection record.', 403);
        }
    }

    public function assertCanEditSection(?User $user, ReceivingInspection $record, string $section): void
    {
        $role = $this->roleFor($user);
        $expectedRole = $section === 'PRODUCTION' ? self::PRODUCTION_INPUT_CHECKER : self::QC_AUDITOR;
        $status = $section === 'PRODUCTION' ? $record->prod_status : $record->qc_status;

        if ($role !== $expectedRole) {
            throw new ReceivingInspectionException('You do not have permission to edit the ' . $section . ' section.', 403);
        }

        if ($status !== 'DRAFT') {
            throw ReceivingInspectionException::conflict('The ' . $section . ' section has already been submitted.');
        }
    }

    public function assertCanReopen(?User $user): void
    {
        if (!$this->canReopen($user)) {
            throw new ReceivingInspectionException('Only an Admin can re-open a submitted section.', 403);
        }
    }

    public function assertCanDelete(?User $user): void
    {
        if (!$this->canDelete($user)) {
            throw new ReceivingInspectionException('Only an Admin can delete an inspection record.', 403);
        }
    }
}
