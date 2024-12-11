<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncomeSource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
