<?php
declare(strict_types=1);
/**
 * /tests/Integration/Form/Type/Console/UserTypeTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Tests\Integration\Form\Type\Console;

use App\DTO\User as UserDto;
use App\Entity\Role;
use App\Entity\UserGroup;
use App\Form\DataTransformer\UserGroupTransformer;
use App\Form\Type\Console\UserType;
use App\Resource\UserGroupResource;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class UserTypeTest
 *
 * @package App\Tests\Integration\Form\Type\Console
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserTypeTest extends TypeTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UserGroupResource
     */
    private $mockUserGroupResource;

    public function testSubmitValidData(): void
    {
        // Create new role entity for testing
        $roleEntity = new Role('ROLE_ADMIN');

        // Create new user group entity
        $userGroupEntity = new UserGroup();
        $userGroupEntity->setRole($roleEntity);

        $this->mockUserGroupResource
            ->expects(static::once())
            ->method('find')
            ->willReturn([$userGroupEntity]);

        $this->mockUserGroupResource
            ->expects(static::once())
            ->method('findOne')
            ->with($userGroupEntity->getId())
            ->willReturn($userGroupEntity);

        // Create form
        $form = $this->factory->create(UserType::class);

        // Create new DTO object
        $dto = new UserDto();
        $dto->setUsername('username');
        $dto->setFirstname('John');
        $dto->setSurname('Doe');
        $dto->setEmail('john.doe@test.com');
        $dto->setPassword('password');
        $dto->setUserGroups([$userGroupEntity]);

        // Specify used form data
        $formData = array(
            'username'      => 'username',
            'firstname'     => 'John',
            'surname'       => 'Doe',
            'email'         => 'john.doe@test.com',
            'password'      => [
                'password1' => 'password',
                'password2' => 'password',
            ],
            'userGroups'    => [$userGroupEntity->getId()],
        );

        // submit the data to the form directly
        $form->submit($formData);

        // Test that data transformers have not been failed
        $this->assertTrue($form->isSynchronized());

        // Test that form data matches with the DTO mapping
        $this->assertEquals($dto, $form->getData());

        // Check that form renders correctly
        $view = $form->createView();
        $children = $view->children;

        foreach (\array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function setUp(): void
    {
        $this->mockUserGroupResource = $this->createMock(UserGroupResource::class);

        parent::setUp();
    }

    /**
     * @return array
     */
    protected function getExtensions(): array
    {
        parent::getExtensions();

        // create a type instance with the mocked dependencies
        $type = new UserType($this->mockUserGroupResource, new UserGroupTransformer($this->mockUserGroupResource));

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        ];
    }
}
