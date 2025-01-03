@extends('layouts.app')

@section('page', 'Frequently Asked Questions')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('content')
    <div class="row mb-5">
        <div class="accordion dark-accordion rounded" id="accordionParent">
            @foreach ($faqs as $key => $faq)
            <div class="col-12 mb-3">
                <div class="accordion-item accordion-wrapper">
                    <h2 class="accordion-header bg-white rounded" id="header-{{ $key }}">
                        <button class="accordion-button collapsed accordion-light-primary txt-primary fw-medium" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $key }}" aria-expanded="false" aria-controls="collapse-{{ $key }}">
                            {{ $faq['question'] }} <i class="svg-color" data-feather="chevron-down"></i>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse bg-white" id="collapse-{{ $key }}" aria-labelledby="header-{{ $key }}" data-bs-parent="#accordionParent">
                        <div class="accordion-body">
                            <p class="mb-0">{!! $faq['answer'] !!}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script></script>
@endsection
