<?php

/**
 * @coversDefaultClass \Folklore\Hypernova\Hypernova
 */
class HypernovaTest extends TestCase
{
    protected $hypernova;

    protected $job = [
        'name' => 'Component',
        'data' => [
            'test' => 1
        ]
    ];

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->hypernova = app('hypernova');
    }

    /**
     * Test adding a job
     *
     * @test
     * @covers ::__construct
     * @covers ::addJob
     * @covers ::getJob
     * @covers ::getJobs
     * @covers ::setJobs
     * @covers ::clearJobs
     */
    public function testAddJob()
    {
        $uuid1 = $this->hypernova->addJob($this->job['name'], $this->job['data']);
        $this->assertEquals($this->job, $this->hypernova->getJob($uuid1));

        $uuid2 = $this->hypernova->addJob($this->job);
        $this->assertEquals($this->job, $this->hypernova->getJob($uuid2));

        $this->assertEquals([
            $uuid1 => $this->job,
            $uuid2 => $this->job
        ], $this->hypernova->getJobs());

        $this->hypernova->clearJobs();
        $this->assertEquals([], $this->hypernova->getJobs());

        $this->hypernova->setJobs([
            $uuid1 => $this->job,
            $uuid2 => $this->job
        ]);
        $this->assertEquals([
            $uuid1 => $this->job,
            $uuid2 => $this->job
        ], $this->hypernova->getJobs());
    }

    /**
     * Test rendering placeholder
     *
     * @test
     * @covers ::renderPlaceholder
     * @covers ::getStartComment
     * @covers ::getEndComment
     */
    public function testRenderPlaceholder()
    {
        $uuid = $this->hypernova->addJob($this->job['name'], $this->job['data']);
        $placeholder = $this->hypernova->renderPlaceholder($uuid);

        $startComment = '<!-- START hypernova['.$uuid.'] -->';
        $endComment = '<!-- END hypernova['.$uuid.'] -->';
        $this->assertRegExp('/^'.preg_quote($startComment, '/').'/', $placeholder);
        $this->assertRegExp('/'.preg_quote($endComment, '/').'$/', $placeholder);

        $this->assertHtmlForJob($placeholder, $this->job, $uuid);
    }

    /**
     * Test render
     *
     * @test
     * @covers ::render
     * @covers ::renderJobs
     * @covers ::replaceContents
     * @covers ::getStartComment
     * @covers ::getEndComment
     */
    public function testRender()
    {
        $uuid = $this->hypernova->addJob($this->job['name'], $this->job['data']);
        $html = $this->hypernova->render();

        $this->assertArrayHasKey($uuid, $html);
        $this->assertHtmlForJob($html[$uuid], $this->job, $uuid);

        $view = view('single');
        $this->hypernova->clearJobs();
        $html = $this->hypernova->render($view);
        $uuid = array_keys($this->hypernova->getJobs())[0];
        $document = new \DOMDocument();
        $document->loadHTML($html);
        $divOther = $document->documentElement->getElementsByTagName('div')[0];
        $divWrapper = $document->documentElement->getElementsByTagName('div')[1];

        $this->assertEquals('other', $divOther->getAttribute('class'));
        $this->assertEquals('wrapper', $divWrapper->getAttribute('class'));

        $wrapperHtml = '';
        $children = $divWrapper->childNodes;
        foreach ($children as $child) {
            $wrapperHtml .= $document->saveHTML($child);
        }
        $wrapperHtml = trim($wrapperHtml);
        $this->assertHtmlForJob($wrapperHtml, $this->job, $uuid);
    }

    /**
     * Test modifying a response
     *
     * @test
     * @covers ::modifyResponse
     * @covers ::renderJobs
     * @covers ::replaceContents
     * @covers ::getStartComment
     * @covers ::getEndComment
     */
    public function testModifyResponse()
    {
        $response = response()->view('single');
        $html = $this->hypernova->modifyResponse($response);
        $uuid = array_keys($this->hypernova->getJobs())[0];
        $document = new \DOMDocument();
        $document->loadHTML($html);
        $divOther = $document->documentElement->getElementsByTagName('div')[0];
        $divWrapper = $document->documentElement->getElementsByTagName('div')[1];

        $this->assertEquals('other', $divOther->getAttribute('class'));
        $this->assertEquals('wrapper', $divWrapper->getAttribute('class'));

        $wrapperHtml = '';
        $children = $divWrapper->childNodes;
        foreach ($children as $child) {
            $wrapperHtml .= $document->saveHTML($child);
        }
        $wrapperHtml = trim($wrapperHtml);
        $this->assertHtmlForJob($wrapperHtml, $this->job, $uuid);
    }
}
