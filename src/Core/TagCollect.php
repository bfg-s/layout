<?php

namespace Bfg\Layout\Core;

/**
 * TagCollect Class
 */
class TagCollect
{
    /**
     * @param array $params
     * @return $this
     */
    public function a(...$params) {
        return $this->add('a', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function abbr(...$params) {
        return $this->add('abbr', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function acronym(...$params) {
        return $this->add('acronym', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function address(...$params) {
        return $this->add('address', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function applet(...$params) {
        return $this->add('applet', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function area(...$params) {
        return $this->add('area', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function article(...$params) {
        return $this->add('article', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function aside(...$params) {
        return $this->add('aside', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function audio(...$params) {
        return $this->add('audio', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function b(...$params) {
        return $this->add('b', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function base(...$params) {
        return $this->add('base', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function basefont(...$params) {
        return $this->add('basefont', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bdi(...$params) {
        return $this->add('bdi', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function bdo(...$params) {
        return $this->add('bdo', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function big(...$params) {
        return $this->add('big', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function blockquote(...$params) {
        return $this->add('blockquote', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function body(...$params) {
        return $this->add('body', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function br(...$params) {
        return $this->add('br', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function button(...$params) {
        return $this->add('button', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function canvas(...$params) {
        return $this->add('canvas', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function caption(...$params) {
        return $this->add('caption', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function center(...$params) {
        return $this->add('center', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function cite(...$params) {
        return $this->add('cite', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function code(...$params) {
        return $this->add('code', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function col(...$params) {
        return $this->add('col', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function colgroup(...$params) {
        return $this->add('colgroup', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function data(...$params) {
        return $this->add('data', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function datalist(...$params) {
        return $this->add('datalist', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dd(...$params) {
        return $this->add('dd', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function del(...$params) {
        return $this->add('del', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function details(...$params) {
        return $this->add('details', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dfn(...$params) {
        return $this->add('dfn', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dialog(...$params) {
        return $this->add('dialog', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dir(...$params) {
        return $this->add('dir', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function div(...$params) {
        return $this->add('div', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dl(...$params) {
        return $this->add('dl', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function dt(...$params) {
        return $this->add('dt', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function em(...$params) {
        return $this->add('em', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function embed(...$params) {
        return $this->add('embed', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function fieldset(...$params) {
        return $this->add('fieldset', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function figcaption(...$params) {
        return $this->add('figcaption', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function figure(...$params) {
        return $this->add('figure', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function font(...$params) {
        return $this->add('font', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function footer(...$params) {
        return $this->add('footer', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function form(...$params) {
        return $this->add('form', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function frame(...$params) {
        return $this->add('frame', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function frameset(...$params) {
        return $this->add('frameset', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h1(...$params) {
        return $this->add('h1', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h2(...$params) {
        return $this->add('h2', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h3(...$params) {
        return $this->add('h3', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h4(...$params) {
        return $this->add('h4', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h5(...$params) {
        return $this->add('h5', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function h6(...$params) {
        return $this->add('h6', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function head(...$params) {
        return $this->add('head', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function header(...$params) {
        return $this->add('header', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function hr(...$params) {
        return $this->add('hr', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function html(...$params) {
        return $this->add('html', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function i(...$params) {
        return $this->add('i', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function iframe(...$params) {
        return $this->add('iframe', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function img(...$params) {
        return $this->add('img', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function input(...$params) {
        return $this->add('input', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function ins(...$params) {
        return $this->add('ins', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function kbd(...$params) {
        return $this->add('kbd', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function label(...$params) {
        return $this->add('label', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function legend(...$params) {
        return $this->add('legend', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function li(...$params) {
        return $this->add('li', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function link(...$params) {
        return $this->add('link', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function main(...$params) {
        return $this->add('main', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function map(...$params) {
        return $this->add('map', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function mark(...$params) {
        return $this->add('mark', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function meta(...$params) {
        return $this->add('meta', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function meter(...$params) {
        return $this->add('meter', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function nav(...$params) {
        return $this->add('nav', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function noframes(...$params) {
        return $this->add('noframes', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function noscript(...$params) {
        return $this->add('noscript', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function object(...$params) {
        return $this->add('object', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function ol(...$params) {
        return $this->add('ol', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function optgroup(...$params) {
        return $this->add('optgroup', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function option(...$params) {
        return $this->add('option', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function output(...$params) {
        return $this->add('output', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function p(...$params) {
        return $this->add('p', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function param(...$params) {
        return $this->add('param', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function picture(...$params) {
        return $this->add('picture', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function pre(...$params) {
        return $this->add('pre', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function progress(...$params) {
        return $this->add('progress', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function q(...$params) {
        return $this->add('q', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function rp(...$params) {
        return $this->add('rp', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function rt(...$params) {
        return $this->add('rt', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function ruby(...$params) {
        return $this->add('ruby', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function s(...$params) {
        return $this->add('s', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function samp(...$params) {
        return $this->add('samp', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function script(...$params) {
        return $this->add('script', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function section(...$params) {
        return $this->add('section', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function select(...$params) {
        return $this->add('select', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function small(...$params) {
        return $this->add('small', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function source(...$params) {
        return $this->add('source', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function span(...$params) {
        return $this->add('span', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function strike(...$params) {
        return $this->add('strike', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function strong(...$params) {
        return $this->add('strong', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function style(...$params) {
        return $this->add('style', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function sub(...$params) {
        return $this->add('sub', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function summary(...$params) {
        return $this->add('summary', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function sup(...$params) {
        return $this->add('sup', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function svg(...$params) {
        return $this->add('svg', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function table(...$params) {
        return $this->add('table', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function tbody(...$params) {
        return $this->add('tbody', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function td(...$params) {
        return $this->add('td', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function template(...$params) {
        return $this->add('template', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function textarea(...$params) {
        return $this->add('textarea', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function tfoot(...$params) {
        return $this->add('tfoot', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function th(...$params) {
        return $this->add('th', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function thead(...$params) {
        return $this->add('thead', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function time(...$params) {
        return $this->add('time', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function title(...$params) {
        return $this->add('title', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function tr(...$params) {
        return $this->add('tr', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function track(...$params) {
        return $this->add('track', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function tt(...$params) {
        return $this->add('tt', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function u(...$params) {
        return $this->add('u', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function ul(...$params) {
        return $this->add('ul', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function var(...$params) {
        return $this->add('var', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function video(...$params) {
        return $this->add('video', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function wbr(...$params) {
        return $this->add('wbr', $params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function use(...$params) {
        return $this->add('use', $params);
    }

}
