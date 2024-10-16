<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public $incrementing = false;  // Disable auto-incrementing

    protected $keyType = 'string';  // Set the key type to string for UUID

    // Optionally, automatically generate a UUID for the primary key on creating a new record
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function countries()
    {
        return $this->hasMany(Country::class);
    }
}
