<?php

use Folklore\Hypernova\Hypernova;
use Folklore\Hypernova\Contracts\Renderer as RendererContract;
use WF\Hypernova\Renderer;

/**
 * @coversDefaultClass \Folklore\Hypernova\HypernovaServiceProvider
 */
class ServiceProviderTest extends TestCase
{
    /**
     * Test register
     *
     * @test
     * @covers ::register
     * @covers ::registerHypernova
     * @covers ::registerRenderer
     */
    public function testRegister()
    {
        $this->assertTrue($this->app->bound('hypernova'));
        $this->assertInstanceOf(Hypernova::class, $this->app->make('hypernova'));

        $this->assertTrue($this->app->bound(RendererContract::class));
        $this->assertInstanceOf(Renderer::class, $this->app->make(RendererContract::class));
    }

    /**
     * Test boot
     *
     * @test
     * @covers ::boot
     * @covers ::bootPublishes
     * @covers ::bootBlade
     */
    public function testBoot()
    {
        $config = include __DIR__.'/../src/config/hypernova.php';
        $this->assertEquals($config, $this->app['config']['hypernova']);

        $directives = $this->app['view.engine.resolver']->resolve('blade')->getCompiler()->getCustomDirectives();
        $this->assertArrayHasKey('hypernova', $directives);
        $output = "<?php echo \$app['hypernova']->pushJob('test'); ?>";
        $this->assertEquals($output, $directives['hypernova']('(\'test\')'));
        $this->assertEquals($output, $directives['hypernova']('\'test\''));
    }
}
