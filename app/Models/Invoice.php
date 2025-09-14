<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'status_id',
        'currency_id',
        'amount',
        'amount_base',
        'rate',
        'notes',
        'url',
        'invoice_date',
        'due_date',
        'paid_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
