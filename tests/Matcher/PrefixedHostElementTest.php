<?php
/**
 * File part of the Novactive eZSiteaccessMatchers Bundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\EzSiteaccessMatchersBundle\Test\Matcher;

use eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Novactive\EzSiteaccessMatchersBundle\Matcher\ExtendedHostElement;
use Novactive\EzSiteaccessMatchersBundle\Matcher\PrefixedHostElement;
use Novactive\EzSiteaccessMatchersBundle\Matcher\SuffixedHostElement;
use PHPUnit\Framework\TestCase;

/**
 * SuffixedHostElement Test Class
 *
 * @author Guillaume Maïssa <g.maissa@novactive.com>
 */
class PrefixedHostElementTest extends TestCase
{
    /**
     * @dataProvider provideMatch
     */
    public function testMatch($matcherConfig = array(), SimplifiedRequest $request, $expected)
    {
        $matcher = new PrefixedHostElement($matcherConfig);
        $matcher->setRequest($request);
        $this->assertEquals($expected, $matcher->match());
    }

    public function provideMatch()
    {
        $request1 = new SimplifiedRequest();
        $request1->setHost('a.test.com');
        $request2 = new SimplifiedRequest();
        $request2->setHost('www.a.test.com');
        return [
            [['elementNumber' => 1, 'prefix' => 'site'], $request1, 'site_a'],
            [['elementNumber' => 1, 'prefix' => 'test_site'], $request1, 'test_site_a'],
            [['elementNumber' => 2, 'prefix' => 'site' ], $request2, 'site_a'],
            [['elementNumber' => 2, 'prefix' => 'test_site' ], $request2, 'test_site_a'],
        ];
    }

    /**
     * @dataProvider provideReverseMatch
     */
    public function testReverseMatch($matcherConfig = array(), SimplifiedRequest $request, $siteaccess, $expected)
    {
        $matcher = new PrefixedHostElement($matcherConfig);
        $matcher->setRequest($request);
        $this->assertEquals($expected, $matcher->reverseMatch($siteaccess)->getRequest());
    }

    public function provideReverseMatch()
    {
        $request1 = new SimplifiedRequest();
        $request1->setHost('a.test.com');
        $request2 = new SimplifiedRequest();
        $request2->setHost('www.a.test.com');
        $request3 = new SimplifiedRequest();
        $request3->setHost('b.test.com');
        $request4 = new SimplifiedRequest();
        $request4->setHost('www.b.test.com');
        return [
            [['elementNumber' => 1, 'prefix' => 'site'], $request1, 'site_b', $request3],
            [['elementNumber' => 1, 'prefix' => 'test_site'], $request1, 'test_site_b', $request3],
            [['elementNumber' => 1, 'prefix' => 'site'], $request3, 'site_a', $request1],
            [['elementNumber' => 1, 'prefix' => 'test_site'], $request3, 'test_site_a', $request1],
            [['elementNumber' => 2, 'prefix' => 'site'], $request2, 'site_b', $request4],
            [['elementNumber' => 2, 'prefix' => 'test_site'], $request2, 'test_site_b', $request4],
            [['elementNumber' => 2, 'prefix' => 'site'], $request4, 'site_a', $request2],
            [['elementNumber' => 2, 'prefix' => 'test_site'], $request4, 'test_site_a', $request2]
        ];
    }
}
