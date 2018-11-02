<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AccountRepository $accountRepository)
    {
        $accounts = $accountRepository->findAll();

        $usernames = [];

        foreach ($accounts as $account) {
            $usernames[] = $account->getUsername();
        }

        return $this->json([
            'users' => $usernames,
        ]);
    }
}
