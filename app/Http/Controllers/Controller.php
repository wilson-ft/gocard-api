<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\LengthAwarePaginator;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($result, $message, $code = 200)
    {
        $request    = request();
        $input      = $request->all();

        $totalData      = 1;
        $currentPage    = 1;
        $pageLength     = 1;

        if(is_array($result) && @$result[0] !== null){
            $page = 1;
            if(@$input['page'] !== null && is_numeric(@$input['page'])){
                $page = $input['page'];
            }

            $perPage = 100;
            if(@$input['page_length'] !== null && is_numeric(@$input['page_length'])){
                $perPage = $input['page_length'];
            }

            $offset     = ($page * $perPage) - $perPage;
            $paginator  = new LengthAwarePaginator(
                array_values(array_slice($result, $offset, $perPage, true)), // Only grab the items we need
                count($result), // Total items
                $perPage, // Items per page
                $page
            );

            $result         = $paginator->items();
            $totalData      = $paginator->total();
            $currentPage    = $page;
            $pageLength     = $perPage;
        }

        $response = Response::json([
            'status'    => true,
            'message'   => $message,
            'data'      => $result
        ], $code)
        ->header('Total', $totalData)
        ->header('Page-No', $currentPage)
        ->header('Page-Length', $pageLength);

        return $response;
    }

    public function sendError($message, $code = 404, $data = [])
    {
        $res = [
            'status'    => false,
            'message'   => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return Response::json($res, $code);
    }
}
