<?php

namespace App\DataFixtures;

use App\Entity\AdminCommentaire;
use App\Entity\Source;
use App\Service\ManualReviewsService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AdminCommentaireFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['comments'];
    }

    public function load(ObjectManager $manager): void
    {
        $service = new ManualReviewsService();
        $rows = $service->getAll();

        $sources = [];
        foreach ($rows as $row) {
            $label = (string) ($row['source'] ?? 'autre');
            if (!isset($sources[$label])) {
                $source = new Source();
                $source->setLabel($label);
                $manager->persist($source);
                $sources[$label] = $source;
            }
        }

        foreach ($rows as $row) {
            $comment = new AdminCommentaire();
            $comment->setAuthor((string) ($row['author'] ?? 'Client'));
            $comment->setRating((int) ($row['rating'] ?? 5));
            $comment->setDate((string) ($row['age'] ?? ''));
            $comment->setText((string) ($row['text'] ?? ''));
            $label = (string) ($row['source'] ?? 'autre');
            $comment->setSource($sources[$label]);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
