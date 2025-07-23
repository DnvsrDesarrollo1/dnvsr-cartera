<?php

namespace App\Traits;

trait LogsUserActivity
{
    /**
     * Log login activity
     */
    public function logLogin()
    {
        $this->logCustomActivity(
            "Usuario {$this->name} inició sesión",
            'login',
            ['ip' => request()->ip()]
        );
    }

    /**
     * Log logout activity
     */
    public function logLogout()
    {
        $this->logCustomActivity(
            "Usuario {$this->name} cerró sesión",
            'logout',
            ['ip' => request()->ip()]
        );
    }

    /**
     * Log role changes
     */
    public function logRoleChange($role, $action = 'assigned')
    {
        $this->logCustomActivity(
            "Rol '{$role}' fue {$action} al usuario {$this->name}",
            'role_change',
            ['role' => $role, 'action' => $action]
        );
    }

    /**
     * Log permission changes
     */
    public function logPermissionChange($permission, $action = 'assigned')
    {
        $this->logCustomActivity(
            "Permiso '{$permission}' fue {$action} al usuario {$this->name}",
            'permission_change',
            ['permission' => $permission, 'action' => $action]
        );
    }

    /**
     * Log important actions
     */
    public function logImportantAction($action, $details = [])
    {
        $this->logCustomActivity(
            "Usuario {$this->name} realizó la acción: {$action}",
            'important_action',
            $details
        );
    }
}
