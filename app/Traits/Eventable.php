<?php 

namespace ICT\Traits;

trait Eventable
{
    /**
     * Events relationship.
     * 
     * @access public
     * @return void
     */
    public function events()
    {
        return $this->hasMany('App\Event');
    }

    /**
     * Get upcoming.
     * 
     * @access public
     * @param mixed $query
     * @return void
     */
    public function scopeUpcoming($query)
    {
        return $query->where('end_time', '>=', new \DateTime())->orderBy('start_time', 'ASC');
    }

}