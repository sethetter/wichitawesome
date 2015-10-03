<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
    /**
     * Tags relationship.
     * 
     * @access public
     * @return void
     */
    public function tags()
    {
        return $this->belongsToMany('ICT\Tag');
    }
}
