<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

class RoleHierarchyVoterTest extends RoleVoterTest
{
    /**
     * @dataProvider getVoteTests
     */
    public function testVote($roles, $attributes, $expected)
    {
        $voter = new RoleHierarchyVoter(new RoleHierarchy(array('ROLE_FOO' => array('ROLE_FOOBAR'))));

        $this->assertSame($expected, $voter->vote($this->getToken($roles), null, $attributes));
    }

    public function getVoteTests()
    {
        return array_merge(parent::getVoteTests(), array(
            array(array('ROLE_FOO'), array('ROLE_FOOBAR'), VoterInterface::ACCESS_GRANTED),
        ));
    }
}
