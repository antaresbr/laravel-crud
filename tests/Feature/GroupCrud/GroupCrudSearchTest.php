<?php

namespace Antares\Tests\Feature\GroupCrud;

use Antares\Tests\Package\AbstractTestCases\GroupCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class GroupCrudSearchTest extends GroupCrudAbstractTestCase
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
        $this->seedAndTestUsers(1);
        $this->seedAndTestGroups(75);
    }

    #[Test]
    public function search_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 75, 75);
    }

    #[Test]
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 75, 75, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 25, 21, 60, 40, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 25, 21, 60, 40, true);
    }
}
