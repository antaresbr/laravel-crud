<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class UserCrudIndexTest extends UserCrudAbstractTestCase
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
        $this->seedAndTestUsers(90);
    }

    #[Test]
    public function index_with_default_data()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65);
    }

    #[Test]
    public function index_with_default_data_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(2, 1, 65, 65);
    }

    #[Test]
    public function index_last_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(3, 1, 65, 65);
    }

    #[Test]
    public function search_with_ignoreStaticFilters()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withDefaultData(null, 1, 65, 65, true);
    }

    #[Test]
    public function index_with_custom_filters_and_pagination()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(1, 20, 11, 45, 35);
    }

    #[Test]
    public function index_with_custom_filters_and_pagination_second_page()
    {
        $this->bootstrapAndAuthUser();
        $this->indexAndSearchRequest_withFiltersAndPagination(2, 20, 11, 45, 35);
    }
}
