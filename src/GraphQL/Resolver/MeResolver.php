<?php


namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\Security\Core\Security;

class MeResolver implements ResolverInterface, AliasedInterface
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function resolve()
    {
        return $this->security->getUser();
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Me'
        ];
    }
}