<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code'];

    public $incrementing = false;

    public const STATUS_COMPLETED = 'd2cbbeb8-378e-4833-a3b6-b89395041676';
    public const STATUS_CANCELLED = 'cc3235dc-0a2f-4cc4-81d9-0916bab7919f';
    public const STATUS_ENTRY = '3a38a613-3de8-4ced-befe-9b2f52dcc102';
    public const STATUS_PAID = 'b9148d72-475d-4708-b9d3-384da010caec';
    public const STATUS_UNPAID = '3108ebe9-86b6-4b80-a4de-3984a705bb29';
    public const STATUS_PENDING = '312cf7e5-25c9-4848-be5e-3b7f47c853ff';
    public const STATUS_POSTED = '9a9469d3-1832-46b8-86eb-28208516e656';
    public const STATUS_REVISED = '667fbd6c-21e9-4d4e-b98f-d0cc621cf602';
    public const STATUS_EXPIRED = '7a685d58-0e09-4124-a300-a76cf412e60a';
    public const STATUS_ORDERED = 'bdfae7da-9145-11f0-96b2-5e65455fb537';

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
}
