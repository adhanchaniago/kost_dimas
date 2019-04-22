<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['name','id_path','entry_date','exit_date','room_number','exit','last_billed'];
    
    public function location()
    {
      return $this->belongsTo('App\Location','room_location');
    }

    public function roomType()
    {
      return $this->belongsTo('App\RoomDetail','room_type');
    }
}
