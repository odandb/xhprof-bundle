<?php

declare(strict_types=1);

namespace Odandb\XhprofBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class TestController
{
    public function profile(): JsonResponse
    {
        return new JsonResponse();
    }
}
