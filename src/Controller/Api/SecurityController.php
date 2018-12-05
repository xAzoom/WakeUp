<?php

namespace App\Controller\Api;

use App\Entity\Account;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/register", name="api_register", methods={"POST", "GET"})
     */
    public function register(
        Request $request, ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $account = new Account();
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(RegisterUserType::class, $account);

        $form->submit($data);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors, 400);
        }

        $account->setPassword($passwordEncoder->encodePassword($account, $account->getPlainPassword()));
        $account->eraseCredentials();

        $entityManager->persist($account);
        $entityManager->flush();

        return new JsonResponse("", 201);
    }

    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
