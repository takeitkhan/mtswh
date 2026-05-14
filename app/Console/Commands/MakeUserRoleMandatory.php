<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class MakeUserRoleMandatory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-user-role-mandatory {role_name=Users : The name of the role to mark as mandatory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark a role as mandatory so it cannot be removed from users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->argument('role_name');
        
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role '{$roleName}' not found!");
            return 1;
        }
        
        $role->update(['is_mandatory' => 1]);
        
        $this->info("Role '{$roleName}' has been marked as mandatory!");
        $this->line("Users will automatically receive this role upon creation.");
        $this->line("This role cannot be removed from users.");
        
        return 0;
    }
}
