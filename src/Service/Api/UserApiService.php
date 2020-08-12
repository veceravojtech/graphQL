<?php


namespace App\Service\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;


class UserApiService
{
    /**
     * @var EntityManager
     */
    private $em;
    private $passwordEncoder;
    private $jwtTokenManager;
    private $successHandler;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManager $em, JWTTokenManagerInterface $jwtTokenManager, AuthenticationSuccessHandler $successHandler)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->successHandler = $successHandler;
    }

    public function signup(Argument $argument)
    {
        $input = $argument->offsetGet('input');

        $user = new User();
        $user->setEmail($input["email"]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $input["password"]));

        $this->em->persist($user);
        $this->em->flush();

        $user->setToken($this->jwtTokenManager->create($user));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function login(Argument $argument)
    {
        $input = $argument->offsetGet('input');


        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $input["email"]]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }
        if($this->checkCredentials($input["password"], $user)){
            $user->setToken($this->jwtTokenManager->create($user));
            $this->em->persist($user);
            $this->em->flush();
            return $user;
        }

        throw new CustomUserMessageAuthenticationException('Bad password.');

    }

    public function checkCredentials($pw, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $pw);
    }


}