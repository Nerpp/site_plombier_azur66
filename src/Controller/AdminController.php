<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use App\Service\ImageOptimizerService;
use App\Service\ManualReviewsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    /**
     * Reconstruit les caches utilisés par la Home.
     * - Ne stocke que des SCALAIRES (arrays), pas d'entités Doctrine.
     * - Clés conformes PSR-6 (A-Z a-z 0-9 _ .)
     */
    private function warmHomeCache(
        TagAwareCacheInterface $cache,
        AdminRepository $repo,
        ManualReviewsService $reviewsSvc
    ): void {
        // --- Admin (scalaires) ---
        $cache->get('home.admin.v1', function (ItemInterface $item) use ($repo) {
            $item->tag(['home', 'home.admin']);

            $a = $repo->findOneBy(['code' => 'singleton']);
            if (!$a) {
                return null;
            }

            return [
                'srcProfil'  => $a->getSrcProfil(),
                'srcLogo'    => $a->getSrcLogo(),
                'sous_titre' => $a->getSousTitre(),
                'telephone'  => $a->getTelephone(),
                'email'      => $a->getEmail(),
                'adresse'    => $a->getAdresse(),
            ];
        });

        // --- Services normalisés (id, libelle, photoServices[{source, description}]) ---
        $cache->get('home.services.v1', function (ItemInterface $item) use ($repo) {
            $item->tag(['home', 'home.services']);

            $admin = method_exists($repo, 'findSingletonWithServicesAndPhotos')
                ? $repo->findSingletonWithServicesAndPhotos()
                : $repo->findOneBy(['code' => 'singleton']);

            if (!$admin) {
                return [];
            }

            $list = [];
            foreach ($admin->getServices() as $svc) {
                $photos = [];
                foreach ($svc->getPhotoServices() as $p) {
                    $photos[] = [
                        'source'      => $p->getSource(),
                        'description' => $p->getDescription(),
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

        // --- Stats ---
        $cache->get('home.stats.v1', function (ItemInterface $item) use ($reviewsSvc) {
            $item->tag(['home', 'home.stats']);
            return $reviewsSvc->getStats();
        });

        // --- Reviews (ex: 9 aléatoires) ---
        $cache->get('home.reviews.9.v1', function (ItemInterface $item) use ($reviewsSvc) {
            $item->tag(['home', 'home.reviews']);
            return $reviewsSvc->getRandomized(9);
        });
    }

    #[Route('', name: 'app_admin_settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request,
        EntityManagerInterface $em,
        AdminRepository $repo,
        SluggerInterface $slugger,
        ImageOptimizerService $imageOptimizer,
        TagAwareCacheInterface $cache,
        ManualReviewsService $reviewsSvc,
        #[Autowire(service: 'cache.app')] CacheItemPoolInterface $appCachePool,
        #[Autowire(param: 'upload_dir_profile')]  string $uploadDirProfile,
        #[Autowire(param: 'upload_dir_logo')]     string $uploadDirLogo,
        #[Autowire(param: 'upload_dir_services')] string $uploadDirServices,
        #[Autowire(param: 'upload_url_profile')]  string $uploadUrlProfile,
        #[Autowire(param: 'upload_url_logo')]     string $uploadUrlLogo,
        #[Autowire(param: 'upload_url_services')] string $uploadUrlServices,
    ): Response {
        $admin = $repo->findOneBy(['code' => 'singleton']) ?? new Admin();

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ========== suppressions demandées ==========
            if ($request->request->getBoolean('remove_image') && $admin->getSrcProfil()) {
                @unlink(rtrim($uploadDirProfile, '/') . '/' . $admin->getSrcProfil());
                $admin->setSrcProfil(null);
            }

            if ($request->request->getBoolean('remove_logo') && $admin->getSrcLogo()) {
                @unlink(rtrim($uploadDirLogo, '/') . '/' . $admin->getSrcLogo());
                $admin->setSrcLogo(null);
            }

            // ========== Upload profil ==========
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $upProfil */
            $upProfil = $form->get('src_profil')->getData();
            if ($upProfil) {
                $this->processUpload(
                    uploadedFile: $upProfil,
                    currentFilename: $admin->getSrcProfil(),
                    destDir: $uploadDirProfile,
                    slugger: $slugger,
                    imageOptimizer: $imageOptimizer,
                    preset: 'avatar_128',
                    onSaved: function (string $new) use ($admin) { $admin->setSrcProfil($new); },
                );
            }

            // ========== Upload logo ==========
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $upLogo */
            $upLogo = $form->get('logo')->getData();
            if ($upLogo) {
                $this->processUpload(
                    uploadedFile: $upLogo,
                    currentFilename: $admin->getSrcLogo(),
                    destDir: $uploadDirLogo,
                    slugger: $slugger,
                    imageOptimizer: $imageOptimizer,
                    preset: 'avatar_128',
                    onSaved: function (string $new) use ($admin) { $admin->setSrcLogo($new); },
                );
            }

            // =========================================================
            // SERVICES + PHOTOS (robuste) : on parcourt LES SOUS-FORMS
            // => garantit que chaque photo reste dans "son" service
            // =========================================================
            $hasError = false;

            /** @var FormInterface $servicesForm */
            $servicesForm = $form->get('services');

            foreach ($servicesForm as $serviceForm) {
                $service = $serviceForm->getData();
                if (!$service) {
                    continue;
                }

                if (null === $service->getAdminService()) {
                    $service->setAdminService($admin);
                }
                $em->persist($service);

                if (!$serviceForm->has('photoServices')) {
                    continue;
                }

                /** @var FormInterface $photosForm */
                $photosForm = $serviceForm->get('photoServices');

                foreach ($photosForm as $photoForm) {
                    $photo = $photoForm->getData();
                    if (!$photo) {
                        continue;
                    }

                    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $up */
                    $up = $photoForm->get('file')->getData();
                    $desc = method_exists($photo, 'getDescription') ? trim((string) $photo->getDescription()) : '';

                    // Fichier choisi => upload + set source
                    if ($up) {
                        $this->processUpload(
                            uploadedFile: $up,
                            currentFilename: $photo->getSource(),
                            destDir: $uploadDirServices,
                            slugger: $slugger,
                            imageOptimizer: $imageOptimizer,
                            preset: 'service_car_1280x720',
                            variantPreset: 'service_car_640x360',
                            variantSuffix: '640x360',
                            primarySuffix: '1280x720',
                            onSaved: function (string $new) use ($photo) { $photo->setSource($new); },
                        );
                    }

                    // Ni fichier ni source existante
                    if (!$up && !$photo->getSource()) {
                        if ($desc === '') {
                            // suppression "vide"
                            if (method_exists($service, 'removePhotoService')) {
                                $service->removePhotoService($photo);
                            }
                            // si pas orphanRemoval côté Doctrine, on force le remove
                            $em->remove($photo);
                            continue;
                        }

                        $photoForm->get('file')->addError(new FormError('Sélectionnez une image pour cette photo.'));
                        $hasError = true;
                        continue;
                    }

                    // Sécurité : si la photo était déjà liée ailleurs (cas ID dupliqué / mauvais mapping),
                    // on retire l'ancienne liaison avant de l'attacher au bon service.
                    if (method_exists($photo, 'getServices') && $photo->getServices() && $photo->getServices() !== $service) {
                        $old = $photo->getServices();
                        if ($old && method_exists($old, 'removePhotoService')) {
                            $old->removePhotoService($photo);
                        }
                    }

                    // Association photo → service (owning side)
                    if (method_exists($service, 'addPhotoService')) {
                        $service->addPhotoService($photo); // devrait setter le service côté photo
                    } else {
                        // fallback si tu as uniquement setServices côté photo
                        $photo->setServices($service);
                    }

                    $em->persist($photo);
                }
            }

            if ($hasError) {
                return $this->render('admin/settings.html.twig', [
                    'form'  => $form->createView(),
                    'admin' => $admin,
                ]);
            }

            $em->persist($admin);
            $em->flush();

            // ====== 1) CLEAR GLOBAL DU POOL 'cache.app' ======
            $appCachePool->clear();

            // ====== 2) WARM DES ENTRÉES UTILES À LA HOME ======
            $this->warmHomeCache($cache, $repo, $reviewsSvc);

            $this->addFlash('success', 'Paramètres enregistrés. Cache vidé et régénéré.');
            return $this->redirectToRoute('app_admin_settings');
        }

        return $this->render('admin/settings.html.twig', [
            'form'  => $form->createView(),
            'admin' => $admin,
        ]);
    }

    /**
     * @param callable(string $newFilename):void $onSaved
     */
    private function processUpload(
        \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile,
        ?string $currentFilename,
        string $destDir,
        SluggerInterface $slugger,
        ImageOptimizerService $imageOptimizer,
        string $preset,
        callable $onSaved,
        ?string $variantPreset = null,
        ?string $variantSuffix = null,
        ?string $primarySuffix = null,
    ): void {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = $uploadedFile->getMimeType();
        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException('Format non autorisé (serveur).');
        }

        if ($currentFilename) {
            @unlink(rtrim($destDir, '/') . '/' . $currentFilename);
            if ($primarySuffix && $variantSuffix) {
                $variantFilename = $this->swapImageSuffix($currentFilename, $primarySuffix, $variantSuffix);
                if ($variantFilename !== $currentFilename) {
                    @unlink(rtrim($destDir, '/') . '/' . $variantFilename);
                }
            }
        }

        if (!is_dir($destDir)) {
            @mkdir($destDir, 0775, true);
        }
        if (!is_writable($destDir)) {
            throw new \RuntimeException(sprintf('Dossier non inscriptible : %s', $destDir));
        }

        $original = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $slugger->slug($original)->lower();
        $ext = $uploadedFile->guessExtension() ?: 'bin';
        $baseName = sprintf('%s-%s', $safeName, uniqid('', true));
        $newFilename = $primarySuffix
            ? sprintf('%s-%s.%s', $baseName, $primarySuffix, $ext)
            : sprintf('%s.%s', $baseName, $ext);

        try {
            $uploadedFile->move($destDir, $newFilename);
        } catch (FileException $e) {
            throw new \RuntimeException('Erreur lors de l’envoi du fichier : ' . $e->getMessage());
        }

        $fullPath = rtrim($destDir, '/') . '/' . $newFilename;
        try {
            $imageOptimizer->optimizePreset($fullPath, $preset);
        } catch (\Throwable $e) {
            @unlink($fullPath);
            throw new \RuntimeException('Erreur lors du traitement de l’image : ' . $e->getMessage());
        }

        if ($variantPreset && $variantSuffix) {
            $variantFilename = sprintf('%s-%s.%s', $baseName, $variantSuffix, $ext);
            $variantPath = rtrim($destDir, '/') . '/' . $variantFilename;
            if (!@copy($fullPath, $variantPath)) {
                @unlink($fullPath);
                throw new \RuntimeException('Erreur lors de la duplication de l’image.');
            }
            try {
                $imageOptimizer->optimizePreset($variantPath, $variantPreset);
            } catch (\Throwable $e) {
                @unlink($variantPath);
                throw new \RuntimeException('Erreur lors du traitement de la variante : ' . $e->getMessage());
            }
        }

        $onSaved($newFilename);
    }

    private function swapImageSuffix(string $filename, string $from, string $to): string
    {
        $needle = '-' . $from . '.';
        if (!str_contains($filename, $needle)) {
            return $filename;
        }
        return str_replace($needle, '-' . $to . '.', $filename);
    }
}
