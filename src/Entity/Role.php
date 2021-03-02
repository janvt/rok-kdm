<?php


namespace App\Entity;


class Role
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_KINGDOM_MEMBER = 'ROLE_KINGDOM_MEMBER';

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const ROLE_OFFICER = 'ROLE_OFFICER';

    const ROLE_SCRIBE = 'ROLE_SCRIBE';
    const ROLE_SCRIBE_ADMIN = 'ROLE_SCRIBE_ADMIN';

    const ROLE_EDIT_ROLES = 'ROLE_EDIT_ROLES';

    const ALL = [
        self::ROLE_USER,
        self::ROLE_KINGDOM_MEMBER,
        self::ROLE_ADMIN,
        self::ROLE_SUPERADMIN,
        self::ROLE_OFFICER,
        self::ROLE_SCRIBE,
        self::ROLE_SCRIBE_ADMIN,
        self::ROLE_EDIT_ROLES,
    ];
}