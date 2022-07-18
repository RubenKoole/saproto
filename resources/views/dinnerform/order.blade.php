@extends('website.layouts.redesign.generic')

@section('page-title')
    Dinner Form
@endsection

@section('container')
    <div class="row justify-content-center">
        @if($dinnerform->isCurrent())
            <div class="col-4">
            @include('dinnerform.includes.order')
            </div>
        @endif
        @if(count($previousOrders)>0)
        <div class="col-4">
                @include('dinnerform.includes.dinnerform-orderlines', ['previousOrders'=>$previousOrders])
        </div>
        @endif
    </div>


@endsection