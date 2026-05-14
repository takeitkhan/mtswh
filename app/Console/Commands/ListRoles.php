<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class ListRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available roles in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = Role::all();
        
        if ($roles->isEmpty()) {
            $this->info('No roles found in the system.');
            return 0;
        }
        
        $this->line("\n=== Available Roles ===\n");
        
        foreach ($roles as $role) {
            $mandatory = $role->is_mandatory ? ' [MANDATORY]' : '';
            $this->line("ID: {$role->id} | Name: {$role->name} | Code: {$role->code} | Type: {$role->type}{$mandatory}");
        }
        
        $this->line("\n");
        
        return 0;
    }
}
