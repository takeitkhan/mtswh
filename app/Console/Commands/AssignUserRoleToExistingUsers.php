<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Roleuser;
use Illuminate\Console\Command;

class AssignUserRoleToExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-user-role-to-existing-users {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the mandatory "User" role to all existing users who do not have it';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the User role (assuming it has id = 3 or code = 'user')
        $userRole = Role::where('code', 'user')->first();
        
        if (!$userRole) {
            $this->error('❌ "User" role not found in the database!');
            return Command::FAILURE;
        }

        // Get all users
        $users = User::all();
        $this->info("📊 Found " . $users->count() . " users in total");

        // Count users who already have the User role
        $usersWithRole = Roleuser::where('role_id', $userRole->id)->distinct('user_id')->count();
        $this->info("✓ Users already having 'User' role: " . $usersWithRole);

        // Find users who don't have the User role
        $usersNeedingRole = User::whereDoesntHave('roles', function($query) use ($userRole) {
            $query->where('role_id', $userRole->id);
        })->get();

        $countNeedingRole = $usersNeedingRole->count();
        $this->info("⚠️  Users missing 'User' role: " . $countNeedingRole);

        if ($countNeedingRole == 0) {
            $this->info("✅ All users already have the 'User' role!");
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$this->option('force')) {
            if (!$this->confirm("Do you want to assign 'User' role to " . $countNeedingRole . " users?")) {
                $this->info("❌ Operation cancelled");
                return Command::SUCCESS;
            }
        }

        // Assign the User role to users who don't have it
        $assignedCount = 0;
        $this->withProgressBar($usersNeedingRole, function ($user) use ($userRole, &$assignedCount) {
            // Check if they already have this specific role (double-check)
            $existing = Roleuser::where('user_id', $user->id)
                                ->where('role_id', $userRole->id)
                                ->first();
            
            if (!$existing) {
                Roleuser::create([
                    'role_id' => $userRole->id,
                    'user_id' => $user->id,
                    'warehouse_id' => null,
                ]);
                $assignedCount++;
            }
        });

        $this->newLine();
        $this->info("✅ Successfully assigned 'User' role to " . $assignedCount . " users!");
        
        return Command::SUCCESS;
    }
}
