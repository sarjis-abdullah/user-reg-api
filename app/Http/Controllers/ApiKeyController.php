<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

class ApiKeyController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('list', Admin::class);

        $apiKeys = ApiKey::all();

        return response()->json(['data' => $apiKeys]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('store', [Admin::class, '']);

        $this->validate($request, [
           'name' => 'required|unique:api_keys,name|string|min:2'
        ]);

        $appName = $request->input('name');

        Artisan::call("apikey:generate $appName");

        $apiKey = ApiKey::latest()->first();

        return response()->json(['data' => $apiKey]);
    }

    /**
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $this->authorize('show', Admin::class);

        $apiKey = ApiKey::find($id);

        return response()->json(['data' => $apiKey]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ApiKey $apiKey
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, ApiKey $apiKey): JsonResponse
    {
        $this->authorize('update', Admin::class);

        $id = $request->segment(4);

        $this->validate($request, [
            'name' => 'string|min:2|unique:api_keys,name,' . $id . ',id',
            'active' => 'boolean'
        ]);

        $data = $request->all();

        ApiKey::where('id', $apiKey->id)->update($data);

        $apiKey = ApiKey::find($apiKey->id);

        return response()->json(['data' => $apiKey]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApiKey $apiKey
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ApiKey $apiKey): JsonResponse
    {
        $this->authorize('destroy', Admin::class);

        $apiKey->delete();

        return response()->json(null, 204);
    }
}
