<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "file_path", "creator", "assignee", "store_id"
    ];

    public function instrumentsRequests(){
        return $this->hasMany(InstrumentsRequest::class, "assignment_id");
    }
}
