<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;

class UserCrudSearchTest extends UserCrudAbstractTestCase
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
        $this->seedAndTestUsers(100);
    }

    /** @test */
    public function search_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65);
    }

    /** @test */
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 100, 100, true);
    }

    /** @test */
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 25, 21, 90, 70, true);
    }

    /** @test */
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 25, 21, 90, 70, true);
    }
}
