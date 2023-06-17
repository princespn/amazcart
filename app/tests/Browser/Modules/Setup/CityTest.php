<?php

namespace Tests\Browser\Modules\Setup;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Modules\Setup\Entities\City;
use Tests\DuskTestCase;

class CityTest extends DuskTestCase
{

    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();


    }

    public function tearDown(): void
    {
        $cities = City::where('id', '>', 48356)->pluck('id');
        City::destroy($cities);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_for_visit_index_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                    ->visit('/setup/location/city')
                    ->assertSee('City List');
        });
    }

    public function test_for_create_city(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->type('#name', 'test-city')
                ->click('#create_form > div > div > div:nth-child(2) > div > div')
                ->click('#create_form > div > div > div:nth-child(2) > div > div > ul > li:nth-child(2)')
                ->pause(20000)
                ->click('#stateDiv > div')
                ->click('#stateDiv > div > ul > li:nth-child(2)')
                ->click('#create_form > div > div > div.col-xl-12 > div > ul > li:nth-child(2) > label > span')
                ->click('#create_form > div > div > div.col-lg-12.text-center > div > button')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Added successfully!');
        });

    }

    public function test_for_validate_create_city(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->click('#create_form > div > div > div.col-lg-12.text-center > div > button')
                ->waitForTextIn('#error_name', 'The name field is required.',25)
                ->waitForTextIn('#error_country', 'The country field is required.', 25)
                ->waitForTextIn('#error_state', 'The state field is required.', 25);

        });

    }



    public function test_for_edit_city(){
        $this->test_for_create_city();
        $this->browse(function (Browser $browser) {
            $browser->waitFor('#allData > tbody', 20000)
                ->type('#allData_filter > label > input[type=search]', 'test-city')
                ->pause(12000)
                ->assertSeeIn('#allData > tbody > tr:nth-child(1) > td:nth-child(2)', 'test-city')
                ->click('#allData > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#allData > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a')
                ->waitFor('#edit_form', 25)
                ->assertSee('Edit City')
                ->type('#name', 'test-city-edit')
                ->click('#edit_form > div > div > div:nth-child(3) > div > div')
                ->click('#edit_form > div > div > div:nth-child(3) > div > div > ul > li:nth-child(3)')
                ->pause(20000)
                ->click('#stateDiv > div')
                ->click('#stateDiv > div > ul > li:nth-child(2)')
                ->click('#edit_form > div > div > div.col-xl-12 > div ul > li:nth-child(1) > label > span')
                ->click('#edit_form > div > div > div.col-lg-12.text-center > div > button')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Updated successfully!');
        });
    }

    public function test_for_validate_edit_city(){
        $this->test_for_create_city();
        $this->browse(function (Browser $browser) {
            $browser->waitFor('#allData > tbody', 25)
                ->type('#allData_filter > label > input[type=search]', 'test-city')
                ->pause(12000)
                ->assertSeeIn('#allData > tbody > tr:nth-child(1) > td:nth-child(2)', 'test-city')
                ->click('#allData > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#allData > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a')
                ->waitFor('#edit_form', 25)
                ->assertSee('Edit City')
                ->type('#name', '')
                ->click('#edit_form > div > div > div:nth-child(3) > div > div')
                ->click('#edit_form > div > div > div:nth-child(3) > div > div > ul > li:nth-child(3)')
                ->pause(20000)
                ->click('#edit_form > div > div > div.col-lg-12.text-center > div > button')
                ->waitForTextIn('#error_name' ,'The name field is required.', 25)
                ->assertSeeIn('#error_name', 'The name field is required.')
                ->assertSeeIn('#error_state', 'The state field is required.');
        });
    }

    public function test_for_status_change(){
        $this->test_for_create_city();
        $this->browse(function (Browser $browser) {
            $browser->waitFor('#allData > tbody', 25)
                ->type('#allData_filter > label > input[type=search]', 'test-city')
                ->pause(15000)
                ->assertSeeIn('#allData > tbody > tr:nth-child(1) > td:nth-child(2)', 'test-city')
                ->click('#allData > tbody > tr:nth-child(1) > td:nth-child(5) > label > div')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Updated successfully!');
        });        
    }
}
