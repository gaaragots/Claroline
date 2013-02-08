<?php

namespace Claroline\CoreBundle\Library\Workspace;

use Claroline\CoreBundle\Library\Testing\FunctionalTestCase;

class CreatorTest extends FunctionalTestCase
{
    /** @var Creator */
    private $creator;

    protected function setUp()
    {
        parent::setUp();
        $this->loadUserFixture();
        $this->creator = $this->client->getContainer()->get('claroline.workspace.creator');
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function testWorkspaceConfigurationIsCheckedBeforeCreation($invalidConfig)
    {
        $this->setExpectedException('RuntimeException');
        $user = $this->getFixtureReference('user/user');

        $this->creator->createWorkspace($invalidConfig, $user);
    }

    public function testWorkspaceCreatedWithMinimalConfigurationHasDefaultParameters()
    {
        $config = new Configuration();
        $config->setWorkspaceName('Workspace Foo');
        $config->setWorkspaceCode('WFOO');
        $user = $this->getFixtureReference('user/user');

        $workspace = $this->creator->createWorkspace($config, $user);

        $this->assertEquals(Configuration::TYPE_SIMPLE, get_class($workspace));
        $this->assertEquals('Workspace Foo', $workspace->getName());
        $roleRepo = $this->client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('ClarolineCoreBundle:Role');
        $this->assertEquals('visitor', $roleRepo->findVisitorRole($workspace)->getTranslationKey());
        $this->assertEquals('collaborator', $roleRepo->findCollaboratorRole($workspace)->getTranslationKey());
        $this->assertEquals('manager', $roleRepo->findManagerRole($workspace)->getTranslationKey());
    }

    public function invalidConfigProvider()
    {
        $firstConfig = new Configuration(); // workspace name is required
        $secondConfig = new Configuration();
        $secondConfig->setWorkspaceName('Workspace X');
        $secondConfig->setWorkspaceType('Some\Type'); // invalid workspace type

        return array(
            array($firstConfig),
            array($secondConfig)
        );
    }
}