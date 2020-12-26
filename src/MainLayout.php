<?php

namespace Bfg\Layout;

use Bfg\Layout\Core\Tag;

/**
 * Class Layout
 * @package Bfg\Layout
 */
abstract class MainLayout extends Tag {

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
     * Layout constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct(null, $params);
        $this->head = $this->head($this->head_params);
        $this->body = $this->body($this->body_params);
        $this->head->title($this->title ?? config('app.name'));
        $this->addTagsFrom('links', 'link', $this->head);
        $this->addTagsFrom('styles', 'link', $this->head);
        $this->addTagsFrom('scripts', 'script', $this->head);
        $this->addTagsFrom('metas', 'meta', $this->head);
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

        return $this;
    }

    /**
     * @param  string  $key
     * @param  null  $value
     * @return $this
     */
    public function addPageData(string $key, $value = null)
    {
        $this->page_data[$key] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function create_body_scripts()
    {
        $this->addTagsFrom('bscripts', 'script', $this->body);

        return $this;
    }

    /**
     * @return array
     */
    public function get_page_data()
    {
        return $this->page_data;
    }

    /**
     * @return $this
     */
    public function create_body_data()
    {
        if (count($this->page_data)) {

            $this->body->script(['id' => 'bfg-page-json', 'type' => 'json'])
                ->appEnd(json_encode($this->page_data));
        }

        return $this;
    }

    /**
     * @param  string  $from
     * @param $data
     * @param  string|null  $to
     * @param  object|null  $subject
     * @return $this
     */
    protected function addTagsFrom (string $from, string $to = null, object $subject = null) {
        if (!$to) $to = $from;
        if (!$subject) $subject = $this;
        foreach ($this->{$from} as $item) {
            $this->{"set_{$from}"}($item, $to, $subject);
        }
        return $this;
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_links($data, string $to, object $subject)
    {
        $subject->{$to}($data);
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_metas($data, string $to, object $subject)
    {
        $subject->{$to}($data);
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_styles($data, string $to, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->{$to}();

        if (is_array($data)) {

            $tag->attr($data);
        }

        else if (is_string($data)) {

            $url = strpos($data, "://") === false ? asset($data) : $data;

            $tag->attr(['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css']);
        }
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_scripts($data, string $to, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->{$to}();

        if (is_array($data)) {

            $tag->attr($data);
        }

        else if (is_string($data)) {

            $url = strpos($data, "://") === false ? asset($data) : $data;

            $tag->attr(['src' => $url, 'type' => 'text/javascript']);
        }
    }

    /**
     * @param $data
     * @param  string  $to
     * @param  object  $subject
     */
    protected function set_bscripts($data, string $to, object $subject)
    {
        $this->set_scripts($data, $to, $subject);
    }
}