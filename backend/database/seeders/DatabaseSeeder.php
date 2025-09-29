<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Users
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'name' => "User $i",
                'email' => "user{$i}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // default password
                'remember_token' => Str::random(10),
                'balance' => rand(1000, 10000),
                'update_counter' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($users);

        // Get all user IDs
        $userIds = User::pluck('id')->toArray();

        $transactions = [];

        for ($i = 1; $i <= 20; $i++) {
            // Pick a random sender
            $sender_id = $userIds[array_rand($userIds)];

            // Pick a random receiver different from sender
            do {
                $receiver_id = $userIds[array_rand($userIds)];
            } while ($receiver_id === $sender_id);

            $amount = rand(100, 1000);
            $commission = round($amount * 0.02, 2); // 2% commission fee

            $transactions[] = [
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'amount' => $amount,
                'commission_fee' => $commission,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ];
        }

        // Insert transactions into DB
        DB::table('transactions')->insert($transactions);

        // 3. Optional: Seed Personal Access Tokens (for API testing)
        $tokens = [];
        foreach ($users as $index => $user) {
            $tokens[] = [
                'tokenable_type' => 'App\Models\User',
                'tokenable_id' => $index + 1,
                'name' => 'Test Token',
                'token' => Str::random(64),
                'abilities' => '["*"]',
                'last_used_at' => null,
                'expires_at' => Carbon::now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('personal_access_tokens')->insert($tokens);

        // 4. Optional: Seed Password Resets
        DB::table('password_resets')->insert([
            'email' => 'user1@example.com',
            'token' => Str::random(64),
            'created_at' => now(),
        ]);

        // 5. Optional: Seed Failed Jobs
        DB::table('failed_jobs')->insert([
            'uuid' => Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'TestJob', 'data' => []]),
            'exception' => 'Test Exception',
            'failed_at' => now(),
        ]);
    }
}