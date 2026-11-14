<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Tree;
use App\Http\Resources\TreeResource;
use App\Http\Requests\Api\V1\StoreTreeRequest;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;



class TreeController extends Controller
{

    public function index(Request $request)
    {
        //
        $tree = $request->user()
            ->trees()
            ->with('seed')
            ->latest()
            ->paginate(15);

        return TreeResource::collection($tree);
    }


    public function store(StoreTreeRequest $request)
    {


        $tree = $request->user()->trees()->create($request->validated());

        return (new TreeResource($tree->load('seed')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }


    public function show(Request $request, Tree $tree)
    {
        //
        $this->ensureUserOwnsTree($request, $tree);

        return new TreeResource($tree->load('seed'));
    }


    private function ensureUserOwnsTree(Request $request, Tree $tree): void
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);
    }


}
