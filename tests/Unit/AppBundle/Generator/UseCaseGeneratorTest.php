<?php

namespace Tests\AppBundle\Unit\Generator;

use AppBundle\Entity\UserStory;
use AppBundle\Generator\UseCaseGenerator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UseCaseGeneratorTest extends TestCase
{
    /**
     * @return array
     */
    public function getUserStoryData(): array
    {
        return [
            [
                [
                    'Kaip vartotojas aš turiu galėti padaryti tą ir aną',
                    'Kaip vartotojas as turiu galeti paspausti mygtuka',
                    'Kaip administratorius as turiu galeti daryti viska',
                ],
                [
                    'vartotojas' => [
                        'padaryti ta ir ana',
                        'paspausti mygtuka'
                    ],
                    'administratorius' => ['daryti viska']
                ],
                0
            ],
            [
                [
                    'Kaip kazkas aš turiu galėti kažką',
                    'Kaip betkas as turiu galeti rašyti į įvesties langą',
                    'Netinkamai suformatuota vartotojo istorija',
                ],
                [
                    'kazkas' => ['kazka'],
                    'betkas' => ['rasyti i ivesties langa'],
                ],
                1
            ],
        ];
    }

    /**
     * Tests if use cases are generated correctly.
     *
     * @param array $userStories
     * @param array $expected
     * @param int   $invalidCount  Count of invalid user stories
     *
     * @dataProvider getUserStoryData
     */
    public function testGetUserStoryData(array $userStories, array $expected, int $invalidCount)
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $generator = new UseCaseGenerator($em);
        $userStories = $this->createUserStories($userStories);
        $useCases = $generator->generateUseCases($userStories);

        $this->assertEquals($expected, $useCases);

        $invalid = 0;

        /** @var UserStory $story */
        foreach ($userStories as $story) {
            $story->isValid() ?: $invalid++;
        }

        $this->assertEquals($invalidCount, $invalid);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function createUserStories($data)
    {
        $stories = [];

        foreach ($data as $item) {
            $story = new UserStory();
            $story->setTitle($item);
            $stories[] = $story;
        }

        return $stories;
    }
}
