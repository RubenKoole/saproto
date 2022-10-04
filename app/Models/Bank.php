<?php

namespace Proto\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bank Account Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $iban
 * @property string $bic
 * @property string $machtigingid
 * @property bool $is_first
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Bank whereBic($value)
 * @method static Builder|Bank whereCreatedAt($value)
 * @method static Builder|Bank whereIban($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereIsFirst($value)
 * @method static Builder|Bank whereMachtigingid($value)
 * @method static Builder|Bank whereUpdatedAt($value)
 * @method static Builder|Bank whereUserId($value)
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @mixin Eloquent
 */
class Bank extends Model
{
    protected $table = 'bankaccounts';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('Proto\Models\User')->withTrashed();
    }
}
