<?php

namespace ICT\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        $slug = Str::slug($value);
        $slugCount = $this->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->count();

        $this->attributes['slug'] = ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }
}