<?php
/**
 * File containing the eZ\Publish\API\Repository\Values\User\Limitation\UserGroupLimitation class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository\Values\User\Limitation;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\User\Limitation\UserGroupLimitation as APIUserGroupLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation as APILimitationValue;
use eZ\Publish\SPI\Limitation\Type as SPILimitationTypeInterface;

/**
 * UserGroupLimitation is a Content Limitation
 */
class UserGroupLimitation implements SPILimitationTypeInterface
{
    /**
     * Create the Limitation Value
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitationValue
     * @param \eZ\Publish\API\Repository\Repository $repository
     *
     * @return bool
     */
    public function acceptValue( APILimitationValue $limitationValue, Repository $repository )
    {
        throw new \eZ\Publish\API\Repository\Exceptions\NotImplementedException( 'acceptValue' );
    }

    /**
     * Create the Limitation Value
     *
     * @param mixed[] $limitationValues
     *
     * @return \eZ\Publish\API\Repository\Values\User\Limitation
     */
    public function buildValue( array $limitationValues )
    {
        return new APIUserGroupLimitation( array( 'limitationValues' => $limitationValues ) );
    }

    /**
     * Evaluate permission against content and placement
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $value
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object
     * @param \eZ\Publish\API\Repository\Values\ValueObject $placement In 'create' limitations; this is parent
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     * @return bool
     */
    public function evaluate( APILimitationValue $value, Repository $repository, ValueObject $object, ValueObject $placement = null )
    {
        if ( !$value instanceof APIUserGroupLimitation )
            throw new InvalidArgumentException( '$value', 'Must be of type: APIUserGroupLimitation' );

        if ( $value->limitationValues[0] != 1 )
        {
            throw new BadStateException(
                'Parent User Group limitation',
                'expected limitation value to be 1 but got:' . $value->limitationValues[0]
            );
        }

        if ( !$object instanceof Content )
            throw new InvalidArgumentException( '$object', 'Must be of type: Content' );

         /**
          * @var \eZ\Publish\API\Repository\Values\Content\Content $object
          */
        $contentInfo = $object->contentInfo;
        $currentUser = $repository->getCurrentUser();
        if ( $contentInfo->ownerId === $currentUser->id )
            return true;

        $userService = $repository->getUserService();
        $contentOwner = $userService->loadUser( $contentInfo->ownerId );
        $contentOwnerGroups = $userService->loadUserGroupsOfUser( $contentOwner );
        $currentUserGroups = $userService->loadUserGroupsOfUser( $currentUser );

        foreach ( $contentOwnerGroups as $contentOwnerGroup )
        {
            foreach ( $currentUserGroups as $currentUserGroup )
            {
                if ( $contentOwnerGroup->id === $currentUserGroup->id )
                    return true;
            }
        }

        return false;
    }

    /**
     * Return Criterion for use in find() query
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $value
     * @param \eZ\Publish\API\Repository\Repository $repository
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface
     */
    public function getCriterion( APILimitationValue $value, Repository $repository )
    {
        throw new \eZ\Publish\API\Repository\Exceptions\NotImplementedException( 'getCriterion' );
    }

    /**
     * Return info on valid $limitationValues
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     *
     * @return mixed[]|int In case of array, a hash with key as valid limitations value and value as human readable name
     *                     of that option, in case of int on of VALUE_SCHEMA_ constants.
     */
    public function valueSchema( Repository $repository )
    {
        throw new \eZ\Publish\API\Repository\Exceptions\NotImplementedException( 'valueSchema' );
    }
}