<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;

class UserGroupCrudIndexTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'index';
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
        $this->seedAndTestGroups(20);
        $this->seedAndTestUserGroups(70);
    }

    /** @test */
    public function index_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 70, 70);
    }

    /** @test */
    public function index_with_default_data_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 70, 70);
    }

    /** @test */
    public function index_last_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 70, 70);
    }

    /** @test */
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 70, 70, true);
    }

    /** @test */
    public function index_with_custom_filters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 15, 6, 45, 40);
    }

    /** @test */
    public function index_with_custom_filters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 15, 6, 45, 40);
    }
}
