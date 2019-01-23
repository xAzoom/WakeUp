<?php

namespace App\Controller\Api;

use App\Entity\Account;
use App\Exception\InvalidDataException;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $account = new Account();
        $data = json_decode($request->getContent(), true);
        if($data === null) {
            throw new InvalidDataException(400, 'Invalid json message received');
        }

        $form = $this->createForm(RegisterUserType::class, $account);
        $form->submit($data);

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $account->setPassword($passwordEncoder->encodePassword($account, $account->getPlainPassword()));
        $account->eraseCredentials();
        $entityManager->persist($account);
        $entityManager->flush();

        return $this->view($account, Response::HTTP_CREATED);
    }
}
