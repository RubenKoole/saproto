
<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-calendar-alt fa-fw me-2"></i> Upcoming events
    </div>
    <div class="card-body">

        @php($events = Proto\Models\Event::where('is_featured', false)->where('end', '>=', date('U'))->orderBy('start')->with('activity')->limit($n)->get())

        @if(count($events) > 0)

            @foreach($events as $key => $event)

                @if(!$event->secret || (Auth::check() && ($event->activity && $event->activity->isParticipating(Auth::user()))))

                    @include('event.display_includes.event_block', ['event'=> $event])

                    <?php $week = date('W', $event->start); ?>

               @endif

           @endforeach

       @else

           <p class="card-text text-center mt-2 mb-4">
               We have no events coming up soon, sorry! 😟
           </p>

       @endif

       <a href="{{ route("event::list") }}" class="btn btn-info btn-block">Go to the calendar</a>

   </div>
</div>