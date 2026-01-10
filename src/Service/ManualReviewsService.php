<?php

namespace App\Service;

final class ManualReviewsService
{
    /** Tous les avis (normalisés) */
    public function getAll(): array
    {
        $base = [
            'label'   => null,
            'age'     => null,
            'visited' => null,
            'url'     => null, // lien de vérification (target=_blank)
        ];

        $gUrl  = 'https://www.google.com/search?sca_esv=637c0e90f834ed3d&si=AMgyJEtREmoPL4P1I5IDCfuA8gybfVI2d5Uj7QMwYCZHKDZ-E2wcxlemEfb8Kv-ek8oqBKXgr8nbPaCIa6Pd-Uh8ILfp6MZME1IrmulSeMq7Zwy2NA7o-Z50WukenIg3WZfpXTit2KfQ&q=azur+66+plomberie+Avis&sa=X&ved=2ahUKEwiXnJPwvO6PAxV_UaQEHcpINh4Q0bkNegQIOxAE&biw=1912&bih=924&dpr=1'; // 👉 remplace
        $pjUrl = 'https://www.pagesjaunes.fr/pros/59248825'; // 👉 remplace
        $maUrl = 'https://www.meilleur-artisan.com/profil/yourProfile#avis'; // 👉 remplace

        $rows = [
            // GOOGLE
            ['author' => 'Ilyes MAKHLOUFI', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 semaines', 'label' => 'Nouveau', 'visited' => 'Visité en septembre', 'text' => "Sébastien est intervenu ce week end dans la maison de notre papi, il a fait preuve d’un grand professionnalisme et de très bons conseils. Intervention au top, encore merci :)", 'url' => $gUrl],
            ['author' => 'Sylvie Provou', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 semaines', 'label' => 'Nouveau', 'visited' => 'Visité en septembre', 'text' => "Travail sérieux, réactivité, flexibilité, et ponctualité. Les échanges avec Mr. Acker ont été très agréables et instructifs. Je recommande vivement.", 'url' => $gUrl],
            ['author' => 'Poujol Marie-Jeanne', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 3 mois', 'visited' => 'Visité en juin', 'text' => "Artisan plombier compétent et sérieux. Délai et rendez-vous tenus. Travail soigné. Prix raisonnable. A recommander", 'url' => $gUrl],
            ['author' => 'Roger Palicot', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 5 mois', 'visited' => 'Visité en avril', 'text' => "Une chaudière en panne et plusieurs devis plus tard, il faut se rendre à l'évidence : Monsieur ACKER a tout de l'artisan proche des gens, à l'écoute, d'une…Plus", 'url' => $gUrl],
            ['author' => 'Franck Jordane', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 8 mois', 'visited' => 'Visité en décembre 2024', 'text' => "J'ai eu l'occasion sur deux sites distincts et pour deux devis de travailler avec Monsieur Sébastien ACKER et je le recommande vivement. Il répond rapidement à…Plus", 'url' => $gUrl],
            ['author' => 'Laura Labourier', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 mois', 'visited' => 'Visité en juillet', 'text' => "Personne sérieuse, très agréable à prix raisonnable je recommande", 'url' => $gUrl],
            ['author' => 'Franck Baillette', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un mois', 'visited' => 'Visité en août', 'text' => "Intervention rapide super professionnel, je recommande!", 'url' => $gUrl],
            ['author' => 'LEBLANC Anaïs', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 5 jours', 'label' => 'Nouveau', 'visited' => 'Visité en septembre', 'text' => "Plombier très fiable et réactif !", 'url' => $gUrl],
            ['author' => 'Odile Claude', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en septembre 2024', 'text' => "Très satisfaite de la prestation de cet artisan très sympathique, à l’écoute et de bons conseils. Dépannage immédiat, travail soigné et rapide. Je recommande !", 'url' => $gUrl],
            ['author' => 'SUBIRATS SYLVIE', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 8 mois', 'visited' => 'Visité en janvier', 'text' => "Plombier très sérieux et efficace ajouté à cela une gentillesse naturelle. C’est un excellent professionnel et c’est plutôt rare de nos jours. Je recommande fortement.", 'url' => $gUrl],
            ['author' => 'Crazy Blues66', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en juin 2024', 'text' => "Très satisfait de l'intervention de Sébastien. Très réactif et fait son maximum pour que le travail soit réalisé rapidement en cas d'urgence. Sympathique, efficace et travail de qualité.", 'url' => $gUrl],
            ['author' => 'S CLÉMENT', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en février 2024', 'text' => "L'évacuation de la maison de mon père âgé était bouchée… Un seul plombier comprend notre désarroi, et intervient rapidement. Après…Plus", 'url' => $gUrl],
            ['author' => 'Jacques', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 10 mois', 'visited' => 'Visité en novembre 2024', 'text' => "Monsieur très sympathique et très compétent. De plus, très réactif en venant dès le lendemain !!!!…Plus", 'url' => $gUrl],
            ['author' => 'moi 89 le marin', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en août 2024', 'text' => "Sébastien est un vrai professionnel, charmant, toujours à l'heure, vous pouvez compter sur lui les yeux fermés, je le recommande.", 'url' => $gUrl],
            ['author' => 'Monique Thibault', 'source' => 'google', 'rating' => 4, 'age' => 'il y a 9 mois', 'visited' => 'Visité en décembre 2024', 'text' => "je te mets un petit 4 à cause des rayures sur le ballon et la plinthe un peu abîmée mais très bon boulot, nous referons appel à toi. merci", 'url' => $gUrl],
            ['author' => 'laetitia Francois', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 4 ans', 'visited' => 'Visité en octobre 2020', 'text' => "Personne très agréable, honnête et professionnel. Travail impeccable, rapide. Pas là pour arnaquer mais pour aider à réaliser…Plus", 'url' => $gUrl],
            ['author' => 'Lydie Medal', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 7 mois', 'visited' => 'Visité en janvier', 'text' => "Changement et pose de chauffe eau. Très sérieux, réactif, ponctuel, travaille proprement. Je recommande vivement", 'url' => $gUrl],
            ['author' => 'Lydie Pomarole', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en mars 2024', 'text' => "Très professionnel, très compétent, sympathique et très serviable. Intervention rapide et chantier propre. Je recommande vivement.", 'url' => $gUrl],
            ['author' => 'Ophélie santias', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Parfait 👍 travail plus que bien fait, très propre. Je recommande cet artisan.", 'url' => $gUrl],
            ['author' => 'Marie jeanne Rodriguez', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Super plombier. Rapide et efficace. Je suis très contente. Merci Sébastien", 'url' => $gUrl],
            ['author' => 'justine rodriguez', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en septembre 2023', 'text' => "Super efficacité, agréable et sympathique. Intervient rapidement et sérieux, je recommande vivement", 'url' => $gUrl],
            ['author' => 'Robin Defert', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 3 ans', 'visited' => 'Visité en février 2022', 'text' => "Rénovation de salle de bain parfaitement réalisée ! Professionnel, honnête et à l'écoute. Je recommande vivement !…Plus", 'url' => $gUrl],
            ['author' => 'Kevin', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 5 ans', 'visited' => 'Visité en septembre 2019', 'text' => "Artisan sérieux, ponctuel, travail soigné et prend le temps d’expliquer ! Je recommande !…Plus", 'url' => $gUrl],
            ['author' => 'Chantal Uzan', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Très bon artisan, très professionnel et aimable", 'url' => $gUrl],
            ['author' => 'André Gieler', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en juillet 2024', 'text' => "Plombier compétent, sympathique, rapidité d’exécution. Je recommande absolument.", 'url' => $gUrl],
            ['author' => 'Mathilde Got', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Artisan disponible, ponctuel et efficace. Merci pour votre intervention, vos explications et vos conseils", 'url' => $gUrl],
            ['author' => 'norbert guedj', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 5 ans', 'visited' => 'Visité en mai 2020', 'text' => "Travaux d'évacuation et receveur de douche refaits. Efficace et bon rapport qualité prix. Je recommande.", 'url' => $gUrl],
            ['author' => 'isabelle J. LECELLIER', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Très réactif, disponible et impliqué. Je recommande", 'url' => $gUrl],
            ['author' => 'Marie-Pierre Chatanay', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 4 ans', 'visited' => 'Visité en novembre 2020', 'text' => "Je recommande Sébastien sans hésitation ! honnête, professionnel, très sympathique…Plus", 'url' => $gUrl],
            ['author' => 'Ninos Shannah', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 3 ans', 'visited' => 'Visité en avril 2022', 'text' => "Au top, très pro, super gentil et abordable. Je conseille à 100% 👌🏻👍🏻…Plus", 'url' => $gUrl],
            ['author' => 'Alice Kinghoof', 'source' => 'google', 'rating' => 5, 'age' => 'Modifié il y a 4 ans', 'visited' => 'Visité en septembre 2021', 'text' => "Plombier efficace et très sérieux. Intervention rapide et efficace. Je recommande vraiment.…Plus", 'url' => $gUrl],
            ['author' => 'Pascal CANTOS', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 4 ans', 'visited' => 'Visité en novembre 2020', 'text' => "Entreprise honnête et propre, je n'hésiterai pas à rappeler si besoin : je recommande…Plus", 'url' => $gUrl],
            ['author' => 'valerie gaude', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 ans', 'visited' => 'Visité en novembre 2022', 'text' => "Très bon travail et sympathique, je recommande…Plus", 'url' => $gUrl],
            ['author' => 'Jean GOMEZ', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 3 ans', 'visited' => 'Visité en juillet 2020', 'text' => "Très pro, de bon conseil et tarifs très raisonnables…Plus", 'url' => $gUrl],
            ['author' => 'Myriam Aubernon', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 ans', 'visited' => 'Visité en janvier 2023', 'text' => "Un contact rapide, agréable et efficace.…Plus", 'url' => $gUrl],
            ['author' => 'Marie MONGET-GALIANO', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 ans', 'visited' => 'Visité en mars 2023', 'text' => "Réactif et pro. Je recommande !!", 'url' => $gUrl],
            ['author' => 'tony rougemont_cremades', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en septembre 2024', 'text' => "Plombier très pro", 'url' => $gUrl],
            ['author' => 'Axel Tessier', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 5 ans', 'visited' => 'Visité en septembre 2020', 'text' => "Superbe plombier !…Plus", 'url' => $gUrl],
            ['author' => 'Gérard Avedillo', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 11 mois', 'visited' => 'Visité en octobre 2024', 'text' => "Avedillo", 'url' => $gUrl],
            ['author' => 'Raymonde Rey', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un an', 'visited' => 'Visité en octobre 2023', 'text' => "", 'url' => $gUrl],
            ['author' => 'Sébastien Poussain', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 2 ans', 'visited' => 'Visité en août 2023', 'text' => "", 'url' => $gUrl],
            ['author' => 'Raphael De La Cruz', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 3 ans', 'visited' => 'Visité en février 2022', 'text' => "", 'url' => $gUrl],
            ['author' => 'Parra Evane', 'source' => 'google', 'rating' => 5, 'age' => 'il y a 4 ans', 'visited' => 'Visité en octobre 2020', 'text' => "", 'url' => $gUrl],
            ['author' => 'Mehdi JK', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un mois', 'visited' => 'Visité en août', 'text' => "", 'url' => $gUrl],
            ['author' => 'Youtube Konix', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un mois', 'visited' => 'Visité en août', 'text' => "", 'url' => $gUrl],
            ['author' => 'Henri Gil', 'source' => 'google', 'rating' => 5, 'age' => 'il y a un mois', 'visited' => 'Visité en juillet', 'text' => "", 'url' => $gUrl],
            ['author' => 'Sylvie Acker', 'source' => 'google', 'rating' => 5, 'age' => 'Modifié il y a 5 ans', 'visited' => 'Visité en juin 2019', 'text' => "", 'url' => $gUrl],

            // PAGES JAUNES (compléments)
            [
                'author'  => 'gilbertgil3',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 5 octobre 2021',
                'visited' => "Expérience vécue le 5 octobre 2021",
                'text'    => "Installation douche à l'italienne après dépose de baignoire et bidet. Travail efficace, et propre. Personne sympathique, à l'écoute. Qualité/prix : Très bon. A recommander sans hésiter.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'norbertg31',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 7 octobre 2020',
                'visited' => "Expérience vécue le 7 octobre 2020",
                'text'    => "Bonjour Madame Monsieur J'ai fait appel à l'entreprise Azur 66 Plomberie suite à des travaux dans ma salle d'eau et je suis très satisfait du suivi, du sérieux et de la rapidité d'intervention de l'entreprise. Je la recommande.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'thierry03',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 13 juin 2020',
                'visited' => "Expérience vécue le 9 juin 2020",
                'text'    => "Plombier très sérieux, efficace. Dépannage rapide. Personne sympathique qui m’a bien expliqué d’où venait la panne. Je recommande fortement.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'norbertg31',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 14 mai 2020',
                'visited' => "Expérience vécue le 14 mai 2020",
                'text'    => "Azur 66 a travaillé pour moi le 6 et 7 mai 2020 : installation d'évacuation refaite et remplacement d’un receveur de douche. Bon rapport qualité/prix, sérieux et à l'écoute. Efficace, je le recommande (Norbert GUEDJ).",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'nico66',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 6 mai 2020',
                'visited' => "Expérience vécue le 2 mai 2020",
                'text'    => "Bonne entreprise, sérieuse et sympa. Bon travail, à l’heure et professionnel. Merci pour votre professionnalisme.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'justinerodriguez',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 19 février 2020',
                'visited' => "Expérience vécue le 18 février 2020",
                'text'    => "Plombier sérieux, efficace et pas cher. Je recommande.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'rivasgaby',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 24 octobre 2019',
                'visited' => "Expérience vécue le 24 octobre 2019",
                'text'    => "Plombier très aimable, intervenu très rapidement (moins d'une heure) et a réparé le chauffe-eau en expliquant la cause et la remise en marche.",
                'url'     => $pjUrl,
            ],
            [
                'author'  => 'CristeleDaSilva',
                'source'  => 'pagesjaunes',
                'rating'  => 5,
                'age'     => 'Le 11 juillet 2019',
                'visited' => "Expérience vécue le 3 juillet 2019",
                'text'    => "Est intervenu dans mon appartement rapidement. Très bon travail. Je recommande.",
                'url'     => $pjUrl,
            ],

            // MEILLEUR-ARTISAN
            ['author' => 'M (Meilleur-Artisan.com)', 'source' => 'meilleur-artisan', 'rating' => 5, 'age' => 'il y a 2 mois', 'text' => "La prestation est impeccable malgré un prix un peu élevé.", 'url' => $maUrl],
            ['author' => 'M (Meilleur-Artisan.com)', 'source' => 'meilleur-artisan', 'rating' => 4, 'age' => 'il y a 6 ans', 'text' => "Rapide efficace parfait", 'url' => $maUrl],
            ['author' => 'M (Meilleur-Artisan.com)', 'source' => 'meilleur-artisan', 'rating' => 5, 'age' => 'il y a 5 ans', 'text' => "Excellent travail", 'url' => $maUrl],
        ];

        return array_map(fn($r) => $r + $base, $rows);
    }

    /** Mélange biaisé (2 Google, 1 autre) + limite */
    public function getRandomized(int $limit = 12): array
    {
        $all = $this->getAll();
        $google = array_values(array_filter($all, fn($r) => $r['source'] === 'google'));
        $others = array_values(array_filter($all, fn($r) => $r['source'] !== 'google'));
        shuffle($google);
        shuffle($others);

        $merged = [];
        $g = 0;
        $o = 0;
        while ((isset($google[$g]) || isset($others[$o])) && count($merged) < $limit) {
            for ($i = 0; $i < 2 && isset($google[$g]) && count($merged) < $limit; $i++, $g++) $merged[] = $google[$g];
            if (isset($others[$o]) && count($merged) < $limit) $merged[] = $others[$o++];
        }
        while (count($merged) < $limit && isset($others[$o])) $merged[] = $others[$o++];
        return $merged;
    }

    /** Statistiques globales (moyenne et nombre) */
    public function getStats(): array
    {
        $all = $this->getAll();
        $count = count($all);
        $avg = $count ? array_sum(array_column($all, 'rating')) / $count : 0;
        return ['average' => $avg, 'count' => $count];
    }
}
