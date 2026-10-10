<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

#[Signature('school:create-admin')]
#[Description('Create the first school administrator securely on a new installation')]
class SchoolCreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::query()->where('role', 'admin')->exists()) {
            $this->error('Akun admin sudah ada. Kelola akun melalui aplikasi.');

            return self::FAILURE;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Jalankan perintah ini secara interaktif agar kata sandi tidak muncul dalam riwayat shell.');

            return self::FAILURE;
        }

        $name = $this->ask('Nama admin');
        $email = $this->ask('Email admin');
        $password = $this->secret('Kata sandi admin (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi kata sandi admin');

        $validation = Validator::make(
            compact('name', 'email', 'password', 'confirmation'),
            [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')],
                'password' => ['required', 'string', 'min:12', 'same:confirmation'],
            ],
        );

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'active' => true,
        ]);

        $this->info('Akun admin berhasil dibuat. Masuk melalui halaman login aplikasi.');

        return self::SUCCESS;
    }
}
