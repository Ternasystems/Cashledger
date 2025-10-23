<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IParameterService;
use API_Assets\Classes\EntityException;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class ParameterController extends BaseController
{
    protected IParameterService $service;

    public function __construct(IParameterService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets the value of a single parameter.
     * Responds to: /index.php?controller=Parameter&action=show&name=SITE_NAME
     * @throws EntityException
     */
    public function show(Request $request): Response
    {
        $name = $request->getQuery('name');
        if (!$name) {
            return $this->json(['error' => 'Parameter name is required.'], 400);
        }

        $parameter = $this->service->GetParameter($name);

        // Return a simple key-value pair.
        return $this->json([$name => $parameter?->it()['ParamValue'] ?? $parameter?->it()['ParamUValue']]);
    }

    /**
     * Updates one or more parameters from POST data.
     * Expects POST data like: ['SITE_NAME' => 'New Site Name', 'MAINTENANCE_MODE' => '1']
     */
    public function update(Request $request): Response
    {
        // Get all POST data.
        $parameters = $request->getPost();

        if (empty($parameters)) {
            return $this->json(['error' => 'No parameters provided for update.'], 400);
        }

        try {
            foreach ($parameters as $key => $parameter)
                $this->service->SetParameter($key, $parameter['value'], $parameter['encrypted']);

            return $this->json(['success' => true, 'message' => 'Parameters updated.']);
        } catch (Exception $e) {
            // In a real app, log the exception message.
            return $this->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }
}