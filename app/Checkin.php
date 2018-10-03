<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The relationship with user.
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The relationship with Tour Point.
     *
     */
    public function tourPoint()
    {
        return $this->belongsTo('App\TourPoint');
    }
}
