<?php

namespace App\Controller;

use App\Repository\AdminCommentaireRepository;
use App\Repository\AdminRepository;
use App\Service\ManualReviewsService;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        AdminRepository $repo,
        ManualReviewsService $svc,
        AdminCommentaireRepository $commentRepo,
        TagAwareCacheInterface $cache
    ): Response
    {

        $admin = $repo->findSingletonWithServicesAndPhotos();

        $services = $cache->get('home.services.v1', function (ItemInterface $item) use ($admin) {
            $item->tag(['home', 'home.services']);  // tags pour invalider
            // Pas d'expiration => "permanent" (sous réserve d'éviction du store)
            if (!$admin) {
                return []; // pas d'admin => pas de services
            }

            // Normalisation vers scalaires pour Twig (évite de stocker des entités)
            $list = [];
            foreach ($admin->getServices() as $svc) {
                $photos = [];
                foreach ($svc->getPhotoServices() as $p) {
                    $photos[] = [
                        'source'      => $p->getSource(),       // ex: foo-car-md.webp
                        'description' => $p->getDescription(),  // texte
                    ];
                }
                $list[] = [
                    'id'            => $svc->getId(),
                    'libelle'       => $svc->getLibelle(),
                    'photoServices' => $photos,
                ];
            }
            return $list;
        });



         $admin = $cache->get('home.admin', function (ItemInterface $item) use ($repo) {
            $item->tag(['home', 'home.admin']);
            return $repo->findOneBy(['code' => 'singleton']);
        });

        $stats = $cache->get('home.stats', function (ItemInterface $item) use ($svc, $commentRepo) {
            $item->tag(['home', 'home.stats']);
            $comments = $commentRepo->findAll();
            if (!$comments) {
                return $svc->getStats();
            }

            $count = count($comments);
            $avg = $count ? array_sum(array_map(static fn($c) => (int) $c->getRating(), $comments)) / $count : 0;
            return ['average' => $avg, 'count' => $count];
        });

        $reviews = $cache->get('home.reviews.9', function (ItemInterface $item) use ($svc, $commentRepo) {
            $item->tag(['home', 'home.reviews']);
            $comments = $commentRepo->findAll();
            if (!$comments) {
                return $svc->getRandomized(9);
            }

            $list = [];
            foreach ($comments as $comment) {
                $list[] = [
                    'author'  => $comment->getAuthor() ?? 'Client',
                    'source'  => $comment->getSource()?->getLabel() ?? 'autre',
                    'rating'  => $comment->getRating() ?? 5,
                    'age'     => $comment->getDate() ?? null,
                    'text'    => $comment->getText() ?? '',
                    'label'   => null,
                    'visited' => null,
                    'url'     => null,
                ];
            }

            shuffle($list);
            return array_slice($list, 0, 9);
        });

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
                'reviews' => $reviews,
                'stats'   => $stats,
                'admin'   => $admin,
                'services'            => $services, // <- on passe la liste normalisée
                'upload_url_services' => 'uploads/services', // utilisé par ton partial
        ]);
    }
}
