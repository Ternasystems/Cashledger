<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\ICivilityService;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CivilityController extends BaseController
{
    private ICivilityService $service;

    public function __construct(ICivilityService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of all civility titles (e.g., Mr., Mrs.).
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $result = $this->service->getCivilities(null, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of all genders.
     */
    public function genders(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $result = $this->service->getGenders(null, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of all occupations.
     */
    public function occupations(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 50);
        $result = $this->service->getOccupations(null, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of all professional titles (e.g., Dr., Prof.).
     */
    public function titles(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 50);
        $result = $this->service->getTitles(null, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of all user statuses (e.g., Active, Pending).
     */
    public function statuses(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 50);
        $result = $this->service->getStatuses(null, $page, $pageSize);
        return $this->json($result);
    }
}