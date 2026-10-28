<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// try column role
$role = \App\Models\Role::firstWhere('role', 'SPV QC');
if ($role) {
    echo "found via App\\Models\\Role. ID: " . $role->id_role . "\n";
    // Check Spatie User roles relation? Spatie permissions might be attached via custom logic or maybe Spatie tables are used directly
    $spatieRoleId = $role->id_role; // or just 4 or 5
    // let's just get all permissions since Spatie's Role model must be different?
}

// Spatie Role table
$spatieRole = \Spatie\Permission\Models\Role::find($role->id_role ?? 5);
if ($spatieRole) {
    echo json_encode($spatieRole->permissions->pluck('name'));
}
