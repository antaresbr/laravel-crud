<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class UserCrudSearchTest extends UserCrudAbstractTestCase
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
        $this->seedAndTestUsers(100);
    }

    #[Test]
    public function search_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65);
    }

    #[Test]
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 100, 100, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 25, 21, 90, 70, true);
    }

    #[Test]
    public function search_with_ignoreStaticFilters_and_customFilters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 25, 21, 90, 70, true);
    }
}
