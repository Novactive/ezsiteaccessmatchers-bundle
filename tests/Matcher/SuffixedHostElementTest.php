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
use Novactive\EzSiteaccessMatchersBundle\Matcher\SuffixedHostElement;
use PHPUnit\Framework\TestCase;

/**
 * SuffixedHostElement Test Class
 *
 * @author Guillaume Maïssa <g.maissa@novactive.com>
 */
class SuffixedHostElementTest extends TestCase
{
    /**
     * @dataProvider provideMatch
     */
    public function testMatch($matcherConfig = array(), SimplifiedRequest $request, $expected)
    {
        $matcher = new SuffixedHostElement($matcherConfig);
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
            [['elementNumber' => 1, 'suffix' => 'site'], $request1, 'a_site'],
            [['elementNumber' => 1, 'suffix' => 'test_site'], $request1, 'a_test_site'],
            [['elementNumber' => 2, 'suffix' => 'site' ], $request2, 'a_site'],
            [['elementNumber' => 2, 'suffix' => 'test_site' ], $request2, 'a_test_site'],
        ];
    }

    /**
     * @dataProvider provideReverseMatch
     */
    public function testReverseMatch($matcherConfig = array(), SimplifiedRequest $request, $siteaccess, $expected)
    {
        $matcher = new SuffixedHostElement($matcherConfig);
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
            [['elementNumber' => 1, 'suffix' => 'site'], $request1, 'b_site', $request3],
            [['elementNumber' => 1, 'suffix' => 'site'], $request3, 'a_site', $request1],
            [['elementNumber' => 2, 'suffix' => 'site'], $request2, 'b_site', $request4],
            [['elementNumber' => 2, 'suffix' => 'site'], $request4, 'a_site', $request2]
        ];
    }
}
