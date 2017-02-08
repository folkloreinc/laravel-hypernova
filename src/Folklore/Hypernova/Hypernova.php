<?php

namespace Folklore\Hypernova;

class Hypernova
{
    protected $app;

    protected $renderer;

    protected $jobs = [];

    public function __construct($app, $renderer)
    {
        $this->app = $app;
        $this->renderer = $renderer;
    }

    public function addJob($component, $data = [])
    {
        $json = json_encode($data);
        $uuid = Uuid::uuid1()->toString();
        $job = [
            'name' => $component,
            'data' => $data
        ];
        $this->jobs[$uuid] = $job;

        $this->renderer->addJob($uuid, $job);

        $attributes = 'data-hypernova-key="'.$component.'" data-hypernova-id="'.$uuid.'"';
        return (
            $this->getStartComment($uuid).
            '<div '.$attributes.'></div>'.
            '<script type="application/json" '.$attributes.'><!--'.$json.'--></script>'.
            $this->getEndComment($uuid)
        );
    }

    public function render($view)
    {
        return $view->render(function ($view, $contents) {
            return $this->replaceContents($contents);
        });
    }

    public function modifyResponse($response)
    {
        $content = $response->getContent();
        $content = $this->replaceContents($content);
        $response->setContent($content);
        return $response;
    }

    protected function getStartComment($uuid)
    {
        return '<!-- START hypernova['.$uuid.'] -->';
    }

    protected function getEndComment($uuid)
    {
        return '<!-- END hypernova['.$uuid.'] -->';
    }

    protected function renderJobs()
    {
        return $this->renderer->render();
    }

    protected function replaceContents($contents)
    {
        $jobsHtml = $this->renderJobs();
        foreach ($jobsHtml as $uuid => $html) {
            $start = preg_quote($this->getStartComment($uuid), '/');
            $end = preg_quote($this->getEndComment($uuid), '/');
            $contents = preg_replace('/'.$start.'(.*?)'.$end.'/', $html, $contents);
        }
        return $contents;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->hypernova, $method], $arguments);
    }
}
