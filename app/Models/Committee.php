<?php

namespace Proto\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'committees';

    /**
     * @return mixed All events organized by this committee.
     */
    public function organizedActivities() {
        return $this->hasMany('Proto\Models\Activity', 'organizing_committee');
    }

    /**
     * @return mixed All activities at which this committee helped out.
     */
    public function helpedActivities() {
        return $this->hasMany('Proto\Models\Activity', 'committees_events')->withPivot(array('amount', 'id'))->withTimestamps();
    }

    /**
     * @return mixed All users associated with this committee.
     */
    public function users()
    {
        return $this->belongsToMany('Proto\Models\User', 'committees_users')->withPivot(array('id', 'start', 'end', 'role', 'edition'))->withTimestamps()->orderBy('pivot_start', 'desc');
    }

    public function image() {
        return $this->belongsTo('Proto\Models\StorageEntry');
    }

    public function allmembers() {

        $members = array('editions' => [], 'members' => ['current' => [], 'past' => []]);

        foreach ($this->users as $user) {
            if ($user->pivot->edition) {
                $members['editions'][$user->pivot->edition][] = $user;
            } else {
                if (!$user->pivot->end || date('U', strtotime($user->pivot->end)) > date('U')) {
                    $members['members']['current'][] = $user;
                } else {
                    $members['members']['past'][] = $user;
                }
            }
        }

        return $members;

    }

    protected $fillable = ['name', 'slug', 'description', 'public'];
}