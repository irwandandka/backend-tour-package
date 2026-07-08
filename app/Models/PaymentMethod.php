<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    public const ID_GOPAY = '678eca5e-e0be-4d8d-9d29-f82758f999af';

    public const ID_BANK_TRANSFER = '318a7381-7879-4182-91a0-ef6f97772485';

    public const ID_MANDIRI_VA = '39ad0058-619b-4899-9251-fe2c37488472';

    public const ID_BCA_VA = 'bfeea698-6d0d-4861-a6f4-74c8ec51667d';

    protected $fillable = [
        'name',
        'description',
        'is_active',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
