<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use App\Models\Permission;

/**
 * Class PermissionController
 *
 * @package App\Http\Controller\Api
 */
class PermissionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PermissionResource::collection(Permission::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        //
    }
}
