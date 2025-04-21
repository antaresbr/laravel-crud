<?php

namespace Antares\Tests\Feature\GroupCrud;

use Antares\Tests\Package\AbstractTestCases\GroupCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class GroupCrudIndexTest extends GroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'index';
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
        $this->seedAndTestGroups(55);
    }

    #[Test]
    public function index_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 55, 55);
    }

    #[Test]
    public function index_with_default_data_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 55, 55);
    }

    #[Test]
    public function index_last_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 55, 55);
    }

    #[Test]
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 55, 55, true);
    }

    #[Test]
    public function index_with_custom_filters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 20, 11, 40, 30);
    }

    #[Test]
    public function index_with_custom_filters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 20, 11, 40, 30);
    }
}
