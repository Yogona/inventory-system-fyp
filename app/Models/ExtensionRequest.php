<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtensionRequest extends Model
{
    use HasFactory;

    protected $guarded = ["creator"];

    public function assignment(){
        return $this->belongsTo(Assignment::class, "assignment");
    }

    public function requester(){
        return $this->belongsTo(User::class, "requester");
    }
}
