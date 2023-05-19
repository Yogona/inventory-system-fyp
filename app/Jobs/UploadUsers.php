<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UploadUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $usersData;

    /**
     * Create a new job instance.
     */
    public function __construct($records, $user)
    {
        $this->usersData = $records;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->usersData as $record){
            User::create([
                "first_name"     => $record["firstName"],
                "last_name"      => $record["lastName"],
                "username"      => $record["username"],
                "gender"        => $record["gender"],
                "email"         => $record["email"],
                "phone"         => $record["phone"],
                "role_id"       => $record["roleId"],
                "department_id" => $record["departId"],
                "password"      => Hash::make($record["phone"])
            ]);

        }
    }
}
