<?php

//no recuerdo los imports, los pondré más adelante
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $trees = $request->user()
            ->trees()
            ->with('category')
            ->latest()
            ->paginate(15);

        return TreeResource::collection($trees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreeRequest $request)
    {
        //
        $trees = $request->user()->trees()->create($request->validated());

        return (new TreeResource($trees->load('level')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $this->ensureUserOwnsHabit($request, $trees);

        return new TreeResource($trees->load('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTreeRequest $request, Tree $trees)
    {
        //
        $this->ensureUserOwnsTree($request, $trees);

        $trees->update($request->validated());

        return new TreeResource($trees->load('level'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tree $trees)
    {
        //
        $this->ensureUserOwnsTree($request, $trees);

        $trees->delete();

        return response()->json([
            'message' => 'Tree deleted successfully.',
        ]);
    }

    private function ensureUserOwnsHabit(Request $request, Tree $trees): void
    {
        abort_unless($trees->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);
    }


}
