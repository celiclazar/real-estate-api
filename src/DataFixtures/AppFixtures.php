<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Property;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 1000; $i++) {
            $property = new Property();
            $property->setTitle('Property ' . $i);
            $property->setDescription('Description for Property ' . $i);
            $property->setPrice(rand(1200, 140000));
            $property->setLocation('Location' . $i);
            $property->setSize(rand(1, 600));
            $imageUrls = $this->generateImageUrls($i, 3);
            $property->setImages($imageUrls);
            $property->setAgentId(rand(1, 57));

            // Use Faker to generate random createdAt and updatedAt timestamps
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            $property->setCreatedAt($createdAt->format("Y-m-d H:i:s"));
            $property->setUpdatedAt($updatedAt->format("Y-m-d H:i:s"));
            // Add more properties as needed...

            $manager->persist($property);
        }

        $manager->flush();
    }

    private function generateImageUrls($propertyIndex, $numUrls): array
    {
        $imageUrls = [];
        for ($j = 1; $j <= $numUrls; $j++) {
            $imageUrl = sprintf('https://example.com/property_%d_image_%d.jpg', $propertyIndex, $j);
            $imageUrls[] = $imageUrl;
        }

        return $imageUrls;
    }
}
