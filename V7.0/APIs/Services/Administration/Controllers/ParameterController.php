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
    public function __construct(protected IParameterService $service){}

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

        $parameter = $this->service->getParameter($name);

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
                $this->service->setParameter($key, $parameter['value'], $parameter['encrypted']);

            return $this->json(['success' => true, 'message' => 'Parameters updated.']);
        } catch (Exception $e) {
            // In a real app, log the exception message.
            return $this->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Gets a calculated value from the database via the parameter repository.
     * Responds to: /index.php?controller=Parameter&action=getFrom&predicate=f_GetVersion&args[]=...
     */
    public function getFrom(Request $request): Response
    {
        try {
            $predicate = $request->getQuery('predicate');
            if (!$predicate) {
                return $this->json(['error' => 'Predicate parameter is required.'], 400);
            }

            // 'args' in a query string will be parsed as an array
            // e.g., ?args[]=val1&args[]=val2
            $args = $request->getQuery('args');

            $result = $this->service->getFrom($predicate, $args);

            return $this->json(['predicate' => $predicate, 'result' => $result]);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to execute predicate.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Checks a boolean condition from the database via the parameter repository.
     * Responds to: /index.php?controller=Parameter&action=check&predicate=f_CheckMaintenanceMode
     */
    public function check(Request $request): Response
    {
        try {
            $predicate = $request->getQuery('predicate');
            if (!$predicate) {
                return $this->json(['error' => 'Predicate parameter is required.'], 400);
            }

            $args = $request->getQuery('args');

            $result = $this->service->checkParameter($predicate, $args);

            return $this->json(['predicate' => $predicate, 'result' => $result]);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to execute predicate.', 'message' => $e->getMessage()], 500);
        }
    }
}