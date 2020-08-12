<?php


namespace App\GraphQL\Resolver;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class UserResolver implements ResolverInterface, AliasedInterface
{
    /**
     * @var EntityManager $em
     */
    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function resolve(Argument $argument)
    {
        return $this->em->getRepository(User::class)->find($argument["id"]);
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'User'
        ];
    }
}