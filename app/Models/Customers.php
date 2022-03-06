<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'age',
        'location',
        'country_code',
    ];
}
