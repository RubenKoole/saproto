<div class="card mb-3">

    <div class="card-header bg-dark text-white">
        See previous months
    </div>

    <ul class="list-group list-group-flush" id="omnomcom-years-accordion">

        @foreach($available_months as $year => $months)

            <li class="list-group-item cursor-pointer" data-bs-toggle="collapse"
                data-bs-target="#omnomcom-years-{{ $year }}">
                <strong>{{ $year }}</strong>
            </li>
            <div id="omnomcom-years-{{ $year }}" class="collapse {{ $year == date('Y') ? 'show' : null }}" data-parent="#omnomcom-years-accordion">
                @foreach($months as $month)
                    <a href="{{ route("omnomcom::orders::list", ['date' => $month]) }}"
                       class="list-group-item">
                        {{ date('F Y', strtotime($month)) }}
                    </a>
                @endforeach
            </div>

        @endforeach
    </ul>

</div>