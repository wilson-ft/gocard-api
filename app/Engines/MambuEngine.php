<?php

namespace App\Engines;

use Illuminate\Support\Str;

use DB;
use Log;

class MambuEngine
{
    private $branchKey;

    public function __construct()
    {
        $this->branchKey = env('MAMBU_BRANCH_KEY', null);
    }

    public function createClient($firstName, $lastName)
    {
        if($this->branchKey == null){
            return [
                "result"    => false,
                "message"   => "MAMBU BRANCH KEY is missing"
            ];
        }

        $createClient   = callMambu([
                            'method'    => 'POST',
                            'url'       => '/api/clients',
                            'json'      => [
                                'client' => [
                                    "firstName"         => $firstName,
                                    "lastName"          => $lastName,
                                    "preferredLanguage" => "ENGLISH",
                                    "notes"             => "",
                                    "assignedBranchKey" => $this->branchKey
                                ],
                                "idDocuments" => [
                                    [
                                        "identificationDocumentTemplateKey" => "8a8e867271bd280c0171bf7e4ec71b01",
                                        "issuingAuthority"                  => "Immigration Authority of Singapore",
                                        "documentType"                      => "NRIC/Passport Number",
                                        "validUntil"                        => "2021-09-12",
                                        "documentId"                        => "S9812345A"
                                    ]
                                ]
                            ]
                        ]);

        if($createClient['status_code'] != 201){
            return [
                "result"    => false,
                "message"   => 'Registering Mambu client is failed. Errors : ' . json_encode(@$createClient['body']->message)
            ];
        }

        return [
            "result"    => true,
            "message"   => "Mambu client is successfully created",
            "data"      => $createClient['body']
        ];
    }

    public function createClientAccount($clientId)
    {
        $createAccount  = callMambu([
                            'method'    => 'POST',
                            'url'       => '/api/savings',
                            'json'      => [
                                "savingsAccount"=> [
                                    "name"              => "Digital Account",
                                    "accountHolderType" => "CLIENT",
                                    "accountHolderKey"  => $clientId,
                                    "accountState"      => "APPROVED",
                                    "productTypeKey"    => "8a8e878471bf59cf0171bf6979700440",
                                    "accountType"       => "CURRENT_ACCOUNT",
                                    "currencyCode"      => "SGD",
                                    "allowOverdraft"    => "true",
                                    "overdraftLimit"    => "100",
                                    "overdraftInterestSettings"=> [
                                        "interestRate"=> 5
                                    ],
                                    "interestSettings"=> [
                                        "interestRate"=> "1.25"
                                    ]
                                ]
                            ]
                        ]);

        if($createAccount['status_code'] != 201){
            return [
                "result"    => false,
                "message"   => 'Registering Mambu account is failed. Errors : ' . json_encode(@$createAccount['body']->message)
            ];
        }

        return [
            "result"    => true,
            "message"   => "Mambu account is successfully created",
            "data"      => $createAccount['body']
        ];
    }

    public function getAccountSaving($accountId)
    {
        $accountSaving  = callMambu([
                            'method'    => 'GET',
                            'url'       => '/api/savings/' . $accountId
                        ]);

        if($accountSaving['status_code'] != 200){
            return [
                "result"    => false,
                "message"   => 'Get account is failed. Errors : ' . json_encode(@$accountSaving['body']->message)
            ];
        }

        return [
            "result"    => true,
            "message"   => "Mambu account is successfully retrieved",
            "data"      => $accountSaving['body']
        ];
    }

    public function transferBalance($accountId, $amount, $type)
    {
        switch ($type) {
            case 'deposit':
                $postData = [
                    "amount"            => (string)$amount,
                    "notes"             => "Deposit into savings account",
                    "type"              => "DEPOSIT",
                    "method"            => "bank",
                    "customInformation" => [
                        [
                            "value"         => Str::random(60),
                            "customFieldID" => "IDENTIFIER_TRANSACTION_CHANNEL_I"
                        ]
                    ]
                ];
                break;

            case 'transfer':
                $postData = [
                    "amount"            => (string)$amount,
                    "notes"             => "Deposit into savings account",
                    "type"              => "TRANSFER",
                    "method"            => "bank",
                    "toSavingsAccount"  => env("MAMBU_MAIN_SAVING_ACCOUNTS_ID")
                ];
                break;

            default:
                return [
                    "result"    => false,
                    "message"   => 'Unknown transfer type'
                ];

                break;
        }

        $transferBalance    = callMambu([
                                'method'    => 'POST',
                                'url'       => '/api/savings/' . $accountId . '/transactions',
                                'json'      => $postData
                            ]);

        if($transferBalance['status_code'] != 201){
            return [
                "result"    => false,
                "message"   => 'Issue on transfer balance. Errors : ' . json_encode(@$transferBalance['body']->message)
            ];
        }

        return [
            "result"    => true,
            "message"   => "Transfer balance is successfull",
            "data"      => $transferBalance['body']
        ];
    }
}
