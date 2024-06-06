<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;

class UserGroupCrudSearchTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'search';
    }

    /** @test */
    public function reset_database()
    {
        $this->resetDatabase();
    }

    /** @test */
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    /** @test */
    public function unauthenticated_index()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(50);
        $this->seedAndTestGroups(30);
        $this->seedAndTestUserGroups(95);
    }

    /** @test */
    public function search_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 95, 95);
    }

    /** @test */
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 95, 95, true);
    }

    /** @test */
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 20, 16, 85, 70, true);
    }

    /** @test */
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 20, 16, 85, 70, true);
    }
}
