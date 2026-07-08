<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;
    protected $table='user_scan';
    protected $fillable=[
    'user_id',
    'tool_name',
    'input_data',
    'result_data',
 ];

    protected $casts = [
        'result_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
   
