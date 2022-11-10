<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @property string $id
 * @property string $name
 * @property string $currencyCode
 * @property int $exchangeRate
 * @method static Builder where(string $string, string $currencyCode)
 */
class Currency extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'currency';

    public $timestamps = false;
}
