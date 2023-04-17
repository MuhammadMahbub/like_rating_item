
<!-- blade part  -->
@php
    $rate_count = App\Models\Rating::where('property_id', 11)->count();
    $rate_exist = App\Models\Rating::where('property_id', 11)->where('user_id', Auth::id())->first();
    $total_rating = App\Models\Rating::where('property_id', 11)->sum('rate_count');
    $avg_rate = ($total_rating/$rate_count);
@endphp

<div class="item-trans-rating">
    <div class="rating-stars block">
        <input type="number" readonly="readonly" class="rating-value star" name="rating-stars-value" value="{{ $rate_exist->rate_count + 1 ?? '' }}">
        <div class="rating-stars-container">
            <input type="hidden" name="property_id" class="property_id" value="11">
            <div class="rating-star sm">
                <i class="rateProperty fa fa-star" rate-value="1"></i>
            </div>
            <div class="rating-star sm  ">
                <i class="rateProperty fa fa-star" rate-value="2"></i>
            </div>
            <div class="rating-star sm  ">
                <i class="rateProperty fa fa-star" rate-value="3"></i>
            </div>
            <div class="rating-star sm ">
                <i class="rateProperty fa fa-star" rate-value="4"></i>
            </div>
            <div class="rating-star sm ">
                <i class="rateProperty fa fa-star" rate-value="5"></i>
            </div>
            <div class="rating-star sm ">
                <span class="ratePercent__11">{{ round($avg_rate, 1) }}</span>
            </div>
        </div>
    </div>
</div>


<script>
    $('.rateProperty').click(function(){
        let rate_value = $(this).attr('rate-value')
        let property_id = $('.property_id').val();
        // console.log(property_id);
        $.ajax({
            url: "{{ route('rate_property') }}",
            type: "POST",
            data: {
                rate_value: rate_value,
                property_id: property_id,
            },
            success: function(response){
                $(".ratePercent__" + response.property_id).html(response.avg_count);
                console.log(response);
            }
        });
    });
</script>


<!-- controller  -->

<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function rate_property(Request $request){

        // return Auth::id();
        $rate = Rating::where('user_id', Auth::id())->where('property_id',$request->property_id)->first();
        
        if(!$rate){
            $rating = new Rating();

            $rating->user_id = Auth::id();
            $rating->property_id = $request->property_id;
            $rating->rate_count = $request->rate_value;
            
            $rating->save();

        }else{
            $rating = Rating::find($rate->id);

            $rating->user_id = Auth::id();
            $rating->property_id = $request->property_id;
            $rating->rate_count = $request->rate_value;
            
            $rating->save();
        }

        $count = Rating::where('property_id', $request->property_id)->count();
        $total_rating =  Rating::where('property_id', $request->property_id)->sum('rate_count');
        $avg_count = round(($total_rating/$count), 1);

        return response()->json([
            'status' => 200,
            'property_id' => $request->property_id,
            'avg_count' => $avg_count,
        ]);
     
    }
}
