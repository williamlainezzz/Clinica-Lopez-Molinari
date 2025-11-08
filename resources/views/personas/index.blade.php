@extends('adminlte::page')

@section('title', $pageTitle ?? 'Personas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="m-0">{{ $heading ?? 'Personas' }}</h1>
        <span class="badge badge-info text-uppercase">Rol: {{ $rolLabel ?? 'Administrador' }}</span>
    </div>
@endsection

@section('content')
    @includeIf($bannerPartial)

    @if(!empty($stats))
        <div class="row">
            @foreach($stats as $stat)
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="small-box bg-{{ $stat['variant'] ?? 'primary' }} shadow-sm">
                        <div class="inner">
                            <h3>{{ $stat['value'] }}</h3>
                            <p class="mb-0">{{ $stat['label'] }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $stat['icon'] ?? 'fas fa-circle' }}"></i>
                        </div>
                        <a href="{{ route('personas.index', ['section' => $stat['slug']]) }}" class="small-box-footer">
                            Ver detalle <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="personas-tabs" role="tablist">
                @foreach($tabs as $tab)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activeSlug === $tab['slug'] ? 'active' : '' }}" role="tab"
                           href="{{ route('personas.index', ['section' => $tab['slug']]) }}">
                            <i class="{{ $tab['icon'] }} mr-1"></i>
                            {{ $tab['label'] }}
                            <span class="badge badge-pill badge-light ml-2">{{ $tab['count'] }}</span>
                            @if(!empty($tab['badge']))
                                <span class="badge badge-secondary ml-1">{{ $tab['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            @include('personas.partials.section-table', ['section' => $activeSection])
        </div>
    </div>
@endsection

@push('css')
    <style>
        .small-box .icon {
            top: 10px;
            opacity: 0.3;
        }
        .small-box .icon i {
            font-size: 3rem;
        }
        .gap-2 {
            gap: .5rem;
        }
    </style>
@endpush
