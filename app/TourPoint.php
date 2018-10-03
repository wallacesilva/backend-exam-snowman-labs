<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourPoint extends Model
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
     * Calculate distance of points
     * Later calculate in Database/SQL, can be better performance
     * 
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @return float Distance between points in KM
     */
    public function distanceInKilometers(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo
    ) {
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometers = $miles * 1.609344;

        logger()->info('kilometers: '.$kilometers);

        return $kilometers;
    }
}
