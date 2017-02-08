<?php

/**
 * @coversDefaultClass \Folklore\Hypernova\Hypernova
 */
class HypernovaTest extends TestCase
{
    protected $hypernova;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->hypernova = app('hypernova');
    }

    /**
     * Test adding a job
     *
     * @test
     * @covers ::addJob
     * @covers ::getJob
     */
    public function testAddJob()
    {
        $job = [
            'name' => 'MyComponent',
            'data' => [
                'test' => 1
            ]
        ];

        $uuid = $this->hypernova->addJob($job['name'], $job['data']);
        $this->assertEquals($job, $this->hypernova->getJob($uuid));

        $uuid = $this->hypernova->addJob($job);
        $this->assertEquals($job, $this->hypernova->getJob($uuid));
    }

    /**
     * Test rendering placeholder
     *
     * @test
     * @covers ::renderPlaceholder
     */
    public function testRenderPlaceholder()
    {
        $job = [
            'name' => 'MyComponent',
            'data' => [
                'test' => 1
            ]
        ];

        $uuid = $this->hypernova->addJob($job['name'], $job['data']);
        $placeholder = $this->hypernova->renderPlaceholder($uuid);
        $document = new \DOMDocument();
        $document->loadHTML($placeholder);
        $div = $document->documentElement->getElementsByTagName('div')[0];
        $this->assertEquals($job['name'], $div->getAttribute('data-hypernova-key'));
        $this->assertEquals($uuid, $div->getAttribute('data-hypernova-id'));

        $script = $document->documentElement->getElementsByTagName('script')[0];
        $this->assertEquals($job['name'], $script->getAttribute('data-hypernova-key'));
        $this->assertEquals($uuid, $script->getAttribute('data-hypernova-id'));
        $json = preg_replace('/^\<\!\-\-/', '', preg_replace('/\-\-\>$/', '', $script->textContent));
        $data = json_decode($json, true);
        $this->assertEquals($job['data'], $data);
    }
}
