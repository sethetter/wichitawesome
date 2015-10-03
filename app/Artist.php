<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
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
