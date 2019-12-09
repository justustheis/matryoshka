<?php

use JustusTheis\Matryoshka\RussianCaching;
use JustusTheis\Matryoshka\BladeDirective;

class BladeDirectiveTest extends TestCase
{
    protected $doll;

    /** @test */
    public function it_sets_up_the_opening_cache_directive()
    {
        $directive = $this->createNewCacheDirective();

        $isCached = $directive->setUp("testView", $post = $this->makePost());

        $this->assertFalse($isCached);

        echo '<div>fragment</div>';

        $cachedFragment = $directive->tearDown();

        $this->assertEquals('<div>fragment</div>', $cachedFragment);
        $this->assertTrue($this->doll->has("testView" . $post->getCacheKey()));
    }

    /** @test */
    function it_can_use_a_string_as_the_cache_key()
    {
        $doll = $this->prophesize(RussianCaching::class);
        $directive = new BladeDirective($doll->reveal());

        $doll->has('foo', 'views')->shouldBeCalled();
        $directive->setUp('foo');

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test */
    function it_can_use_a_collection_as_the_cache_key()
    {
        $doll = $this->prophesize(RussianCaching::class);
        $directive = new BladeDirective($doll->reveal());

        $collection = collect(['one', 'two']);
        $doll->has("testKey".md5($collection), 'views')->shouldBeCalled();
        $directive->setUp("testKey", $collection);

        ob_end_clean(); // Since we're not doing teardown.
    }

    /** @test */
    function it_can_use_the_model_to_determine_the_cache_key()
    {
        $doll = $this->prophesize(RussianCaching::class);
        $directive = new BladeDirective($doll->reveal());

        $post = $this->makePost();
        $doll->has('testKey' . 'Post/1-' . $post->updated_at->timestamp, 'views')->shouldBeCalled();
        $directive->setUp("testKey", $post);

        ob_end_clean(); // Since we're not doing teardown.
    }

   /** @test */
    function it_throws_an_exception_if_it_cannot_determine_the_cache_key()
    {
        $this->expectException(Exception::class);
        $directive = $this->createNewCacheDirective();

        $directive->setUp("testKey", new UnCacheablePost);
    }

    protected function createNewCacheDirective()
    {
        $cache = new \Illuminate\Cache\Repository(
            new \Illuminate\Cache\ArrayStore
        );

        $this->doll = new RussianCaching($cache);

        return new BladeDirective($this->doll);
    }
}

class UnCacheablePost extends \Illuminate\Database\Eloquent\Model
{
}
