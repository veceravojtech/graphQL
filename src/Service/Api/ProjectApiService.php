<?php


namespace App\Service\Api;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;


class ProjectApiService
{
    /**
     * @var EntityManager
     */
    private $em;
    private $passwordEncoder;
    private $jwtTokenManager;
    private $successHandler;
    private  $security;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManager $em, JWTTokenManagerInterface $jwtTokenManager, AuthenticationSuccessHandler $successHandler, Security $security)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->successHandler = $successHandler;
        $this->security = $security;
    }

    public function projectCreate(Argument $argument)
    {
        $input = $argument->offsetGet('input');
        $user = $this->security->getUser();
        if($user){
            $user = $this->em->getRepository(User::class)->find($user->getId());
        }else{
            throw new \Exception('Not logged!');
        }

        $project = new Project();
        $project->setName($input["name"]);
        $project->setDescription($input["description"]);
        $project->setUser($user);

        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }

    public function projectUpdate(Argument $argument)
    {
        $input = $argument->offsetGet('input');
        $project = $this->getProject($input);

        $project->setName($input["name"]);
        $project->setDescription($input["description"]);

        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }

    public function projectDelete(Argument $argument)
    {
        $input = $argument->offsetGet('input');

        $project = $this->getProject($input);

        $this->em->remove($project);
        $this->em->flush();

        return $project;
    }

    public function getProject($input)
    {
        $user = $this->security->getUser();
        if($user){
            $user = $this->em->getRepository(User::class)->find($user->getId());
        }else{
            throw new \Exception('Not logged!');
        }

        $project = $this->em->find(Project::class, $input["id"]);

        if(!$project || $project->getUser() !== $user){
            throw new \Exception('Project not found');
        }

        return $project;
    }


}