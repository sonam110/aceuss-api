<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;

trait TopMostParentId {

	protected static function bootTopMostParentId()
    {
    	if (auth()->guard('api')->check()) {
	        // if user is superadmin - usertype admin
	        if (auth()->guard('api')->user()->user_type_id != '1') {
	        	
	        		static::creating(function ($model) {
			            $model->top_most_parent_id = auth()->guard('api')->user()->top_most_parent_id;
			        });
	        		static::addGlobalScope('top_most_parent_id', function (Builder $builder) {
		                $builder->where('top_most_parent_id', auth()->guard('api')->user()->top_most_parent_id);
		            });
	        	
	        }
	    }
    }

}