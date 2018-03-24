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
use PHPUnit\Framework\TestCase;

/**
 * ExtendedHostElement Test Class
 *
 * @author Guillaume Maïssa <g.maissa@novactive.com>
 */
class ExtendedHostElementTest extends TestCase
{
    /**
     * @dataProvider provideMatch
     */
    public function testMatch($matcherConfig = array(), SimplifiedRequest $request, $expected)
    {
        $matcher = new ExtendedHostElement($matcherConfig);
        $matcher->setRequest($request);
        $this->assertEquals($expected, $matcher->match());
    }

    public function provideMatch()
    {
        $request1 = new SimplifiedRequest();
        $request1->setHost('my-site.test.com');
        $request2 = new SimplifiedRequest();
        $request2->setHost('my-test-site.test.com');
        $request3 = new SimplifiedRequest();
        $request3->setHost('www.my-site.test.com');
        $request4 = new SimplifiedRequest();
        $request4->setHost('www.my-test-site.test.com');
        return [
            [['elementNumber' => 1], $request1, 'my_site'],
            [['elementNumber' => 1, 'replacements' => ['pattern' => '-', 'replacement' => '_']], $request1, 'my_site'],
            [['elementNumber' => 1], $request2, 'my_test_site'],
            [['elementNumber' => 1, 'replacements' => ['pattern' => '-', 'replacement' => '_']], $request2, 'my_test_site'],
            [['elementNumber' => 2], $request3, 'my_site'],
            [['elementNumber' => 2, 'replacements' => ['pattern' => '-', 'replacement' => '_']], $request3, 'my_site'],
            [['elementNumber' => 2], $request4, 'my_test_site'],
            [['elementNumber' => 2, 'replacements' => ['pattern' => '-', 'replacement' => '_']], $request4, 'my_test_site']
        ];
    }

    /**
     * @dataProvider provideReverseMatch
     */
    public function testReverseMatch($matcherConfig = array(), SimplifiedRequest $request, $siteaccess, $expected)
    {
        $matcher = new ExtendedHostElement($matcherConfig);
        $matcher->setRequest($request);
        $this->assertEquals($expected, $matcher->reverseMatch($siteaccess)->getRequest());
    }

    public function provideReverseMatch()
    {
        $request1 = new SimplifiedRequest();
        $request1->setHost('my-site.test.com');
        $request2 = new SimplifiedRequest();
        $request2->setHost('my-test-site.test.com');
        $request3 = new SimplifiedRequest();
        $request3->setHost('www.my-site.test.com');
        $request4 = new SimplifiedRequest();
        $request4->setHost('www.my-test-site.test.com');
        return [
            [['elementNumber' => 1], $request1, 'my_test_site', $request2],
            [['elementNumber' => 1], $request2, 'my_site', $request1],
            [['elementNumber' => 2], $request3, 'my_test_site', $request4],
            [['elementNumber' => 2], $request4, 'my_site', $request3]
        ];
    }

    /**
     * @dataProvider provideConfigException
     */
    public function testConfigException($matcherConfig)
    {
        $this->expectException('\eZ\Publish\Core\Base\Exceptions\InvalidArgumentException');
        $this->expectExceptionMessage('Missing pattern or replacement parameter');
        new ExtendedHostElement($matcherConfig);
    }

    public function provideConfigException()
    {
        return [
            [['elementNumber' => 1, 'replacements' => ['pattern' => '-']]],
            [['elementNumber' => 1, 'replacements' => ['replacement' => '_']]],
            [['elementNumber' => 1, 'replacements' => ['_']]]
        ];
    }
}
