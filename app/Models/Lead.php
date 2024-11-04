<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile', 'description', 'source', 'status'];

    /**
     * Get lead update to lead_id.
    */
    public function updates()
    {
        return $this->hasMany(LeadUpdate::class);
    }
}
