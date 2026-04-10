<?php

namespace App\DataFixtures;

use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Entity\CommandeLocation;
use App\Entity\LigneCommandeLocation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** =========================
         *  TypeLocation
         *  ========================= */
        for ($i = 1; $i <= 25; $i++) {
            $type = new TypeLocation();
            $type->setLibelle('Robe ' . $i);

            $manager->persist($type);
            $this->addReference('type_' . $i, $type);
        }

        /** =========================
         *  Entretien
         *  ========================= */
        for ($i = 1; $i <= 25; $i++) {
            $entretien = new Entretien();
            $entretien->setLavage('Ne pas laver ' . $i);
            $entretien->setRepassage('Ne pas repasser ' . $i);
            $entretien->setNettoyageSec('Nettoyage à sec autorisé ' . $i);

            $manager->persist($entretien);
            $this->addReference('entretien_' . $i, $entretien);
        }

        /** =========================
         *  Couleur
         *  ========================= */
        for ($i = 1; $i <= 25; $i++) {
            $couleur = new Couleur();
            $couleur->setNomCouleur('Rouge ' . $i);
            $couleur->setImage(
                'https://myfabricplace.co.uk/cdn/shop/products/IMG_0735.jpg'
            );

            $manager->persist($couleur);
            $this->addReference('couleur_' . $i, $couleur);
        }

        /** =========================
         *  ModeleLocation
         *  ========================= */
        for ($i = 1; $i <= 25; $i++) {
            $modele = new ModeleLocation();
            $modele->setLibelle('Robe empire ' . $i);
            $modele->setPrixLocation((string) random_int(50, 1000));
            $modele->setLongueur('midi');
            $modele->setLongueurManches('Manches longues');
            $modele->setTissu('Coton');
            $modele->setSilhouette('ample');
            $modele->setFormeCol('col rond');

            $modele->setEntretien(
                $this->getReference('entretien_' . random_int(1, 25), Entretien::class)
            );

            $modele->setType(
                $this->getReference('type_' . random_int(1, 25), TypeLocation::class)
            );

            $manager->persist($modele);
            $this->addReference('modele_' . $i, $modele);
        }

        /** =========================
         *  StockLocation
         *  ========================= */
        $tailles = ['XS', 'S', 'M', 'L', 'XL'];

        for ($i = 1; $i <= 50; $i++) {
            $stock = new StockLocation();
            $stock->setTaille($tailles[array_rand($tailles)]);
            $stock->setQuantite(random_int(1, 20));
            $stock->setImageVariante(
                'https://myfabricplace.co.uk/cdn/shop/products/IMG_0734.jpg'
            );

            $stock->setCouleur(
                $this->getReference('couleur_' . random_int(1, 25), Couleur::class)
            );

            $stock->setModeleLocation(
                $this->getReference('modele_' . random_int(1, 25), ModeleLocation::class)
            );

            $manager->persist($stock);
            $this->addReference('stock_' . $i, $stock);
        }

        /** =========================
         *  CommandeLocation
         *  ========================= */
        $statuts = ['en_attente', 'payee', 'en_cours', 'terminee', 'annulee'];

        for ($i = 1; $i <= 20; $i++) {
            $commande = new CommandeLocation();

            $dateCommande = new \DateTimeImmutable('-' . random_int(0, 30) . ' days');
            $dateDebut = $dateCommande->modify('+' . random_int(1, 5) . ' days');
            $dateFin = random_int(0, 1)
                ? $dateDebut->modify('+' . random_int(3, 10) . ' days')
                : null;

            $commande->setDateCommande($dateCommande);
            $commande->setDateDebutLoc($dateDebut);
            $commande->setDateFinLoc($dateFin);
            $commande->setLocation(true);
            $commande->setStatut($statuts[array_rand($statuts)]);
            $commande->setMontantTotal((string) random_int(100, 2000));

            $manager->persist($commande);
            $this->addReference('commande_' . $i, $commande);
        }

        /** =========================
         *  LigneCommandeLocation
         *  ========================= */
        for ($i = 1; $i <= 40; $i++) {
            $ligne = new LigneCommandeLocation();

            $ligne->setIdCommandeloc(
                $this->getReference(
                    'commande_' . random_int(1, 20),
                    CommandeLocation::class
                )
            );

            $ligne->setIdStockloc(
                $this->getReference(
                    'stock_' . random_int(1, 50),
                    StockLocation::class
                )
            );

            $ligne->setQuantite(random_int(1, 3));
            $ligne->setPrix((string) random_int(50, 500));

            $manager->persist($ligne);
        }

        /** =========================
         *  Flush
         *  ========================= */
        $manager->flush();
    }
}
