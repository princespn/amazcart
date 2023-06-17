<?php

namespace Tests\Browser\Modules\SupportTicket;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Modules\SupportTicket\Entities\TicketStatus;
use Tests\DuskTestCase;

class StatusTest extends DuskTestCase
{

    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();


    }

    public function tearDown(): void
    {
        $statuses = TicketStatus::where('id', '>', 4)->pluck('id');
        TicketStatus::destroy($statuses);

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
                    ->visit('/admin/ticket/status')
                    ->assertSee('Status List');
        });
    }


    public function test_for_create_status(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->type('#name', 'test-status')
                ->click('#add_form > div > div:nth-child(1) > div.col-xl-12 > div > ul > li:nth-child(2) > label > span')
                ->click('#submit_btn')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Created successfully!');
        });
    }

    public function test_for_validate_create_form(){
        $this->test_for_visit_index_page();
        $this->browse(function (Browser $browser) {
            $browser->type('#name', '')
                ->click('#submit_btn')
                ->waitForText('The name field is required.', 25)
                ->type('#name', 'Pending')
                ->pause(1000)
                ->click('#submit_btn')
                ->waitForText('The name has already been taken.', 25);
        });
    }

    public function test_for_edit_status(){
        $this->test_for_create_status();
        $this->browse(function (Browser $browser) {
            $browser->type('#DataTables_Table_1_filter > label > input[type=search]', 'test-status')
                ->pause(5000)
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > button')
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > div > a.dropdown-item.edit_status')
                ->waitForText('Edit', 25)
                ->type('#name', 'test-status')
                ->click('#edit_form > div > div:nth-child(1) > div.col-xl-12 > div > ul > li:nth-child(2) > label > span')
                ->click('#submit_btn')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Updated successfully!');
        });
    }

    public function test_for_validate_edit_form(){
        $this->test_for_create_status();
        $this->browse(function (Browser $browser) {
            $browser->type('#DataTables_Table_1_filter > label > input[type=search]', 'test-status')
                ->pause(5000)
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > button')
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > div > a.dropdown-item.edit_status')
                ->waitForText('Edit', 25)
                ->type('#name', '')
                ->click('#submit_btn')
                ->waitForText('The name field is required.', 25)
                ->type('#name', 'Pending')
                ->pause(1000)
                ->click('#submit_btn')
                ->waitForText('The name has already been taken.', 25);
        });
    }


    public function test_for_delete_status(){
        $this->test_for_create_status();
        $this->browse(function (Browser $browser) {
            $browser->type('#DataTables_Table_1_filter > label > input[type=search]', 'test-status')
                ->pause(5000)
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > button')
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(4) > div > div > a.dropdown-item.delete_status')
                ->whenAvailable('#item_delete_form', function($modal){
                    $modal->click('#dataDeleteBtn');
                })
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Deleted successfully!');
        });        
    }

    public function test_for_update_status(){
        $this->test_for_create_status();
        $this->browse(function (Browser $browser) {
            $browser->type('#DataTables_Table_1_filter > label > input[type=search]', 'test-status')
                ->pause(6000)
                ->click('#sku_tbody > tr:nth-child(1) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message',25)
                ->assertSeeIn('.toast-message', 'Updated successfully!');

        });        
    }

}
