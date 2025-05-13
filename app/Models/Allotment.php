<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Allotment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_detail_id',
        'transaction_id',
        'code',
        'period',
        'day1',
        'day2',
        'day3',
        'day4',
        'day5',
        'day6',
        'day7',
        'day8',
        'day9',
        'day10',
        'day11',
        'day12',
        'day13',
        'day14',
        'day15',
        'day16',
        'day17',
        'day18',
        'day19',
        'day20',
        'day21',
        'day22',
        'day23',
        'day24',
        'day25',
        'day26',
        'day27',
        'day28',
        'day29',
        'day30',
        'day31',
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

    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
