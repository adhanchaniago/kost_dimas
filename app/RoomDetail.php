<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    protected $fillable = ['monthly_rate','daily_rate','room_location','room_type'];

    public function room_location()
    {
      return $this->belongsTo('App\Location','room_location');
    }
}
