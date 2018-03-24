<?php
/**
 * File part of the Novactive eZSiteaccessMatchers Bundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\EzSiteaccessMatchersBundle\Matcher;

use eZ\Bundle\EzPublishCoreBundle\SiteAccess\Matcher;
use eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\VersatileMatcher;

/**
 * Prefixed HostElement Siteaccess Matcher class
 *
 * @author Guillaume Maïssa <g.maissa@novactive.com>
 */
class PrefixedHostElement implements VersatileMatcher, Matcher
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest
     */
    private $request;

    /**
     * Number of elements to take into account.
     *
     * @var int
     */
    private $elementNumber;

    /**
     * Host elements used for matching as an array.
     *
     * @var array
     */
    private $hostElements;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param array $matchingConfiguration configuration for the matcher with :
     *                                     - Number of elements to take into account.
     *                                     - prefix string to be concatenated to the host element
     */
    public function __construct($matchingConfiguration)
    {
        $this->setMatchingConfiguration($matchingConfiguration);
    }

    /**
     * {@inheritdoc}
     */
    public function setMatchingConfiguration($matchingConfiguration)
    {
        $elementNumber = $matchingConfiguration['elementNumber'];
        $prefix = $matchingConfiguration['prefix'];
        if (is_array($elementNumber)) {
            // DI config parser will create an array with 'value' => number
            $elementNumber = current($elementNumber);
        }

        $this->elementNumber = (int)$elementNumber;
        $this->prefix = $prefix;
    }

    public function __sleep()
    {
        return array('elementNumber', 'hostElements', 'prefix');
    }

    /**
     * Returns matching Siteaccess.
     *
     * @return string|false Siteaccess matched or false.
     */
    public function match()
    {
        $elements = $this->getHostElements();
        $match = false;

        if (isset($elements[$this->elementNumber - 1])) {
            $match = sprintf('%s_%s', $this->prefix, $elements[$this->elementNumber - 1]);
        }

        return $match;
    }

    public function getName()
    {
        return 'prefixed:host:element';
    }

    /**
     * Injects the request object to match against.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     */
    public function setRequest(SimplifiedRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function reverseMatch($siteAccessName)
    {
        $hostElements = explode('.', $this->request->host);
        $elementNumber = $this->elementNumber - 1;
        if (!isset($hostElements[$elementNumber])) {
            return null;
        }

        $sitaccessParts = explode('_', $siteAccessName);

        $hostElements[$elementNumber] = $sitaccessParts[count($sitaccessParts) -1];
        $this->request->setHost(implode('.', $hostElements));

        return $this;
    }

    /**
     * @return array
     */
    private function getHostElements()
    {
        if (isset($this->hostElements)) {
            return $this->hostElements;
        } elseif (!isset($this->request)) {
            return array();
        }

        $elements = explode('.', $this->request->host);

        return $this->hostElements = $elements;
    }
}
