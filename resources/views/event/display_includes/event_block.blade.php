@if($event->mayViewEvent(Auth::user()))

    <a class="card mb-3 leftborder leftborder-info text-decoration-none"
       href="{{ route('event::show', ['id' => $event->getPublicId()]) }}">

        <div class="card-body event text-start {{ $event->image && (!isset($hide_photo) || !$hide_photo) ? 'bg-img' : 'no-img'}}"
             style="{{ $event->image && (!isset($hide_photo) || !$hide_photo) ? sprintf('background: center no-repeat url(%s);', $event->image->generateImagePath(800,300)) : '' }} background-size: cover;">

            {{-- Countdown --}}
            @if(isset($countdown) && $countdown)
                <div class="btn btn-info btn-block mb-3 ">
                    <i class="fas fa-circle-notch fa-fw fa-spin me-2" aria-hidden="true"></i>
                    <span class="proto-countdown" data-countdown-start="{{ $event->start }}" data-countdown-text-counting="Starts in {}" data-countdown-text-finished="Event is underway!">
                        Counting down...
                    </span>
                </div>
            @endif

            {{-- Featured --}}
            @if($event->is_featured)
                <i class="fas fa-star fa-fw text-gold" aria-hidden="true"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="This is a featured activity!"></i>
            @endif

            {{-- Participating --}}
            @if($event->activity && Auth::check() && $event->activity->isParticipating(Auth::user()))
                    @if($event->activity->isOnBackupList(Auth::user()))
                        <i class="fas fa-check text-warning fa-fw" aria-hidden="true"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="You are on the backuplist!"></i>
                    @else
                        <i class="fas fa-check text-primary fa-fw" aria-hidden="true"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="You are participating!"></i>
                    @endif
                @if($event->activity->isHelping(Auth::user()))
                    <i class="fas fa-life-ring fa-fw text-danger" aria-hidden="true"
                       data-bs-toggle="tooltip" data-bs-placement="top" title="You are helping!"></i>
                @endif
            @endif

            {{-- Ticket --}}
            @if (Auth::check() && $event->hasBoughtTickets(Auth::user()))
                <i class="fas fa-ticket-alt fa-fw text-info" aria-hidden="true"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="You bought a ticket!"></i>
            @endif

            {{-- Helper --}}
            @if (Auth::check() && Auth::user()->is_member && $event->activity && $event->activity->inNeedOfHelp(Auth::user()))
                <i class="fas fa-exclamation-triangle fa-fw text-danger" aria-hidden="true"
                   data-bs-toggle="tooltip" data-bs-placement="top" title="This activity needs your help!"></i>
            @endif

            {{-- Title --}}
            <strong>{{ $event->title }}</strong>

            {{-- Secret --}}
            @if($event->secret)
                <span class="badge bg-info float-end mb-1" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Secret activities can only be visited if you know the link. You can see it now because you are an admin.">
                    <i class="fas fa-eye-slash fa-fw me-1"></i> Secret!
                </span>
            @endif

            {{-- Published --}}
            @if($event->publication && !$event->isPublished())
                <span class="badge bg-warning float-end mb-1" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="Scheduled activities can only be visited if the schedule date is past. You can see it now because you are an admin.">
                    <i class="fas fa-eye-slash fa-fw me-1"></i> Scheduled!
                </span>
            @endif

            {{-- Category --}}
            @if($event->category)
                <span class="badge rounded-pill bg-info px-3 me-1 mb-1 d-inline-block mw-100 ellipsis float-end">
                    <i class="{{ $event->category->icon }} fa-fw" aria-hidden="true"></i>
                    {{ $event->category->name }}
                </span>
            @endif

            <br>

            {{-- Time --}}
            <span>
                <i class="fas fa-clock fa-fw" aria-hidden="true"></i>
                @if (isset($include_year))
                    {{ $event->generateTimespanText('D j M Y, H:i', 'H:i', '-') }}
                @else
                    {{ $event->generateTimespanText('D j M, H:i', 'H:i', '-') }}
                @endif
            </span>

            <br>

            {{-- Location --}}
            <span>
                <i class="fas fa-map-marker-alt fa-fw" aria-hidden="true"></i>
                {{ $event->location }}
            </span>

            {{-- External --}}
            @if($event->is_external)
                <br>

                <span>
                    <i class="fas fa-info-circle fa-fw" aria-hidden="true"></i>
                    Not Organized by S.A. Proto
                </span>
            @endif

            {{-- Signup Icon --}}
                <div class= "d-flex justify-content-between">
                    @if($event->allUsersCount()>0)
                        <span>
                            <i class="fas fa-user-alt fa-fw" aria-hidden="true"></i>
                            {{$event->allUsersCount()}}
                        </span>
                    @endif

                    @if($event->activity && $event->activity->canSubscribe())
                        <span>
                            <i class="fas fa-lock-open"></i>
                        </span>
                    @endif
                </div>

        </div>

    </a>

@endif