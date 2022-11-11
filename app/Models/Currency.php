<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @property string $id
 * @property string $name
 * @property string $currencyCode
 * @property int $exchangeRate
 * @method static Builder where(string $string, string $currencyCode)
 * @method static Currency create(array $contents)
 */
class Currency extends Model
{
    use HasUuids;

    protected $table = 'currency';

    public $timestamps = false;

    protected $fillable = ['name', 'currencyCode', 'exchangeRate'];
}
