<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppSetting\IndexRequest;
use App\Http\Requests\AppSetting\SetSettingRequest;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use App\Repositories\Contracts\AppSettingRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppSettingController extends Controller
{
    /**
     * @var AppSettingRepository
     */
    protected $appSettingRepository;
    /**
     * AppSettingController constructor.
     * @param AppSettingRepository $appSettingRepository
     */
    public function __construct(AppSettingRepository $appSettingRepository)
    {
        $this->appSettingRepository = $appSettingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
//        $this->authorize('list', [Branch::class, 'app_setting_list']);

        $appSettings = $this->appSettingRepository->findBy($request->all());

        return AppSettingResource::collection($appSettings);
    }

    /**
     * Display app setting of the resource.
     *
     * @param AppSetting $appSetting
     * @return AppSettingResource
     */
    public function show(AppSetting $appSetting): AppSettingResource
    {
//        $this->authorize('list', [Branch::class, 'branch_list']);

        return new AppSettingResource($appSetting);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SetSettingRequest $request
     * @return AppSettingResource
     */
    public function setSetting(SetSettingRequest $request)
    {
//        $this->authorize('list', [Branch::class, 'branch_list']);

        $appSettings = $this->appSettingRepository->setSettings($request->all());

        return new AppSettingResource($appSettings);
    }
}
