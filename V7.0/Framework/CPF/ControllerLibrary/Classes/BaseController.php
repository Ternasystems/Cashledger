<?php

declare(strict_types=1);

namespace TS_Controller\Classes;


use TS_Configuration\Classes\AbstractCls;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Response;

/**
 * The lean, foundational base class for ALL controllers (both API and Application).
 * It provides universal helper methods for creating common response types.
 * This class has no dependencies.
 */
abstract class BaseController extends AbstractCls
{
    /**
     * Creates a response that redirects the user to a specific URL.
     */
    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * Creates a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): Response
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return new Response($content, $statusCode, ['Content-Type' => 'application/json']);
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
     *
     * Supports suffixes like _like, _gt, _lt, _gte, _lte, _neq.
     *
     * @param array|null $filter e.g., ['ContinentId' => 'uuid-1', 'Name_like' => 'Test%']
     * @return array|null e.g., [['ContinentId', '=', 'uuid-1'], ['Name', 'LIKE', 'Test%']]
     */
    protected function parseFilter(?array $filter): ?array
    {
        if (is_null($filter)) {
            return null;
        }

        $whereClause = [];
        $operatorMap = ['_like' => 'LIKE', '_gt' => '>', '_gte' => '>=', '_lt' => '<', '_lte' => '<=', '_neq' => '!='];

        foreach ($filter as $key => $value) {
            $column = $key;
            $operator = '='; // Default operator

            foreach ($operatorMap as $suffix => $sqlOperator) {
                if (str_ends_with($key, $suffix)) {
                    $column = substr($key, 0, -strlen($suffix));
                    $operator = $sqlOperator;
                    break;
                }
            }

            $whereClause[] = [$column, $operator, $value];
        }
        return $whereClause;
    }
}

