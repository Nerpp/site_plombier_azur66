<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AdminFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['admin'];
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setCode('singleton');
        $admin->setSousTitre('Plomberie generale - Chauffe eau - Renovation');
        $admin->setTelephone('06 38 77 68 35');
        $admin->setEmail('azur66plomberie@gmail.com');
        $admin->setAdresse('13 impasse Joseph Sunyer');
        $admin->setVille('66140 Canet en Roussillon');
        $admin->setSuppInfo('Devis gratuit');
        $admin->setSlogan('Installation - Depannage - Renovation');
        $admin->setSrcProfil(null);
        $admin->setSrcLogo(null);

        $manager->persist($admin);
        $manager->flush();
    }
}
