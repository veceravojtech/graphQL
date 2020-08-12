<?php


namespace App\GraphQL\Mutation;


use App\Entity\User;
use App\Service\Api\ProjectApiService;
use App\Service\Api\UserApiService;
use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface as AliasedInterfaceAlias;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class ProjectUpdateMutation implements MutationInterface, AliasedInterfaceAlias
{
    /**
     * @var EntityManager $em
     */
    private $em;
    /**
     * @var ProjectApiService $projectApiService
     */
    private $projectApiService;
    public function __construct(EntityManager $em, ProjectApiService $projectApiService)
    {
        $this->em = $em;
        $this->projectApiService = $projectApiService;
    }

    public function projectUpdate(Argument $argument)
    {
        return $this->projectApiService->projectUpdate($argument);
    }

    public static function getAliases(): array
    {
        return [
            'projectUpdate' => 'projectUpdate'
        ];
    }

}