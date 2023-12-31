@extends('backEnd.master')
@section('page-title', app('general_setting')->site_title)
@section('mainContent')
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('dashboard.dashboard_setup') }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="white_box_50px box_shadow_white">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('dashboard.make_widget_card_colorful') }}</h3>
                        </div>
                    </div>
                    <div class="row">
                        @foreach (app('dashboard_setup')->whereBetween('id', [1,12]) as $key => $config)
                            @if(!isModuleActive('MultiVendor') && $config->id == 2)
                            @else
                                <div class="col-4">
                                    <label class="primary_input_label">
                                        @php
                                            switch ($config->type) {
                                                case 'total_products':
                                                echo __("dashboard.total_product");
                                                break;
                                                case 'total_sellers':
                                                echo __("dashboard.total_seller");
                                                break;
                                                case 'total_customer':
                                                echo __("dashboard.total_customer");
                                                break;
                                                case 'todays_visitor':
                                                echo __("dashboard.today_visitors");
                                                break;
                                                case 'total_sale':
                                                echo __("dashboard.total_sale");
                                                break;
                                                case 'total_order':
                                                echo __("dashboard.total_order");
                                                break;
                                                case 'pending_order':
                                                echo __("dashboard.total_pending_order");
                                                break;
                                                case 'completed_order':
                                                echo __("dashboard.total_completed_order");
                                                break;
                                                case 'total_review':
                                                echo __("dashboard.total_review");
                                                break;
                                                case 'total_revenue':
                                                echo __("dashboard.total_revenue");
                                                break;
                                                case 'active_customer':
                                                echo __("dashboard.active_customers");
                                                break;
                                                case 'total_subcriber':
                                                echo __("dashboard.total_subscriber");
                                                break;
                                            }
                                        @endphp
                                    </label>
                                    <ul class="permission_list sms_list">
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="{{$config->type}}" class="config_type" type="radio" id="{{$config->type}}_1" value="0" @if ($config->is_active == 0) checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('dashboard.normal') }}</p>
                                        </li>
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="{{$config->type}}" class="config_type" type="radio" id="{{$config->type}}_1" value="1" @if ($config->is_active == 1) checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('dashboard.color') }}</p>
                                        </li>
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="{{$config->type}}" class="config_type" type="radio" id="{{$config->type}}_1" value="2" @if ($config->is_active == 2) checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('dashboard.reverse') }}</p>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="box_header common_table_header mt-5">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('dashboard.make_visible_in_dashboard') }}</h3>
                        </div>
                    </div>
                    <div class="row">
                        @foreach (app('dashboard_setup')->whereBetween('id', [13,41]) as $key => $config_d)
                        @if(!isModuleActive('MultiVendor') && $config_d->id == 14 || $config_d->id == 27 || $config_d->id == 31)
                        @else
                                <div class="col-4">
                                    <label class="primary_input_label">
                                        @php
                                            switch ($config_d->type) {
                                                case 'total_product_card':
                                                echo __("dashboard.total_product_card");
                                                break;
                                                case 'total_seller_card':
                                                echo __("dashboard.total_seller_card");
                                                break;
                                                case 'total_customer_card':
                                                echo __("dashboard.total_customer_card");
                                                break;
                                                case 'visitor_card':
                                                echo __("dashboard.visitor_card");
                                                break;
                                                case 'total_order_card':
                                                echo __("dashboard.total_order_card");
                                                break;
                                                case 'total_pending_order_card':
                                                echo __("dashboard.total_pending_order_card");
                                                break;
                                                case 'total_completed_order_card':
                                                echo __("dashboard.total_completed_order_card");
                                                break;
                                                case 'total_sale_amount_card':
                                                echo __("dashboard.total_sale_amount_card");
                                                break;
                                                case 'total_review_card':
                                                echo __("dashboard.total_review_card");
                                                break;
                                                case 'total_revenue_card':
                                                echo __("dashboard.total_revenue_card");
                                                break;
                                                case 'total_active_customer_card':
                                                echo __("dashboard.total_active_customer_card");
                                                break;
                                                case 'total_subscriber_card':
                                                echo __("dashboard.total_subscriber_card");
                                                break;
                                                case 'product_graph':
                                                echo __("dashboard.product_graph");
                                                break;
                                                case 'order_summary_graph':
                                                echo __("dashboard.order_summary_graph");
                                                break;
                                                case 'seller_graph':
                                                echo __("dashboard.seller_graph");
                                                break;
                                                case 'guest_vs_registered_order_graph':
                                                echo __("dashboard.guest_vs_registered_order_graph");
                                                break;
                                                case 'today_order_summary_graph':
                                                echo __("dashboard.today_order_summary_graph");
                                                break;
                                                case 'top_ten_product':
                                                echo __("dashboard.top_ten_product");
                                                break;
                                                case 'top_ten_seller':
                                                echo __("dashboard.top_ten_seller");
                                                break;
                                                case 'category_wise_product_qty':
                                                echo __("dashboard.category_wise_product_qty");
                                                break;
                                                case 'category_wise_sale':
                                                echo __("dashboard.category_wise_sale");
                                                break;
                                                case 'coupon_wise_sale':
                                                echo __("dashboard.coupon_wise_sale");
                                                break;
                                                case 'new_customers':
                                                echo __("dashboard.new_customers");
                                                break;
                                                case 'recently_added_products':
                                                echo __("dashboard.recently_added_products");
                                                break;
                                                case 'top_refferer':
                                                echo __("dashboard.top_refferers");
                                                break;
                                                case 'latest_order':
                                                echo __("dashboard.latest_order");
                                                break;
                                                case 'latest_search_keyword':
                                                echo __("dashboard.latest_search_keyword");
                                                break;
                                                case 'appealed_dispute':
                                                echo __("dashboard.appealed_disputed");
                                                break;
                                                case 'reviews':
                                                echo __("dashboard.reviews");
                                                break;
                                            }
                                            @endphp
                                    </label>
                                    <ul class="permission_list sms_list">
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="{{$config_d->type}}" class="config_type" type="radio" id="{{$config_d->type}}_1" value="1" @if ($config_d->is_active == 1) checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('dashboard.visible') }}</p>
                                        </li>
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="{{$config_d->type}}" class="config_type" type="radio" id="{{$config_d->type}}_1" value="0" @if ($config_d->is_active == 0) checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ __('dashboard.hide') }}</p>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script type="text/javascript">
    (function($) {
    "use strict";
        $(document).ready(function(){
            $(document).on('change', '.config_type', function(){
                $.post("{{ route('appearance.dashoboard.status_update') }}", {_token:'{{ csrf_token() }}', type:this.name, is_active:this.value}, function(data){
                    if(data == 1){
                        toastr.success("{{__('common.updated_successfully')}}","{{__('common.success')}}");
                    }
                    else{
                        toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                    }
                })
                .fail(function(response) {
                if(response.responseJSON.error){
                        toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                        $('#pre-loader').addClass('d-none');
                        return false;
                    }
                });
            });
        });
    })(jQuery);
</script>
@endpush
