<?php
/**
 * File contains: eZ\Publish\Core\Repository\Tests\Service\Mock\SearchTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository\Tests\Service\Mock;

use eZ\Publish\Core\Repository\Tests\Service\Mock\Base as BaseServiceMockTest;
use eZ\Publish\Core\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\SPI\Persistence\Content\ContentInfo as SPIContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type as SPIContentType;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use Exception;

/**
 * Mock test case for Search service
 */
class SearchTest extends BaseServiceMockTest
{
    protected $repositoryMock;

    protected $domainMapperMock;

    protected $permissionsCriterionHandlerMock;

    /**
     * Test for the __construct() method.
     *
     * @covers \eZ\Publish\Core\Repository\SearchService::__construct
     */
    public function testConstructor()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $settings = array( "teh setting" );

        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            $settings
        );

        $this->assertAttributeSame(
            $repositoryMock,
            "repository",
            $service
        );

        $this->assertAttributeSame(
            $searchHandlerMock,
            "searchHandler",
            $service
        );

        $this->assertAttributeSame(
            $domainMapperMock,
            "domainMapper",
            $service
        );

        $this->assertAttributeSame(
            $permissionsCriterionHandlerMock,
            "permissionsCriterionHandler",
            $service
        );

        $this->assertAttributeSame(
            $settings,
            "settings",
            $service
        );
    }

    public function providerForFindContentValidatesLocationCriteriaAndSortClauses()
    {
        return array(
            array(
                new Query( array( "filter" => new Criterion\Location\Depth( Criterion\Operator::LT, 2 ) ) ),
                "Argument '\$query' is invalid: Location criterions cannot be used in Content search"
            ),
            array(
                new Query( array( "query" => new Criterion\Location\Depth( Criterion\Operator::LT, 2 ) ) ),
                "Argument '\$query' is invalid: Location criterions cannot be used in Content search"
            ),
            array(
                new Query(
                    array(
                        "query" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\Depth( Criterion\Operator::LT, 2 )
                            )
                        )
                    )
                ),
                "Argument '\$query' is invalid: Location criterions cannot be used in Content search"
            ),
            array(
                new Query( array( "sortClauses" => array( new SortClause\Location\Id() ) ) ),
                "Argument '\$query' is invalid: Location sort clauses cannot be used in Content search"
            ),
        );
    }

    /**
     * @dataProvider providerForFindContentValidatesLocationCriteriaAndSortClauses
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContentValidatesLocationCriteriaAndSortClauses( $query, $exceptionMessage )
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();

        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        try
        {
            $service->findContent( $query );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertEquals( $exceptionMessage, $e->getMessage() );
            throw $e;
        }

        $this->fail( "Expected exception was not thrown" );
    }

    public function providerForFindSingleValidatesLocationCriteria()
    {
        return array(
            array(
                new Criterion\Location\Depth( Criterion\Operator::LT, 2 ),
                "Argument '\$filter' is invalid: Location criterions cannot be used in Content search"
            ),
            array(
                new Criterion\LogicalAnd(
                    array(
                        new Criterion\Location\Depth( Criterion\Operator::LT, 2 )
                    )
                ),
                "Argument '\$filter' is invalid: Location criterions cannot be used in Content search"
            ),
        );
    }

    /**
     * @dataProvider providerForFindSingleValidatesLocationCriteria
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindSingleValidatesLocationCriteria( $criterion, $exceptionMessage )
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        try
        {
            $service->findSingle( $criterion );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertEquals( $exceptionMessage, $e->getMessage() );
            throw $e;
        }

        $this->fail( "Expected exception was not thrown" );
    }

    /**
     * Test for the findContent() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findContent
     * @expectedException \Exception
     * @expectedExceptionMessage Handler threw an exception
     */
    public function testFindContentThrowsHandlerException()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();

        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterionMock */
        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();
        $query = new Query( array( "filter" => $criterionMock ) );

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->throwException( new Exception( "Handler threw an exception" ) ) );

        $service->findContent( $query, array(), true );
    }

    /**
     * Test for the findContent() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findContent
     */
    public function testFindContentNoPermissionsFilter()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            array()
        );

        $repositoryMock->expects( $this->never() )->method( "hasAccess" );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentService" )
            ->will(
                $this->returnValue(
                    $contentServiceMock = $this
                        ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\ContentService" )
                        ->disableOriginalConstructor()
                        ->getMock()
                )
            );

        $serviceQuery = new Query;
        $handlerQuery = new Query( array( "filter" => new Criterion\MatchAll(), "limit" => SearchService::MAX_LIMIT ) );
        $fieldFilters = array();
        $spiContentInfo = new SPIContentInfo;
        $contentMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Content" );

        /** @var \PHPUnit_Framework_MockObject_MockObject $searchHandlerMock */
        $searchHandlerMock->expects( $this->once() )
            ->method( "findContent" )
            ->with( $this->equalTo( $handlerQuery ), $this->equalTo( $fieldFilters ) )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiContentInfo ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $contentServiceMock
            ->expects( $this->once() )
            ->method( "loadContent" )
            ->will( $this->returnValue( $contentMock ) );

        $result = $service->findContent( $serviceQuery, $fieldFilters, false );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $contentMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * Test for the findContent() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findContent
     */
    public function testFindContentWithPermission()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            array()
        );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentService" )
            ->will(
                $this->returnValue(
                    $contentServiceMock = $this
                        ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\ContentService" )
                        ->disableOriginalConstructor()
                        ->getMock()
                )
            );

        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();
        $query = new Query( array( "filter" => $criterionMock, "limit" => 10 ) );
        $fieldFilters = array();
        $spiContentInfo = new SPIContentInfo;
        $contentMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Content" );

        /** @var \PHPUnit_Framework_MockObject_MockObject $searchHandlerMock */
        $searchHandlerMock->expects( $this->once() )
            ->method( "findContent" )
            ->with( $this->equalTo( $query ), $this->equalTo( $fieldFilters ) )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiContentInfo ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $domainMapperMock->expects( $this->never() )
            ->method( $this->anything() );

        $contentServiceMock
            ->expects( $this->once() )
            ->method( "loadContent" )
            ->will( $this->returnValue( $contentMock ) );

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->returnValue( true ) );

        $result = $service->findContent( $query, $fieldFilters, true );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $contentMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * Test for the findContent() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findContent
     */
    public function testFindContentWithNoPermission()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject $searchHandlerMock */
        $searchHandlerMock->expects( $this->never() )->method( "findContent" );

        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();
        $query = new Query( array( "filter" => $criterionMock ) );

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->returnValue( false ) );

        $result = $service->findContent( $query, array(), true );

        $this->assertEquals(
            new SearchResult( array( "time" => 0, "totalCount" => 0 ) ),
            $result
        );
    }

    public function providerForTestFindContentValidatesFieldSortClauses()
    {
        $fieldSortClause1 = new SortClause\Field(
            "testContentTypeIdentifier",
            "testFieldDefinitionIdentifier",
            Query::SORT_ASC
        );
        $fieldSortClause2 = new SortClause\Field(
            "testContentTypeIdentifier",
            "testFieldDefinitionIdentifier",
            Query::SORT_ASC,
            "eng-GB"
        );

        return array(
            array(
                array( new SortClause\ContentId(), $fieldSortClause1 ),
                true,
                false,
                "Argument '\$query->sortClauses[1]' is invalid: No language is specified for translatable field"
            ),
            array(
                array( $fieldSortClause2 ),
                false,
                false,
                "Argument '\$query->sortClauses[0]' is invalid: Language is specified for non-translatable field," .
                " null should be used instead"
            ),
            array(
                array( $fieldSortClause1 ),
                false,
                true
            ),
            array(
                array( $fieldSortClause2 ),
                true,
                true
            ),
        );
    }

    /**
     * Test for the findContent() method.
     *
     * @dataProvider providerForTestFindContentValidatesFieldSortClauses
     */
    public function testFindContentValidatesFieldSortClauses( $sortClauses, $isTranslatable, $isValid, $message = null )
    {
        $repositoryMock = $this->getRepositoryMock();
        $contentTypeServiceMock = $this->getMock( "eZ\\Publish\\API\\Repository\\ContentTypeService" );
        $contentTypeMock = $this->getMock( "eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType" );
        $fieldDefinitionMock = $this->getMock( "eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition" );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        $permissionsCriterionHandlerMock
            ->expects( $this->any() )
            ->method( "addPermissionsCriterion" )
            ->will( $this->returnValue( false ) );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentTypeService" )
            ->will( $this->returnValue( $contentTypeServiceMock ) );

        $contentTypeServiceMock
            ->expects( $this->once() )
            ->method( "loadContentTypeByIdentifier" )
            ->with( "testContentTypeIdentifier" )
            ->will( $this->returnValue( $contentTypeMock ) );

        $contentTypeMock
            ->expects( $this->once() )
            ->method( "getFieldDefinition" )
            ->with( "testFieldDefinitionIdentifier" )
            ->will( $this->returnValue( $fieldDefinitionMock ) );

        $fieldDefinitionMock
            ->expects( $this->once() )
            ->method( "__get" )
            ->with( "isTranslatable" )
            ->will( $this->returnValue( $isTranslatable ) );

        try
        {
            $result = $service->findContent(
                new Query( array( "sortClauses" => $sortClauses ) ),
                array(),
                true
            );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertFalse( $isValid, "Invalid sort clause expected" );
            $this->assertEquals( $message, $e->getMessage() );
        }

        if ( $isValid )
        {
            $this->assertTrue( isset( $result ) );
        }
    }

    /**
     * Test for the findContent() method.
     */
    public function testFindContentWithDefaultQueryValues()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $this->getPermissionsCriterionHandlerMock(),
            array()
        );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentService" )
            ->will(
                $this->returnValue(
                    $contentServiceMock = $this
                        ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\ContentService" )
                        ->disableOriginalConstructor()
                        ->getMock()
                )
            );

        $fieldFilters = array();
        $spiContentInfo = new SPIContentInfo;
        $contentMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Content" );
        $domainMapperMock->expects( $this->never() )
            ->method( $this->anything() );

        $contentServiceMock
            ->expects( $this->once() )
            ->method( "loadContent" )
            ->will( $this->returnValue( $contentMock ) );

        /** @var \PHPUnit_Framework_MockObject_MockObject $searchHandlerMock */
        $searchHandlerMock
            ->expects( $this->once() )
            ->method( "findContent" )
            ->with(
                new Query(
                    array(
                        "filter" => new Criterion\MatchAll(),
                        "limit" => 1073741824
                    )
                ),
                array()
            )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiContentInfo ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $result = $service->findContent( new Query(), $fieldFilters, false );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $contentMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * Test for the findSingle() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findSingle
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testFindSingleThrowsNotFoundException()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $this->getPermissionsCriterionHandlerMock(),
            array()
        );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterionMock */
        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();

        $service->findSingle( $criterionMock, array(), true );
    }

    /**
     * Test for the findSingle() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findSingle
     * @expectedException \Exception
     * @expectedExceptionMessage Handler threw an exception
     */
    public function testFindSingleThrowsHandlerException()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterionMock */
        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->throwException( new Exception( "Handler threw an exception" ) ) );

        $service->findSingle( $criterionMock, array(), true );
    }

    /**
     * Test for the findSingle() method.
     *
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::addPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\PermissionsCriterionHandler::getPermissionsCriterion
     * @covers \eZ\Publish\Core\Repository\SearchService::findSingle
     */
    public function testFindSingle()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            array()
        );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentService" )
            ->will(
                $this->returnValue(
                    $contentServiceMock = $this
                        ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\ContentService" )
                        ->disableOriginalConstructor()
                        ->getMock()
                )
            );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterionMock */
        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->returnValue( true ) );

        $fieldFilters = array();
        $spiContentInfo = new SPIContentInfo;
        $contentMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Content" );

        /** @var \PHPUnit_Framework_MockObject_MockObject $searchHandlerMock */
        $searchHandlerMock->expects( $this->once() )
            ->method( "findSingle" )
            ->with( $this->equalTo( $criterionMock ), $this->equalTo( $fieldFilters ) )
            ->will( $this->returnValue( $spiContentInfo ) );

        $domainMapperMock->expects( $this->never() )
            ->method( $this->anything() );

        $contentServiceMock
            ->expects( $this->once() )
            ->method( "loadContent" )
            ->will( $this->returnValue( $contentMock ) );

        $result = $service->findSingle( $criterionMock, $fieldFilters, true );

        $this->assertEquals( $contentMock, $result );
    }

    /**
     * Test for the findLocations() method.
     */
    public function functionFindLocationsWithPermission()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            array()
        );

        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();
        $query = new LocationQuery( array( "filter" => $criterionMock, "limit" => 10 ) );
        $spiLocation = new SPILocation;
        $locationMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Location" );

        /** @var \PHPUnit_Framework_MockObject_MockObject $locationSearchHandlerMock */
        $locationSearchHandlerMock->expects( $this->once() )
            ->method( "findLocations" )
            ->with( $this->equalTo( $query ) )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiLocation ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $domainMapperMock->expects( $this->once() )
            ->method( "buildLocationDomainObject" )
            ->with( $this->equalTo( $spiLocation ) )
            ->will( $this->returnValue( $locationMock ) );

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->returnValue( true ) );

        $result = $service->findLocations( $query, true );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $locationMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * Test for the findLocations() method.
     */
    public function testFindLocationsWithNoPermissionsFilter()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $permissionsCriterionHandlerMock,
            array()
        );

        $repositoryMock->expects( $this->never() )->method( "hasAccess" );

        $serviceQuery = new LocationQuery;
        $handlerQuery = new LocationQuery( array( "filter" => new Criterion\MatchAll(), "limit" => SearchService::MAX_LIMIT ) );
        $spiLocation = new SPILocation;
        $locationMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Location" );

        /** @var \PHPUnit_Framework_MockObject_MockObject $locationSearchHandlerMock */
        $locationSearchHandlerMock->expects( $this->once() )
            ->method( "findLocations" )
            ->with( $this->equalTo( $handlerQuery ) )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiLocation ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $domainMapperMock->expects( $this->once() )
            ->method( "buildLocationDomainObject" )
            ->with( $this->equalTo( $spiLocation ) )
            ->will( $this->returnValue( $locationMock ) );

        $result = $service->findLocations( $serviceQuery, false );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $locationMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * Test for the findLocations() method.
     *
     * @dataProvider providerForTestFindContentValidatesFieldSortClauses
     */
    public function testFindLocationsValidatesFieldSortClauses( $sortClauses, $isTranslatable, $isValid, $message = null )
    {
        $repositoryMock = $this->getRepositoryMock();
        $contentTypeServiceMock = $this->getMock( "eZ\\Publish\\API\\Repository\\ContentTypeService" );
        $contentTypeMock = $this->getMock( "eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType" );
        $fieldDefinitionMock = $this->getMock( "eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition" );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        $permissionsCriterionHandlerMock
            ->expects( $this->any() )
            ->method( "addPermissionsCriterion" )
            ->will( $this->returnValue( false ) );

        $repositoryMock
            ->expects( $this->once() )
            ->method( "getContentTypeService" )
            ->will( $this->returnValue( $contentTypeServiceMock ) );

        $contentTypeServiceMock
            ->expects( $this->once() )
            ->method( "loadContentTypeByIdentifier" )
            ->with( "testContentTypeIdentifier" )
            ->will( $this->returnValue( $contentTypeMock ) );

        $contentTypeMock
            ->expects( $this->once() )
            ->method( "getFieldDefinition" )
            ->with( "testFieldDefinitionIdentifier" )
            ->will( $this->returnValue( $fieldDefinitionMock ) );

        $fieldDefinitionMock
            ->expects( $this->once() )
            ->method( "__get" )
            ->with( "isTranslatable" )
            ->will( $this->returnValue( $isTranslatable ) );

        try
        {
            $result = $service->findLocations(
                new LocationQuery( array( "sortClauses" => $sortClauses ) ),
                true
            );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertFalse( $isValid, "Invalid sort clause expected" );
            $this->assertEquals( $message, $e->getMessage() );
        }

        if ( $isValid )
        {
            $this->assertTrue( isset( $result ) );
        }
    }

    /**
     * Test for the findLocations() method.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Handler threw an exception
     */
    public function testFindLocationsThrowsHandlerException()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $permissionsCriterionHandlerMock = $this->getPermissionsCriterionHandlerMock();

        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $this->getDomainMapperMock(),
            $permissionsCriterionHandlerMock,
            array()
        );

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterionMock */
        $criterionMock = $this
            ->getMockBuilder( "eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion" )
            ->disableOriginalConstructor()
            ->getMock();
        $query = new LocationQuery( array( "filter" => $criterionMock ) );

        $permissionsCriterionHandlerMock->expects( $this->once() )
            ->method( "addPermissionsCriterion" )
            ->with( $criterionMock )
            ->will( $this->throwException( new Exception( "Handler threw an exception" ) ) );

        $service->findLocations( $query, true );
    }

    /**
     * Test for the findLocations() method.
     */

    /**
     * Test for the findContent() method.
     */
    public function testFindLocationsWithDefaultQueryValues()
    {
        $repositoryMock = $this->getRepositoryMock();
        /** @var \eZ\Publish\SPI\Search\Handler $searchHandlerMock */
        $searchHandlerMock = $this->getSPIMockHandler( 'Search\\Handler' );
        /** @var \eZ\Publish\SPI\Search\Location\Handler $locationSearchHandlerMock */
        $locationSearchHandlerMock = $this->getSPIMockHandler( 'Search\\Location\\Handler' );
        $domainMapperMock = $this->getDomainMapperMock();
        $service = new SearchService(
            $repositoryMock,
            $searchHandlerMock,
            $locationSearchHandlerMock,
            $domainMapperMock,
            $this->getPermissionsCriterionHandlerMock(),
            array()
        );

        $spiLocation = new SPILocation;
        $locationMock = $this->getMockForAbstractClass( "eZ\\Publish\\API\\Repository\\Values\\Content\\Location" );
        $domainMapperMock->expects( $this->once() )
            ->method( "buildLocationDomainObject" )
            ->with( $this->equalTo( $spiLocation ) )
            ->will( $this->returnValue( $locationMock ) );

        /** @var \PHPUnit_Framework_MockObject_MockObject $locationSearchHandlerMock */
        $locationSearchHandlerMock
            ->expects( $this->once() )
            ->method( "findLocations" )
            ->with(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\MatchAll(),
                        "limit" => 1073741824
                    )
                )
            )
            ->will(
                $this->returnValue(
                    new SearchResult(
                        array(
                            "searchHits" => array( new SearchHit( array( "valueObject" => $spiLocation ) ) ),
                            "totalCount" => 1
                        )
                    )
                )
            );

        $result = $service->findLocations( new LocationQuery(), false );

        $this->assertEquals(
            new SearchResult(
                array(
                    "searchHits" => array( new SearchHit( array( "valueObject" => $locationMock ) ) ),
                    "totalCount" => 1
                )
            ),
            $result
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\Core\Repository\DomainMapper
     */
    protected function getDomainMapperMock()
    {
        if ( !isset( $this->domainMapperMock ) )
        {
            $this->domainMapperMock = $this
                ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\DomainMapper" )
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->domainMapperMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\Core\Repository\PermissionsCriterionHandler
     */
    protected function getPermissionsCriterionHandlerMock()
    {
        if ( !isset( $this->permissionsCriterionHandlerMock ) )
        {
            $this->permissionsCriterionHandlerMock = $this
                ->getMockBuilder( "eZ\\Publish\\Core\\Repository\\PermissionsCriterionHandler" )
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->permissionsCriterionHandlerMock;
    }

    /**
     * Returns the content service to test with $methods mocked
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     *
     * @return \eZ\Publish\Core\Repository\SearchService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPartlyMockedSearchService( array $methods = array() )
    {
        return $this->getMock(
            "eZ\\Publish\\Core\\Repository\\SearchService",
            $methods,
            array(
                $this->getRepositoryMock(),
                $this->getPersistenceMock()->searchHandler(),
                $this->getDomainMapperMock(),
                $this->getPermissionsCriterionHandlerMock(),
                array()
            )
        );
    }
}
