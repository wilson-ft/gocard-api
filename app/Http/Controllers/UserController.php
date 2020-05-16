<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\User;

use App\Http\Requests\Users\StoreUser;
use App\Http\Requests\Users\VerifyUser;
use App\Http\Requests\Users\TransferBalance;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Users\AuthResource;
use App\Http\Resources\Users\UserWithCategoriesResource;

use App\Engines\MambuEngine;
use App\Engines\AwsEngine;

use Carbon\Carbon;
use DB;

class UserController extends Controller
{
    public $mambuEngine;
    public $awsEngine;

    public function __construct(MambuEngine $mambuEngine, AwsEngine $awsEngine)
    {
        $this->mambuEngine  = $mambuEngine;
        $this->awsEngine    = $awsEngine;
    }

    public function store(StoreUser $request)
    {
        DB::beginTransaction();

        $input = $request->all();

        // Store Photo to S3
        try {
            $path = Storage::putFile('', $request->file('photo'), 'public');
        } catch (\Throwable $th) {
            return $this->sendError("Issue on uploading photo", 422);
        }
        // End Store Photo to S3

        // Create Mambu Client
        $createClient = $this->mambuEngine->createClient($input['first_name'], $input['last_name']);

        if(!$createClient['result']){
            return $this->sendError("Issue on creating Mambu client", 422);
        }

        $mambuClient = $createClient['data']->client;
        // End Create Mambu Client

        // Create Mambu Account
        $createAccount  = $this->mambuEngine->createClientAccount($mambuClient->encodedKey);

        if(!$createAccount['result']){
            return $this->sendError("Issue on creating Mambu client", 422);
        }

        $mambuAccount = $createAccount['data']->savingsAccount;
        // End Create Mambu Account

        $user = User::create([
            'first_name'        => $input['first_name'],
            'last_name'         => $input['last_name'],
            'phone_no'          => $input['phone_no'],
            'photo'             => $path,
            'ext_account_id'    => $mambuAccount->encodedKey
        ]);

        if(!$user){
            return $this->sendError("Issue on creating user", 422);
        }

        DB::commit();

        return $this->sendResponse(new UserResource($user), 'User is successfully created');
    }

    public function verify(VerifyUser $request)
    {
        $input  = $request->all();

        // Store Photo to S3
        try {
            $target = Storage::putFile('', $request->file('photo'), 'public');
        } catch (\Throwable $th) {
            return $this->sendError("Issue on uploading photo", 422);
        }
        // End Store Photo to S3

        $user   = User::where('phone_no', $input['phone_no'])
                ->first();

        $comparePhoto = $this->awsEngine->comparePhotos($user->photo, $target);

        if(!$comparePhoto){
            return $this->sendError("User is not verified", 422);
        }

        $user->api_token = Str::random(60);
        $user->save();

        Storage::disk('s3')->delete($target);

        return $this->sendResponse(new AuthResource($user), 'User is verified');
    }

    public function show(Request $request)
    {
        $loggedUser = Auth::user();

        return $this->sendResponse(new UserResource($loggedUser), 'User is successfully retrieved');
    }

    public function transfer(TransferBalance $request)
    {
        $input      = $request->all();

        $loggedUser = Auth::user();

        $transferBalance = $this->mambuEngine->transferBalance($loggedUser->ext_account_id, $input['amount'], $input['type']);

        if(!$transferBalance['result']){
            return $this->sendError($transferBalance['message'], 422);
        }

        return $this->sendResponse(new UserResource($loggedUser), 'Transfer is successfully done');
    }

    public function showCategories()
    {
        $loggedUser = Auth::user();

        return $this->sendResponse(new UserWithCategoriesResource($loggedUser), 'User is successfully retrieved');
    }
}
