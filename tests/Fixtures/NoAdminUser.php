<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

class NoAdminUser
{
    public function isAdmin()
    {
        return false;
    }
}
