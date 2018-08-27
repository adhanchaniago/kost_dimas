<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['name','id_path','entry_date','exit_date','room_number','exit'];
    
    public function location()
    {
      return $this->belongsTo('App\Location','room_location');
    }
}
