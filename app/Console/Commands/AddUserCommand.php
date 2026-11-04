<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('user:add')]
#[Description('Create a new passwordless user account')]
class AddUserCommand extends Command
{
	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$name = $this->ask('Name');
		$email = $this->ask('Email address');

		$validator = Validator::make(['name' => $name, 'email' => $email], [
			'name' => ['required', 'string', 'max:255'],
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				'unique:users,email',
			],
		]);

		if ($validator->fails()) {
			foreach ($validator->errors()->all() as $error) {
				$this->error($error);
			}

			return self::FAILURE;
		}

		$user = User::create([
			'name' => $name,
			'email' => $email,
			'password' => null,
		]);

		$this->info("User {$user->email} created successfully.");

		return self::SUCCESS;
	}
}
