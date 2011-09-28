<?php
/**
 * File containing the User mapper
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\Persistence\Storage\Legacy\User;
use ezp\Persistence\User,
    ezp\Persistence\User\Role,
    ezp\Persistence\User\RoleUpdateStruct,
    ezp\Persistence\User\Policy,
    \RuntimeException;

/**
 * mapper for User realted objects
 *
 */
class Mapper
{
    /**
     * Map role data to a role
     *
     * @param array $data
     * @return \ezp\Persistence\User\Role
     */
    public function mapRole( array $data )
    {
        $role = new Role();

        foreach ( $data as $row )
        {
            if ( empty( $role->id ) )
            {
                $role->id   = $row['ezrole_id'];
                $role->name = $row['ezrole_name'];
            }

            $role->groupIds[] = $row['ezuser_role_contentobject_id'];

            $policyId = $row['ezpolicy_id'];
            if ( !isset( $role->policies[$policyId] ) &&
                 ( $policyId !== null ) )
            {
                $role->policies[$policyId] = new Policy( array(
                    'id'       => $row['ezpolicy_id'],
                    'roleId'   => $row['ezrole_id'],
                    'module'   => $row['ezpolicy_module_name'],
                    'function' => $row['ezpolicy_function_name'],
                ) );
            }

            // @TODO: Handle policy limitations
        }

        $role->groupIds = array_unique( array_filter( $role->groupIds ) );
        $role->policies = array_values( $role->policies );

        return $role;
    }
}
?>
