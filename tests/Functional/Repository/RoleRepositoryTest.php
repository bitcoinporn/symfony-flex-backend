<?php
declare(strict_types=1);
/**
 * /tests/Functional/Repository/RoleRepositoryTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Tests\Functional\Repository;

use App\Repository\RoleRepository;
use App\Resource\RoleResource;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class RoleRepositoryTest
 *
 * @package Functional\Repository
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class RoleRepositoryTest extends KernelTestCase
{
    /**
     * @var RoleRepository;
     */
    private $repository;

    public static function tearDownAfterClass(): void
    {
        $application = new Application(static::$kernel);

        $command = new LoadDataFixturesDoctrineCommand();

        $application->add($command);

        $input = new ArrayInput([
            'command'           => 'doctrine:fixtures:load',
            '--no-interaction'  => true,
            '--fixtures'        => 'src/DataFixtures/',
        ]);

        $input->setInteractive(false);

        $command->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    }

    public function setUp(): void
    {
        parent::setUp();

        static::bootKernel();

        $this->repository = static::$kernel->getContainer()->get(RoleResource::class)->getRepository();
    }

    public function testThatResetMethodDeletesAllRecords(): void
    {
        self::assertSame(5, $this->repository->countAdvanced());
        self::assertSame(5, $this->repository->reset());
        self::assertSame(0, $this->repository->countAdvanced());
    }
}
