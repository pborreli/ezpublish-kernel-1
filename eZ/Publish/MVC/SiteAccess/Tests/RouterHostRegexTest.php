<?php
/**
 * File containing the eZ\Publish\MVC\SiteAccess\Tests\RouterHostRegexTest class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\SiteAccess\Tests;
use PHPUnit_Framework_TestCase,
    eZ\Publish\MVC\SiteAccess\Router;

class RouterHostRegexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \eZ\Publish\MVC\SiteAccess\Router::__construct
     */
    public function testConstruct()
    {
        return new Router(
            "default_sa",
            array(
                "Regex\\Host" => array(
                    "regex" => "^(\\w+_sa)$",
                ),
                "Map\\URI" => array(
                    "first_sa" => "first_sa",
                    "second_sa" => "second_sa",
                ),
                "Map\\Host" => array(
                    "first_sa" => "first_sa",
                    "first_siteaccess" => "first_sa",
                ),
            )
        );
    }

    /**
     * @depends testConstruct
     * @dataProvider matchProvider
     * @covers \eZ\Publish\MVC\SiteAccess\Router::match
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Map::__construct
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Map::match
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Map\URI::__construct
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Map\Host::__construct
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Regex::__construct
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Regex::match
     * @covers \eZ\Publish\MVC\SiteAccess\Matcher\Regex\Host::__construct
     */
    public function testMatch( $url, $siteAccess, $router )
    {
        $this->assertSame( $siteAccess, $router->match( $url ) );
    }

    public function matchProvider()
    {
        return array(
            array( "http://example.com", "default_sa" ),
            array( "https://example.com", "default_sa" ),
            array( "http://example.com/", "default_sa" ),
            array( "https://example.com/", "default_sa" ),
            array( "http://example.com//", "default_sa" ),
            array( "https://example.com//", "default_sa" ),
            array( "http://example.com:8080/", "default_sa" ),
            array( "http://example.com/first_siteaccess/", "default_sa" ),
            array( "http://example.com/?first_siteaccess", "default_sa" ),
            array( "http://example.com/?first_sa", "default_sa" ),
            array( "http://example.com/first_salt", "default_sa" ),
            array( "http://example.com/first_sa.foo", "default_sa" ),
            array( "http://example.com/test", "default_sa" ),
            array( "http://example.com/test/foo/", "default_sa" ),
            array( "http://example.com/test/foo/bar/", "default_sa" ),
            array( "http://example.com/test/foo/bar/first_sa", "default_sa" ),
            array( "http://example.com/default_sa", "default_sa" ),

            array( "http://example.com/first_sa", "first_sa" ),
            array( "http://example.com/first_sa/", "first_sa" ),
            // Double slashes shouldn't be considered as one
            array( "http://example.com//first_sa//", "default_sa" ),
            array( "http://example.com///first_sa///test", "default_sa" ),
            array( "http://example.com//first_sa//foo/bar", "default_sa" ),
            array( "http://example.com/first_sa/foo", "first_sa" ),
            array( "http://example.com:82/first_sa/", "first_sa" ),
            array( "http://third_siteaccess/first_sa/", "first_sa" ),
            array( "http://first_sa/", "first_sa" ),
            array( "https://first_sa/", "first_sa" ),
            array( "http://first_sa:81/", "first_sa" ),
            array( "http://first_sa/", "first_sa" ),
            array( "http://first_sa:82/", "first_sa" ),
            array( "http://first_sa:83/", "first_sa" ),
            array( "http://first_sa/foo/", "first_sa" ),
            array( "http://first_sa:82/foo/", "first_sa" ),
            array( "http://first_sa:83/foo/", "first_sa" ),
            array( "http://first_sa/foobar/", "first_sa" ),
            array( "http://second_sa:82/", "second_sa" ),
            array( "http://second_sa:83/", "second_sa" ),
            array( "http://second_sa/foo/", "second_sa" ),
            array( "http://second_sa:82/foo/", "second_sa" ),
            array( "http://second_sa:83/foo/", "second_sa" ),
            array( "http://second_sa/foobar/", "second_sa" ),

            array( "http://example.com/second_sa", "second_sa" ),
            array( "http://example.com/second_sa/", "second_sa" ),
            array( "http://example.com/second_sa?param1=foo", "second_sa" ),
            array( "http://example.com/second_sa/foo/", "second_sa" ),
            array( "http://example.com:82/second_sa/", "second_sa" ),
            array( "http://example.com:83/second_sa/", "second_sa" ),
            array( "http://first_siteaccess:82/second_sa/", "second_sa" ),
            array( "http://first_siteaccess:83/second_sa/", "second_sa" ),
        );
    }
}