<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    #[Route('/cron/populate-elastica', name: 'cron_populate_elastica')]
    public function populateElastica(): Response
    {
        $process = new Process(['php', 'bin/console', 'fos:elastica:populate', '--quiet']);
        $process->setTimeout(3600);
        $process->start();

        return new Response('Population started', 200);
    }
}
