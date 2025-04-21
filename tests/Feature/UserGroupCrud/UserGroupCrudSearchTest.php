<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class UserGroupCrudSearchTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'search';
    }

    #[Test]
    public function reset_database()
    {
        $this->resetDatabase();
    }

    #[Test]
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    #[Test]
    public function unauthenticated_index()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    #[Test]
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(50);
        $this->seedAndTestGroups(30);
        $this->seedAndTestUserGroups(95);
    }

    #[Test]
    public function search_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 95, 95);
    }

    #[Test]
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 95, 95, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 20, 16, 85, 70, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 20, 16, 85, 70, true);
    }
}
