<?php

namespace Bfg\Layout;

use Bfg\Doc\Attributes\DocClassName;
use Bfg\Doc\Attributes\DocMethods;
use Livewire\Component;
use Livewire\LifecycleManager;

/**
 * Class Scene
 * @package Bfg\Layout
 */
abstract class Scene extends Component
{
    /**
     * Template of component
     * @var string|null
     */
    protected ?string $template = null;

    /**
     * Child components for slot data
     *
     * @var array
     */
    protected array $child = [];

    /**
     * Inside child components for extending
     *
     * @var array
     */
    #[DocMethods([
        '{value}', 'static', 'mixin'
    ], 's_{key}', 'The component {key}'), DocClassName('{namespace}\{class}Components')]
    public static array $components = [];

    /**
     * Data for send to template
     * @return array
     */
    protected function viewData(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string
     */
    public function render(
    ): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|string|\Illuminate\Contracts\Foundation\Application
    {
        $child = $this->renderChild();

        if (!$this->template) {
            return $this->defaultTemplate($child);
        }

        return view(
            $this->makeNameTemplate($this->template),
            array_merge($this->viewData(), [
                'slot' => $child
            ])
        );
    }

    /**
     * @param  string  $child
     * @return string
     */
    protected function defaultTemplate(string $child): string
    {
        return "<div>{$child}</div>";
    }

    /**
     * @param  string|null  $template
     * @return string|null
     */
    protected function makeNameTemplate(?string $template): ?string
    {
        return $template;
    }

    /**
     * @return string
     */
    protected function renderChild(): string
    {
        $view = "";

        foreach ($this->child as $child) {
            $child = is_string($child) ? app($child) : $child;
            $manager = LifecycleManager::fromInitialInstance($child)
                ->initialHydrate()
                ->mount([])
                ->renderToView();

            $view .= $manager->initialDehydrate()->toInitialResponse()->effects['html'];
        }

        return $view;
    }

    /**
     * @param $method
     * @param $params
     * @return $this|Scene|static|mixed|void
     * @throws \Exception
     */
    public function __call($method, $params)
    {
        if (property_exists($this, $method)) {
            if (isset($params[1])) {
                $this->{$method} = $params;
            } else {
                if (isset($params[0])) {
                    $this->{$method} = $params[0];
                }
            }
        } else {
            if (preg_match('/^s_(.*)/', $method, $m)) {
                $name = $m[1];
                if (isset(static::$components[$name])) {
                    /** @var Scene|static $child */
                    $child = app(static::$components[$name]);
                    $this->child[] = $child;
                    return $child;
                }

                throw new \Exception("Component [{$name}] not found!");
            }
        }

        return parent::__call($method, $params);
    }
}
