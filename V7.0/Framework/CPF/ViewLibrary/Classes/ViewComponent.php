<?php

declare(strict_types=1);

namespace TS_View\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * The abstract base class for all View Components.
 * A View Component is a self-contained class responsible for rendering a specific piece of UI.
 * It encapsulates the logic needed to fetch data for and render a partial view.
 */
abstract class ViewComponent extends AbstractCls
{
    /**
     * The main method of the component. It's responsible for preparing data
     * and returning the View instance that should be rendered.
     *
     * This method will be called by the ComponentService, which can inject
     * any required dependencies into it automatically.
     *
     * @return View The configured View instance pointing to the component's template.
     */
    abstract public function render(): View;
}