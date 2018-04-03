<?php

namespace Folklore\Hypernova;

use Illuminate\Container\Container;
use Ramsey\Uuid\Uuid;
use Folklore\Hypernova\Contracts\Renderer as RendererContract;
use Illuminate\Contracts\Support\Renderable;

class Hypernova
{
    protected $container;

    protected $jobs = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function pushJob($component, $data = [])
    {
        $uuid = $this->addJob($component, $data);
        return $this->renderPlaceholder($uuid);
    }

    public function addJob($component, $data = [])
    {
        $uuid = Uuid::uuid1()->toString();
        $job = is_array($component) ? $component:[
            'name' => $component,
            'data' => $data
        ];
        $this->jobs[$uuid] = $job;

        return $uuid;
    }

    public function getJob($uuid)
    {
        return array_get($this->jobs, $uuid);
    }

    public function getJobs()
    {
        return $this->jobs;
    }

    public function setJobs($jobs)
    {
        $this->jobs = $jobs;
        return $this;
    }

    public function clearJobs()
    {
        return $this->setJobs([]);
    }

    public function renderPlaceholder($uuid, $job = null)
    {
        $job = !$job ? $this->getJob($uuid):$job;
        if (!$job) {
            return '';
        }

        $component = $job['name'];
        $data = $job['data'];
        $json = json_encode($data);

        $attributes = 'data-hypernova-key="'.$component.'" data-hypernova-id="'.$uuid.'"';
        return (
            $this->getStartComment($uuid).
            '<div '.$attributes.'></div>'.
            '<script type="application/json" '.$attributes.'><!--'.$json.'--></script>'.
            $this->getEndComment($uuid)
        );
    }

    public function render($component = null, $data = [])
    {
        if ($component instanceof Renderable) {
            return $this->renderView($component);
        } elseif (is_string($component)) {
            return $this->renderComponent($component, $data);
        }

        $response = $this->renderJobs($this->jobs);
        $html = [];
        foreach ($response->results as $uuid => $job) {
            $html[$uuid] = $job->html;
        }
        return $html;
    }

    public function renderView($view)
    {
        return $view->render(function ($view, $contents) {
            return $this->replaceContents($contents);
        });
    }

    public function renderComponent($name, $data = [])
    {
        $uuid = Uuid::uuid1()->toString();
        $job = [
            'name' => $name,
            'data' => $data
        ];
        $response = $this->renderJobs([
            $uuid => $job
        ]);
        return isset($response->results[$uuid]) ?
            $response->results[$uuid]->html:$this->renderPlaceholder($uuid, $job);
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

    protected function renderJobs($jobs)
    {
        $renderer = $this->container->make(RendererContract::class);
        foreach ($jobs as $uuid => $job) {
            $renderer->addJob($uuid, $job);
        }
        $response = $renderer->render();
        if($response->error !== null) {
            throw new HypernovaException($response->error->getMessage());
        }
        $results = [];
        foreach ($response->results as $uuid => $job) {
            if($job->error !== null) {
                throw new HypernovaException(print_r($job->error, true));
            }
            $html = preg_replace(
                '/data-hypernova-id\=\"[^\"]+\"/i',
                'data-hypernova-id="'.$uuid.'"',
                $job->html
            );
            $job->html = $html;
            $results[$uuid] = $job;
        }
        $response->results = $results;

        return $response;
    }

    protected function replaceContents($contents)
    {
        $response = $this->renderJobs($this->jobs);
        foreach ($response->results as $uuid => $html) {
            $start = preg_quote($this->getStartComment($uuid), '/');
            $end = preg_quote($this->getEndComment($uuid), '/');
            $contents = preg_replace('/'.$start.'(.*?)'.$end.'/', $html, $contents);
        }
        return $contents;
    }
}
