<?php

use Folklore\Hypernova\HypernovaMiddleware;

/**
 * @coversDefaultClass \Folklore\Hypernova\HypernovaMiddleware
 */
class HypernovaMiddlewareTest extends TestCase
{
    protected $job = [
        'name' => 'Component',
        'data' => [
            'test' => 1
        ]
    ];

    /**
     * Test register
     *
     * @test
     * @covers ::handle
     */
    public function testHandle()
    {
        $middleware = $this->app->make(HypernovaMiddleware::class);
        $next = function () {
            return response()->view('single');
        };
        $response = $middleware->handle(app('request'), $next);
        $html = $response->getContent();
        $uuid = array_keys(app('hypernova')->getJobs())[0];

        $startComment = '<!-- START hypernova['.$uuid.'] -->';
        $endComment = '<!-- END hypernova['.$uuid.'] -->';
        $this->assertNotRegExp('/^'.preg_quote($startComment, '/').'/', $html);
        $this->assertNotRegExp('/'.preg_quote($endComment, '/').'$/', $html);

        $document = new \DOMDocument();
        $document->loadHTML($response);
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
        $this->clearViewCache();
    }

    /**
     * Test that handle reject redirect
     *
     * @test
     * @covers ::handle
     */
    public function testHandleRejectRedirect()
    {
        $middleware = $this->app->make(HypernovaMiddleware::class);
        $redirectResponse = redirect('/');
        $next = function () use ($redirectResponse) {
            return $redirectResponse;
        };
        $response = $middleware->handle(app('request'), $next);
        $this->assertEquals($redirectResponse, $response);
    }

    /**
     * Test that handle reject other content type than text/html
     *
     * @test
     * @covers ::handle
     */
    public function testHandleRejectOtherContentType()
    {
        $middleware = $this->app->make(HypernovaMiddleware::class);
        $jsonResponse = response()->json([]);
        $next = function () use ($jsonResponse) {
            return $jsonResponse;
        };
        $response = $middleware->handle(app('request'), $next);
        $this->assertEquals($jsonResponse, $response);
    }
}
