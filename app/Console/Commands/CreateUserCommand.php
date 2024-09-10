<?php

namespace App\Console\Commands;

use App\Enums\RoleNameEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Name of the new user');
        $user['email'] = $this->ask('Email of the new user');
        $user['password'] = $this->secret('Password of the new user');

        $roleName = $this->choice('Role of the new user', RoleNameEnum::getValues(), 0);
        $roleId = 1;

        if ($roleName === RoleNameEnum::DEVELOPER->value)
        {
            $roleId = 2;
        }

        elseif ($roleName === RoleNameEnum::TESTER->value)
        {
            $roleId = 3;
        }

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }

        $role = Role::where('id', $roleId)->first();
        if (!$role) {
            $this->error('Role not found');

            return;
        }

        DB::transaction(function () use ($user, $role) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt($user['password']),
                'role_id' => $role->id,
            ]);
        });

        $this->info('User: '.$user['email']. ' with role: '.$role->name. ' created successfully');
    }

    public function isolationLockExpiresAt(): \DateTimeInterface|\DateInterval
    {
        return now()->addMinutes(5);
    }
}
