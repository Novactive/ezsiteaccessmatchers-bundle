<?php
/**
 * File part of the Novactive eZSiteaccessMatchers Bundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\EzSiteaccessMatchersBundle\Matcher;

use eZ\Bundle\EzPublishCoreBundle\SiteAccess\Matcher;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Routing\SimplifiedRequest;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\VersatileMatcher;

/**
 * Extended HostElement Siteaccess Matcher class
 *
 * @author Guillaume Maïssa <g.maissa@novactive.com>
 */
class ExtendedHostElement implements VersatileMatcher, Matcher
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
    protected $patterns;

    /**
     * @var string
     */
    protected $replacements = [
        'pattern' => ['-'],
        'replacement' => ['_']
    ];

    /**
     * Constructor.
     *
     * @param array $matchingConfiguration configuration for the matcher with :
     *                                     - Number of elements to take into account.
     *                                     - prefix string to be concatenated to the host element
     *
     * @throws InvalidArgumentException if config is not valid
     */
    public function __construct($matchingConfiguration)
    {
        $this->setMatchingConfiguration($matchingConfiguration);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException if config is not valid
     */
    public function setMatchingConfiguration($matchingConfiguration)
    {
        $elementNumber = $matchingConfiguration['elementNumber'];
        if (is_array($elementNumber)) {
            // DI config parser will create an array with 'value' => number
            $elementNumber = current($elementNumber);
        }
        $this->elementNumber = (int)$elementNumber;

        if (isset($matchingConfiguration['replacements'])) {
            if (!isset($matchingConfiguration['replacements']['pattern']) ||
                !isset($matchingConfiguration['replacements']['replacement'])
            ) {
                throw new InvalidArgumentException(
                    'replacements',
                    'Missing pattern or replacement parameter'
                );
            }
            $replacements = $matchingConfiguration['replacements'];
            $this->replacements = $replacements;
        }
    }

    public function __sleep()
    {
        return array('elementNumber', 'hostElements', 'replacements');
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
            $match = preg_replace(
                $this->preparePatterns($this->replacements['pattern']),
                $this->replacements['replacement'],
                $elements[$this->elementNumber - 1]
            );
        }

        return $match;
    }

    public function getName()
    {
        return 'extended:host:element';
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

        $cleanedSiteAccessName = preg_replace(
            $this->preparePatterns($this->replacements['replacement']),
            $this->replacements['pattern'],
            $siteAccessName
        );

        $hostElements[$elementNumber] = $cleanedSiteAccessName;
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

    private function preparePatterns($patterns)
    {
        if (is_array($patterns)) {
            foreach ($patterns as $key => $value) {
                $patterns[$key] = sprintf('/%s/', $value);
            }
        } else {
            $patterns = sprintf('/%s/', $patterns);
        }

        return $patterns;
    }
}
