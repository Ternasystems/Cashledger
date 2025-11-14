<?php

declare(strict_types=1);

namespace TS_View\Classes;

use BadMethodCallException;
use ReflectionException;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DIException;
use TS_Exception\Classes\ViewException;
use TS_Locale\Classes\Translator;

/**
 * A View renderer that supports layouts, sections, and shared data.
 */
class View extends AbstractCls
{
    private string $viewPath;
    private array $sharedData = [];
    private ?string $layout = null;
    private ?ComponentService $componentService = null;
    private ?Escaper $escaper = null;
    private ?HelpersRegistry $helpers = null;
    private ?Translator $translator = null;

    /** @var array<string, string> Holds content for named sections. */
    private array $sections = [];
    private ?string $currentSection = null;

    // Properties to hold the state passed from a component
    public string $templatePath = '' {
        get {
            return $this->templatePath;
        }
    }
    public array $data = [] {
        get {
            return $this->data;
        }
    }

    public function __construct(string $baseViewPath)
    {
        $this->viewPath = rtrim($baseViewPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Injects the Helpers registry.
     */
    public function setHelpersRegistry(HelpersRegistry $registry): void
    {
        $this->helpers = $registry;
    }

    /**
     * Injects the Escaper service.
     */
    public function setEscaper(Escaper $escaper): void
    {
        $this->escaper = $escaper;
    }

    /**
     * Injects the Translator service.
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Injects the component service, making the component() method available.
     */
    public function setComponentService(ComponentService $service): void
    {
        $this->componentService = $service;
    }

    // --- New methods for component interaction ---

    /**
     * A factory method used by components to specify their template and data.
     */
    public function create(string $templatePath, array $data = []): self
    {
        $this->templatePath = $templatePath;
        $this->data = $data;
        return $this;
    }
    // --- End new methods ---

    public function share(string $key, mixed $value): void
    {
        $this->sharedData[$key] = $value;
    }

    public function layout(string $layoutPath): self
    {
        $this->layout = $layoutPath;
        return $this;
    }

    /**
     * @throws ViewException
     */
    public function render(string $templatePath, array $data = []): string
    {
        $content = $this->renderPartial($templatePath, $data);

        if ($this->layout !== null) {
            $this->sections['content'] = $content;
            $content = $this->renderPartial($this->layout);
            $this->layout = null;
            $this->sections = [];
        }

        return $content;
    }

    /**
     * @throws ViewException
     */
    public function renderPartial(string $templatePath, array $data = []): string
    {
        $fullPath = $this->viewPath . DIRECTORY_SEPARATOR . str_replace('.', '/', $templatePath) . '.php';

        if (!file_exists($fullPath)) {
            throw new ViewException('view_not_found', [':path' => $fullPath]);
        }

        $finalData = array_merge($this->sharedData, $data);
        extract($finalData);

        ob_start();
        require $fullPath;
        return ob_get_clean() ?: '';
    }

    /**
     * A public helper to access the Translator.
     * This allows $this->t() to work in views.
     */
    public function t(string $key, array $placeholders = []): string
    {
        if ($this->translator === null) {
            return $key;
        }
        return $this->translator->trans($key, $placeholders);
    }

    /**
     * A public helper to access the Escaper.
     * This allows $this->h() to work in views.
     */
    public function h(?string $string): string
    {
        if ($this->escaper === null) {
            return (string)$string;
        }
        return $this->escaper->html($string);
    }

    /**
     * @throws ViewException
     * @throws ReflectionException|DIException
     */
    public function component(string $name, array $args = []): string
    {
        if ($this->componentService === null) {
            throw new ViewException('component_service_not_set');
        }
        return $this->componentService->render($name, $args);
    }

    /**
     * @throws ViewException
     */
    public function startSection(string $name): void
    {
        if ($this->currentSection !== null) {
            throw new ViewException('nested_section_error');
        }
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * @throws ViewException
     */
    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new ViewException('end_section_without_start');
        }
        $this->sections[$this->currentSection] = ob_get_clean() ?: '';
        $this->currentSection = null;
    }

    public function section(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Magic method to proxy calls to the Escaper service and the Helpers registry.
     * @throws ViewException
     */
    public function __call(string $name, array $arguments): mixed
    {
        // Prioritize helpers
        if ($this->helpers !== null && $this->helpers->has($name)) {
            return $this->helpers->call($name, $arguments);
        }

        // Fall back to escaper
        if ($this->escaper !== null && method_exists($this->escaper, $name)) {
            return $this->escaper->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist on View, Helpers, or Escaper.");
    }
}