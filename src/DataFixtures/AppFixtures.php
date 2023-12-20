<?php

namespace App\DataFixtures;

use App\Entity\Entry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $entries = [
            ["name" => "Kill Hitler", "resourceUrl" => "https://xkcd.com/1063/"],
            ["name" => "Time Machine", "resourceUrl" => "https://www.youtube.com/watch?v=8zwEnNJumQ4"],
        ];

        foreach ($entries as $entry) {
            $entryEntity = new Entry();
            $entryEntity->setName($entry["name"]);
            $entryEntity->setResourceUrl($entry["resourceUrl"]);
            $manager->persist($entryEntity);
        }

        $manager->flush();
    }
}
