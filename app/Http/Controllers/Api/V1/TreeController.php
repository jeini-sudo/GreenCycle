<?php

//no recuerdo los imports, los pondré más adelante
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Tree;
use App\Http\Resources\TreeResource;
use App\Http\Requests\StoreTreeRequest;
use App\Http\Requests\UpdateTreeRequest;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;



class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreeRequest $request)
    {
        //
        
        $tree = $request->user()->trees()->create($request->validated());

        return (new TreeResource($tree->load('seed')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tree $tree)
    {
        //
        $this->ensureUserOwnsTree( $request,  $tree);

        return new TreeResource($tree->load('seed'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreeRequest $request, Tree $tree)
    {
        //
        $this->ensureUserOwnsTree($request, $tree);

        $tree->update($request->validated());

        return new TreeResource($tree->load('seed'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tree $tree)
    {
        //
        $this->ensureUserOwnsTree($request, $tree);

        $tree->delete();

        return response()->json([
            'message' => 'El arbol se fue.',
        ]);
    }

    private function ensureUserOwnsTree(Request $request, Tree $tree): void
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);
    }


}
