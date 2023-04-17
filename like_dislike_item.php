
<!-- blade file  -->

<div class="item-card9-icons">
    @php
        $like_exist = App\Models\Like::where('property_id', 11)->where('user_id',Auth::id())->first();
        $like_count = App\Models\Like::where('property_id', 11)->count();
    @endphp
    <a href="javascript:void(0);" class="like_btn item-card9-icons1 wishlist" property-id="11" data-bs-toggle="tooltip" data-bs-placement="top" title="wishlist">
        <span class="like_icon__11">
            @if($like_exist)
            <i class="fa fa fa-heart text-danger"></i>
            @else
            <i class="fa fa fa-heart-o"></i>
            @endif
        </span>
        {{-- <i class="fa fa fa-heart-o" ></i></a> --}}
    <a href="javascript:void(0);" class="likeCount__11 item-card9-icons1 wishlist" property-id="11" data-bs-toggle="tooltip" data-bs-placement="top" title="wishlist"> {{ $like_count }} </a>
    
</div>


// <!-- Blade Script File -->
@section('scripts')
    <script>
        $('.like_btn').click(function(){
            let property_id = $(this).attr('property-id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('like_property') }}",
                type: "POST",
                data: {
                    property_id: property_id,
                },
                success: function(response){
                    $(".likeCount__" + response.property_id).html(response.count);
                    if (response.status == 400) {
                        $('.like_icon__'+response.property_id).html(`<i class="fa fa fa-heart-o"></i>`);
                        toastr.success("DisLiked");
                    } else if (response.status == 200) {
                        $('.like_icon__'+response.property_id).html(`<i class="fa fa fa-heart text-danger"></i>`);
                        toastr.success("Liked");
                    }

                }
            });
        });
    </script>
@endsection


<!-- route file  -->
Route::post('/like/property', [LikeController::class, 'like_property'])->name('like_property');


<!-- controller  -->
<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like_property(Request $request){

        $like = Like::where('user_id', Auth::id())->where('property_id',$request->property_id)->first();

        if(!$like){
            Like::create([
                'user_id' => Auth::id(),
                'property_id' => $request->property_id,
                'like_count' => 1,
            ]);

            $count = Like::where('property_id', $request->property_id)->count();

            return response()->json([
                'status' => 200,
                'count' => $count,
                'property_id' => $request->property_id,
            ]);
        }else{
            $like->delete();

            $count = Like::where('property_id', $request->property_id)->count();

            return response()->json([
                'status' => 400,
                'count' => $count,
                'property_id' => $request->property_id,
            ]);
        }

    }
}
