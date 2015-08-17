<?php

namespace ICT\Traits;

use ICT\Scopes\VisibilityScope;

trait Visibility
{
    /**
     * Boot the scope.
     * 
     * @return void
     */
    public static function bootVisibility()
    {
        $scope = new VisibilityScope;
        static::addGlobalScope($scope);

        /**
         * Need to look for deleting events because of conflict
         * with the SoftDeleting Trait which prevents the use of
         * $instance->delete() when element is not visible
         */
        self::deleting(function($instance) use($scope) {
            if($instance->hasSoftDeletes()) {
                if($instance->forceDeleting) {
                    $instance->withTrashed()->withHidden()->where($instance->getKeyName(), $instance->getKey())->forceDelete();
                } else {
                    $instance->deleted_at = $instance->freshTimestamp();
                    $instance->save();
                }
            }
        });
    }

    public function hasSoftDeletes() {
        $traits = class_uses($this);
        return in_array('SoftDeletes', $traits) || in_array('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }

    /**
     * Get the name of the column for applying the scope.
     * 
     * @return string
     */
    public function getVisibleColumn()
    {
        return defined('static::VISIBILITY_COLUMN') ? static::VISIBILITY_COLUMN : 'visible';
    }

    /**
     * Get the fully qualified column name for applying the scope.
     * 
     * @return string
     */
    public function getQualifiedVisibleColumn()
    {
        return $this->getTable().'.'.$this->getVisibleColumn();
    }

    /**
     * Get the query builder without the scope applied.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withHidden()
    {
        return with(new static)->newQueryWithoutScope(new VisibilityScope);
    }

}