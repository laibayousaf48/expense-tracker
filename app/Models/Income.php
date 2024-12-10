<?php

namespace App\Models;

use App\Models\IncomeSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['income_source_id', 'amount', 'description', 'date'];

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }
}
