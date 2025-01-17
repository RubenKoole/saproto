@extends('website.layouts.redesign.dashboard')

@section('page-title')
    News Admin
@endsection

@section('container')

    <div id="news-admin" class="row justify-content-center">

        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('news::add') }}" class="badge bg-info float-end">
                        Create a new news item.
                    </a>
                </div>

                <div class="table-responsive">

                    <table class="table table-sm table-hover">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td>Title</td>
                            <td>Published</td>
                            <td>Controls</td>

                        </tr>

                        </thead>

                        @foreach($newsitems as $newsitem)

                            <tr>

                                <td class="title">{{ $newsitem->title }}</td>
                                <td class="published-at">
                                    <span class="text-{{ $newsitem->isPublished() ? 'primary' : 'muted' }}">
                                        {{ $newsitem->published_at }}
                                    </span>
                                </td>
                                <td class="controls">
                                    <a href="{{ route('news::show', ['id' => $newsitem->id]) }}">
                                        <i class="fas fa-link me-2"></i>
                                    </a>

                                    <a href="{{ route('news::edit', ['id' => $newsitem->id]) }}">
                                        <i class="fas fa-edit me-2"></i>
                                    </a>

                                    @include('website.layouts.macros.confirm-modal', [
                                        'action' => route('news::delete', ['id' => $newsitem->id]),
                                        'text' => '<i class="fas fa-trash text-danger"></i>',
                                        'title' => 'Confirm Delete',
                                        'message' => 'Are you sure you want to delete this news item?',
                                        'confirm' => 'Delete',
                                    ])

                                </td>

                            </tr>

                        @endforeach

                    </table>

                </div>

                <div class="card-footer pb-0">
                    {!! $newsitems->links() !!}
                </div>

            </div>

        </div>

    </div>

@endsection