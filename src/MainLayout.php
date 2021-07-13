<?php

namespace Bfg\Layout\Core;

use Bfg\Layout\MetaConfigs;

/**
 * Class Layout
 * @package Bfg\Layout\Core
 */
abstract class MainLayout extends Tag
{

    /**
     * @var string
     */
    protected $e = "html";

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Tag|null
     */
    protected ?Tag $head = null;

    /**
     * @var array
     */
    protected array $head_params = [];

    /**
     * @var array
     */
    protected array $metas = [];

    /**
     * @var array
     */
    protected array $links = [];

    /**
     * @var array
     */
    protected array $scripts = [];

    /**
     * @var array
     */
    protected array $bscripts = [];

    /**
     * @var array
     */
    protected array $styles = [];

    /**
     * http://htmlbook.ru/html/base
     * @var array
     */
    protected array $base = [];

    /**
     * @var Tag|null
     */
    protected ?Tag $body = null;

    /**
     * @var array
     */
    protected array $body_params = [];

    /**
     * @var array
     */
    protected array $page_data = [];

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

        $this->attr('lang', \App::getLocale());
        $this->head = $this->head($this->head_params);
        $this->body = $this->body($this->body_params);
        $this->head->title($this->title ?? config('app.name'));

        $this->makeLinks()
            ->makeStyles()
            ->makeScripts()
            ->makeMetas();


        foreach (MetaConfigs::get() as $name => $content) {
            $this->head->meta(["http-equiv" => $name, "name" => $name, "content" => $content]);
        }
    }

    /**
     * @return $this
     */
    protected function makeLinks(): static
    {
        foreach ($this->links as $link) {
            $this->head->link($link);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function makeStyles(): static
    {
        foreach ($this->styles as $style) {
            $this->set_style($style, $this->head);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function makeScripts(): static
    {
        foreach ($this->scripts as $script) {
            $this->set_script($script, $this->head);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function makeMetas(): static
    {
        foreach ($this->metas as $meta) {
            $this->head->meta($meta);
        }

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content): static
    {
        $this->body->appEnd($content);

        return $this;
    }

    /**
     * @return $this
     */
    public function create_body_scripts(): static
    {
        $respond = app(Respond::class)->toArray();

        if (count($respond)) {
            $this->body->script(['data-bfg-call' => '', 'type' => 'application/json'])->appEnd(
                json_encode($respond, JSON_UNESCAPED_UNICODE)
            );
        }

        foreach ($this->bscripts as $script) {
            $this->set_script($script, $this->body);
        }

        return $this;
    }

    /**
     * @param $data
     * @param  object  $subject
     */
    protected function set_styles($data, string $to, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->{$to}();

        if (is_array($data)) {
            if (isset($data['href']) && !str_contains($data['href'], "://")) {
                $data['href'] = asset($data['href']);
            }
            if (!isset($data['rel'])) {
                $data['rel'] = 'stylesheet';
            }
            if (!isset($data['type'])) {
                $data['type'] = 'text/css';
            }
            $tag->attr($data);
        } else {
            if (is_string($data)) {
                $url = !str_contains($data, "://") ? asset($data) : $data;

                $tag->attr(['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css']);
            }
        }
    }

    /**
     * @param $data
     * @param  object  $subject
     */
    protected function set_scripts($data, string $to, object $subject)
    {
        /** @var Tag $tag */
        $tag = $subject->{$to}();

        if (is_array($data)) {
            if (isset($data['src']) && !str_contains($data['src'], "://")) {
                $data['src'] = asset($data['src']);
            }
            if (!isset($data['type'])) {
                $data['type'] = 'text/javascript';
            }
            $tag->attr($data);
        } else {
            if (is_string($data)) {
                $url = !str_contains($data, "://") ? asset($data) : $data;

                $tag->attr(['src' => $url, 'type' => 'text/javascript']);
            }
        }
    }
}
