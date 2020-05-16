<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Event;
use App\UserPayment;

use App\Http\Requests\Events\BuyEvent;

use App\Http\Resources\Events\EventResource;
use App\Http\Resources\UserCategories\UserCategoryResource;

use App\Engines\MambuEngine;
use App\UserCategory;
use Carbon\Carbon;
use DB;

class EventController extends Controller
{
    public $mambuEngine;

    public function __construct(MambuEngine $mambuEngine)
    {
        $this->mambuEngine  = $mambuEngine;
    }

    public function index(Request $request)
    {
        $input  = $request->all();

        $events = Event::query();

        if(@$input['category_id'] != null){
            $events->where('category_id', $input['category_id']);
        }

        $collection = EventResource::collection($events->get());

        return $this->sendResponse($collection->values()->all(), 'Events retrieved sucessfully');
    }

    public function show($id)
    {
        $event = Event::find($id);

        return $this->sendResponse(new EventResource($event), 'Event is successfully retrieved');
    }

    public function buy(BuyEvent $request)
    {
        $input      = $request->all();

        $loggedUser = Auth::user();
        $event      = Event::find($input['event_id']);

        if($event == null){
            return $this->sendError('Event is not exists', 422);
        }

        // Deduct Balance
        $transferBalance = $this->mambuEngine->transferBalance($loggedUser->ext_account_id, $event->price, 'transfer');

        if(!$transferBalance['result']){
            return $this->sendError($transferBalance['message'], 422);
        }
        // End Deduct Balance

        // Add Cashback
        $addCashback = $this->mambuEngine->transferBalance($loggedUser->ext_account_id, $event->price * $event->cashback, 'deposit');

        if(!$addCashback['result']){
            return $this->sendError($addCashback['message'], 422);
        }
        // End Add Cashback

        $userPayment    = UserPayment::create([
                            'user_id'       => $loggedUser->id,
                            'event_id'      => $event->id,
                            'grand_total'   => $event->price - ($event->price * $event->cashback),
                            'status'        => 'paid'
                        ]);

        $userCategory   = UserCategory::where([
                            'user_id'       => $loggedUser->id,
                            'category_id'   => $event->category_id
                        ])->first();

        return $this->sendResponse(new UserCategoryResource($userCategory), 'Event is successfully purchased');
    }
}
