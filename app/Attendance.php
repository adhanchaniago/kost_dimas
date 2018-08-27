<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['atd_date','room_location','guest_id'];

    public function guest()
    {
      return $this->belongsTo('App\Guest','guest_id');
    }

    public function location()
    {
      return $this->belongsTo('App\Location','room_location');
    }
}
