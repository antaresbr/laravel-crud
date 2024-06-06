<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;

class UserCrudIndexTest extends UserCrudAbstractTestCase
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
        $this->seedAndTestUsers(90);
    }

    /** @test */
    public function index_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65);
    }

    /** @test */
    public function index_with_default_data_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 65, 65);
    }

    /** @test */
    public function index_last_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(3, 1, 65, 65);
    }

    /** @test */
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65, true);
    }

    /** @test */
    public function index_with_custom_filters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 20, 11, 45, 35);
    }

    /** @test */
    public function index_with_custom_filters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 20, 11, 45, 35);
    }
}
