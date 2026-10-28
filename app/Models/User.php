<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Plan;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'password',
        'email',
        'email_verified_at',
        'role',
        'id_role',
        'id_plan',
        'active_plan_id',
        'force_logout_after',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'force_logout_after' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * RELASI: Plan yang sedang dipilih Manager saat switch
     */
    public function activePlan()
    {
        return $this->belongsTo(Plan::class, 'active_plan_id');
    }

    /**
     * RELASI: Daftar Plan yang di-assign Superadmin ke Manager ini
     */
    public function allowedPlans()
    {
        return $this->belongsToMany(Plan::class, 'manager_plans', 'user_id', 'plan_id')
                    ->withTimestamps();
    }

    /**
     * LOGIKA: Cek apakah user memiliki role Manager
     */
    public function isManager(): bool
    {
        if ($this->roleModel) {
            return strtolower($this->roleModel->role) === 'manager';
        }
        return strtolower($this->attributes['role'] ?? '') === 'manager';
    }

    /**
     * KEAMANAN: Verifikasi apakah Manager boleh akses plan tertentu
     */
    public function canAccessPlan(int $planId): bool
    {
        if (!$this->isManager()) return false;
        return $this->allowedPlans()->where('plan.id', $planId)->exists();
    }

    /**
     * LOGIKA: Dapatkan objek Plan yang efektif untuk tampilan
     */
    public function getEffectivePlan(): ?Plan
    {
        if ($this->isManager() && $this->active_plan_id) {
            return $this->activePlan;
        }
        return $this->plan;
    }

    /**
     * LOGIKA: Dapatkan ID Plan yang efektif untuk query filter
     */
    /**
     * Accessor untuk id_plan - otomatis mengembalikan active_plan_id jika Manager sedang switch plan
     */
    public function getIdPlanAttribute($value)
    {
        if ($this->isManager() && $this->active_plan_id) {
            return (int) $this->active_plan_id;
        }
        return $value ? (int) $value : null;
    }

    public function getEffectivePlanId(): ?int
    {
        return (int) $this->id_plan;
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    /**
     * Accessor untuk role - otomatis menggunakan database role jika ada
     */
    public function getRoleAttribute($value)
    {
        // Jika ada role dari database, gunakan itu
        if ($this->relationLoaded('roleModel') ? $this->roleModel : $this->roleModel()->first()) {
            return $this->roleModel->role ?? $value;
        }
        
        // Jika tidak ada, gunakan role legacy dari kolom role
        return $value;
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
    /**
     * Method untuk login dengan username atau email
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)
                   ->orWhere('email', $username)
                   ->first();
    }

    /**
     * Override method untuk mendukung login dengan username atau email
     */
    public static function findByUsernameOrEmail($login)
    {
        return static::where('username', $login)
                    ->orWhere('email', $login)
                    ->first();
    }

    /**
     * Check permissions for QC forms
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // Removed hardcoded Superadmin bypass, so checkboxes take full control
        // if (strtolower($this->role) === 'superadmin') {
        //     return true;
        // }
        
        if (!$this->roleModel) {
            return false;
        }

        return $this->roleModel->permissions()->where('name', $permission)->exists();
    }
}
