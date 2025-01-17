@php($leaderboard = Proto\Models\Leaderboard::where('featured', true)->first())

@if($leaderboard)

    <div class="card mb-3">

        <div class="card-header bg-dark" data-bs-toggle="collapse"
             data-bs-target="#collapse-leaderboard-{{ $leaderboard->id }}">
            <i class="fa {{ $leaderboard->icon }}"></i> {{ $leaderboard->name }} Leaderboard
        </div>

        @if(count($leaderboard->entries) > 0)
            <table class="table table-sm mb-0">
                @foreach($leaderboard->entries()->orderBy('points', 'DESC')->limit(5)->get() as $entry)
                    <tr>
                        <td class="ps-3 place-{{ $loop->index+1 }}" style="max-width: 50px">
                            <i class="fas fa-sm fa-fw {{ $loop->index == 0 ? 'fa-crown' : 'fa-hashtag' }}"></i>
                            {{ $loop->index+1 }}
                        </td>
                        <td>{{ $entry->user->name }}</td>
                        <td class="pe-4"><i class="fa {{ $leaderboard->icon }}"></i> {{ $entry->points }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <hr>
            <p class="text-muted text-center pt-3">There are no entries yet.</p>
        @endif

        <div class="p-3">
            <a href="{{ route('leaderboards::index') }}" class="btn btn-info btn-block">Go to leaderboards</a>
        </div>

    </div>
@endif