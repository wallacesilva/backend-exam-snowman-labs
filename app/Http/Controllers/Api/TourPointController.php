<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Socialite;
use Validator;
use App\TourPoint;
use App\Checkin;

class TourPointController extends Controller
{
    /**
     * Define name of points in cache
     * @var String
     */
    public $cached_points_name = 'cached_points';

    /**
     * Define duration of points in cache, in minutes
     * @var Integer
     */
    public $cached_points_duration = 0;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data['error'] = false;

        // in minutes
        $points_duration = $this->cached_points_duration;

        $points_default_distance = $request->input('distance', 1); // in kilometers, default 1

        $geolocation = null;

        // if has lat_long. Actual location
        // later get geolocation from ip
        if (!is_null($request->input('lat_long'))) {

            $lat_long = $request->input('lat_long');

            list($lat, $lng) = explode(',', $lat_long);

            $geolocation = ['latitude' => $lat, 'longitude' => $lng];
        }

        // make cache from public points only
        $cachepoints = Cache::remember($this->cached_points_name, $points_duration, function () use ($points_default_distance, $geolocation) {

            // cached points
            $points = collect([]);

            // get caches public
            $publicpoints = TourPoint::where('visibility', 'public')
                                    ->get();
            
            // define empty collection
            $loggedpoints = collect([]);

            // add tour points to user logged
            $publicpoints->map(function ($item, $key) use ($points, $points_default_distance, $geolocation) {

                // add only in radius distance
                if (is_null($geolocation) || $item->distanceInKilometers($item->latitude, $item->longitude, $geolocation['latitude'], $geolocation['longitude']) <= $points_default_distance) {
                    $points->push($item);
                }
            });

            return $points;
        });
            
        // get caches from user, if logged
        if (auth()->check()) {

            $loggedpoints = TourPoint::where('visibility', 'private')
                                    ->where('user_id', auth()->user()->id)
                                    ->get();

            // add tour points of user logged to cache
            $loggedpoints->map(function ($item, $key) use ($cachepoints, $points_default_distance, $geolocation) {

                // add only in radius distance
                if (is_null($geolocation) || $item->distanceInKilometers($item->latitude, $item->longitude, $geolocation['latitude'], $geolocation['longitude']) <= $points_default_distance) {
                    $cachepoints->push($item);
                }

            });

        } else {

            // if user not logged can see only
            $cachepoints = $cachepoints->filter(function ($itemValue, $key) {
                // return in_array($itemValue->category, ['park', 'museum']);
                return true;
            });

        }

        $data['points'] = $cachepoints;

        return response()->json($data, 200);
    }

    /**
     * Display a listing of the resource by User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listByUser(Request $request)
    {
        // check if user logged to add tour point
        if (!auth()->check()) {

            $data['error'] = true;
            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //
        }

        $points = TourPoint::where('user_id', auth()->user()->id)
                            ->get();
        
        $data['error'] = false;
        $data['points'] = $points;

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // check if user logged to add tour point
        if (!auth()->check()) {

            $data['error'] = true;
            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //
        }

        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:190',
            'latitude' => 'required',
            'longitude' => 'required',
            'category' => 'required|in:park,museum,restaurant',
            'visibility' => 'required|in:public,private',
        ]);

        // if fails return errors
        if ($validator->fails()) {
            $data['error'] = true;
            $data['errors'] = $validator->errors();

            return response()->json($data, 400);
        }

        $data['error'] = false;

        // clear inputs and add to model
        $point = new TourPoint;
        $point->name = $request->input('name');
        $point->latitude = $request->input('latitude');
        $point->longitude = $request->input('longitude');
        $point->category = $request->input('category');
        $point->visibility = $request->input('visibility');
        $point->user_id = auth()->user()->id;

        // persist on database
        $point->save();

        // clear cache if new public point
        if ($point->visibility == 'public') {

            // clear cache
            Cache::forget($this->cached_points_name);
        }

        $data['point'] = $point;

        $data['message'] = 'Stored with success!';

        return response()->json($data, 201);
    }

    /**
     * Store a TourPoint checkIn of User in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $tourPointId
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request, int $tourPointId)
    {
        // check if user logged to add tour point checkin
        if (!auth()->check()) {

            $data['error'] = true;

            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //

        }

        try {
            
            $point = TourPoint::findOrFail($tourPointId);

        } catch (ModelNotFoundException $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 404);

        } catch (Exception $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 400);
        }

        // clear inputs and add to model
        $checkin = new Checkin;
        $checkin->tourpoint_id = $tourPointId;
        $checkin->user_id = auth()->user()->id;

        // persist on database
        $checkin->save();

        $data['point'] = $point;

        $data['error'] = false;
        $data['message'] = sprintf('Checkin "%s" with success!', $point->name);

        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
        $data['error'] = false;

        try {
            
            $point = TourPoint::findOrFail($id);

        } catch (ModelNotFoundException $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 404);

        } catch (Exception $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 400);
        }
        
        // check visibility of point
        if ($point->visibility == 'private' || (!auth()->check() || auth()->user()->id != $point->user_id)) {

            $data['error'] = true;

            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //
        }

        $data['point'] = $point;

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // check if is user, only user can change tour point
        if (!auth()->check() || auth()->user()->id != $point->user_id) {

            $data['error'] = true;

            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //
        }

        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:190',
            'latitude' => 'required',
            'longitude' => 'required',
            'category' => 'required|in:park,museum,restaurant',
            'visibility' => 'required|in:public,private',
        ]);

        // if fails return errors
        if ($validator->fails()) {

            $data['error'] = true;
            $data['message'] = 'Has validation errors';
            $data['errors'] = $validator->errors();

            return response()->json($data, 400);

        }

        $data['error'] = false;

        try {
            
            $point = TourPoint::findOrFail($id);

        } catch (ModelNotFoundException $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 404);

        } catch (Exception $e) {
            
            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 400);
        }
        
        // clear inputs and add to model
        $point->name = $request->input('name');
        $point->latitude = $request->input('latitude');
        $point->longitude = $request->input('longitude');
        $point->category = $request->input('category');
        $point->visibility = $request->input('visibility');

        // persist on database
        $point->save();

        // clear cache if new public point
        if ($point->visibility == 'public') {

            // clear cache
            Cache::forget($this->cached_points_name);
        }

        $data['point'] = $point;

        $data['message'] = 'Updated with success!';

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
        $data['error'] = false;
        $tour_point = null;

        try {

            $tour_point = TourPoint::findOrFail($id);

        } catch (ModelNotFoundException $e) {
            
            $data['error'] = true;

            $data['message'] = $e->getMessage();

            return response()->json($data, 404);

        } catch (Exception $e) {

            $data['error'] = true;
            $data['message'] = $e->getMessage();

            return response()->json($data, 400);

        }

        // check if is user, only user can change tour point
        if (!auth()->check() || auth()->user()->id != $tour_point->user_id) {

            $data['error'] = true;

            $data['message'] = 'Unauthorized';

            return response()->json($data, 401); //
        }
        
        $data['point'] = $tour_point;

        // persist on database, remove/destroy, without softdeletes
        $tour_point->delete();

        // clear cache if was public point
        if ($tour_point->visibility == 'public') {

            // clear cache
            Cache::forget($this->cached_points_name);
        }

        $data['message'] = 'Deleted with success!';

        return response()->json($data, 200);
    }
}
