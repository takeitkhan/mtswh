<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use App\Models\Roleuser;

class AssignMandatoryRolesToExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-mandatory-roles-to-existing-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all mandatory roles to existing users who don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mandatoryRoles = Role::where('is_mandatory', 1)->get();
        
        if ($mandatoryRoles->isEmpty()) {
            $this->info('No mandatory roles found.');
            return 0;
        }
        
        $users = User::all();
        $assignmentCount = 0;
        
        foreach ($users as $user) {
            foreach ($mandatoryRoles as $role) {
                // Check if user already has this role
                $existingRole = Roleuser::where('user_id', $user->id)
                                        ->where('role_id', $role->id)
                                        ->first();
                
                // If not, create it
                if (!$existingRole) {
                    Roleuser::create([
                        'role_id' => $role->id,
                        'user_id' => $user->id,
                        'warehouse_id' => null,
                    ]);
                    $assignmentCount++;
                    $this->line("✓ Assigned '{$role->name}' to user '{$user->name}' (ID: {$user->id})");
                }
            }
        }
        
        $this->info("\n✓ Successfully assigned {$assignmentCount} mandatory role(s) to users!");
        
        return 0;
    }
}
