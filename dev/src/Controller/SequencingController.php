<?php 

namespace App\Controller;

use App\Entity\MethodSequencing;
use App\Form\SequencingFormType;
use App\Repository\MethodSequencingRepository;
use App\Repository\MethodSequencingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SequencingController extends AbstractController
{
    public function __construct(
        #[Autowire(service: MethodSequencingRepository::class)]
        private MethodSequencingRepositoryInterface $sequencingRepository,
    ) {
    }

}