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
}
