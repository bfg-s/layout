<?php

namespace Bfg\Layout;

/**
 * Class MainLayout
 * @package Bfg\Layout
 */
abstract class MainLayout extends Tag {

    /**
     * UI Option switcher
     * @var bool
     */
    protected $ui = false;

    /**
     * @var string
     */
    protected $e = "html";

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Tag
     */
    protected $head;

    /**
     * @var array
     */
    protected $head_params = [];

    /**
     * @var array
     */
    protected $metas = [];

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @var array
     */
    protected $bscripts = [];

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * http://htmlbook.ru/html/base
     * @var array
     */
    protected $base = [];

    /**
     * @var Tag
     */
    protected $body;

    /**
     * @var array
     */
    protected $body_params = [];

    /**
     * @var array
     */
    protected $page_data = [];

    /**
     * @var string
     */
    protected $asset_driver = "asset";

    /**
     * The ID of container
     * @var string|null
     */
    protected $containerId = null;

    /**
     * Join HTML type
     * @var string
     */
    protected $lj = "<!DOCTYPE html>";

    /**
     * MainLayout constructor.
     */
    public function __construct()
    {
        parent::__construct(null, []);
        if ($this->containerId) { MetaConfigs::add('container', $this->containerId); }
        $this->attr('lang', \App::getLocale());
        $this->head = $this->head($this->head_params);
        $this->body = $this->body($this->body_params);
        $this->head->title($this->title ?? config('app.name'));

        foreach ($this->links as $link) { $this->head->link($link); }
        foreach ($this->styles as $style) { $this->set_style($style, $this->head); }
        if ($this->ui) { $this->set_style(asset("vendor/ui/ui.css"), $this->head); }
        foreach ($this->scripts as $script) { $this->set_script($script, $this->head); }
        foreach ($this->metas as $meta) { $this->head->meta($meta); }

        foreach (MetaConfigs::get() as $name => $content) {
            $this->head->meta(["http-equiv" => $name, "name" => $name, "content" => $content]);
        }
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->body->appEnd($content);

        if ($this->containerId) {

            $this->body->attr('id', $this->containerId);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function create_body_scripts()
    {
        $respond = app(Respond::class)->toArray();

        if (count($respond)) {

            $this->body->script(['data-bfg-call' => '', 'type' => 'application/json'])->appEnd(
                json_encode($respond, JSON_UNESCAPED_UNICODE)
            );
        }

        if ($this->ui) {

            $this->set_script(asset("vendor/ui/ui.js"), $this->body);
        }

        foreach ($this->bscripts as $script) {

            $this->set_script($script, $this->body);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_style($data, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->link();

        if (is_array($data)) {

            $tag->attr($data);
        }

        else if (is_string($data)) {

            $url = strpos($data, "://") === false ? call_user_func($this->asset_driver, $data) : $data;

            $tag->attr(['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css']);
        }
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_script($data, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->script();

        if (is_array($data)) {

            $tag->attr($data);
        }

        else if (is_string($data)) {

            $url = strpos($data, "://") === false ? call_user_func($this->asset_driver, $data) : $data;

            $tag->attr(['src' => $url, 'type' => 'text/javascript']);
        }
    }
}