<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Passenger extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'gender',
        'title',
        'postal_code',
        'nationality',
        'passport_number',
        'passport_expiry_date',
        'passport_issue_date',
        'passport_issue_country',
        'birth_place',
        'birth_date',
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

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
