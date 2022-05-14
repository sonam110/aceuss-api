<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;

trait TopMostParentId {

	protected static function bootTopMostParentId()
    {
    	if (auth()->guard('api')->check()) 
    	{
	        // if user is superadmin - usertype admin OT top_most_parent_id=1
	        if ((auth()->guard('api')->user()->top_most_parent_id==1)) 
	        {
	        	//nothing heppen
	        }
	        else
	        {	        	
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