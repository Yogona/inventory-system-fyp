<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrumentsRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "store_id", 
        "instrument_id", 
        "quantity", 
        "days", 
        "allocatee", 
        "store_id", 
        "deadline", 
        "assignment_id"
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function instrument(){
        return $this->belongsTo(Instrument::class);
    }
}
