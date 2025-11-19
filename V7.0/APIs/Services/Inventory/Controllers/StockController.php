<?php

namespace API_Inventory_Controller;

use API_Administration_Controller\AbstractController;
use API_Inventory_Facade\StockFacade;
use Exception;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * The concrete StockController.
 * It extends the AbstractController and is now extremely simple.
 */
class StockController extends AbstractController
{
    protected StockFacade $stockFacade;
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Stock';

    /**
     * We inject our specific StockFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(StockFacade $facade)
    {
        parent::__construct($facade);
        $this->stockFacade = $facade;
    }

    /**
     * Specifically updates the quantity of a stock item.
     * Expects a POST request with a JSON body like {"quantity": 150.5}
     * e.g., /?controller=Stock&action=updateQuantity&id=...
     */
    public function updateQuantity(Request $request): Response
    {
        $resourceType = $this->getResourceType($request);
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);
        $quantity = $data['quantity'] ?? null;

        if (!$id || !isset($quantity) || !is_numeric($quantity)) {
            return $this->json(['error' => 'ID and a numeric "quantity" in the JSON body are required.'], 400);
        }

        try {
            $item = $this->stockFacade->putQuantity($resourceType, $id, $quantity);
            if (!$item) {
                return $this->json(['error' => 'Stock item not found.'], 404);
            }
            return $this->json($item);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update stock quantity.', 'message' => $e->getMessage()], 500);
        }
    }
}