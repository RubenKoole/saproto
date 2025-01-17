<form method="post"
      action="{{ ( ! isset($dinnerformCurrent) ? route("dinnerform::add") : route("dinnerform::edit", ['id' => $dinnerformCurrent->id])) }}"
      enctype="multipart/form-data">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            {{ ! isset($dinnerformCurrent) ? 'Create Dinnerform' : "Edit Dinnerform at $dinnerformCurrent->restaurant" }}
        </div>

        <div class="card-body">

            <div class="row">

                <!-- Left column -->
                <div class="col-md-6">

                    <!-- Restaurant -->
                    <div class="col-md-12 mb-3">
                        <label for="restaurant">Dinnerform restaurant:</label>
                        <input type="text" class="form-control" id="restaurant" name="restaurant"
                               placeholder="Elat Roma"
                               value="{{ $dinnerformCurrent->restaurant ?? '' }}"
                               required
                        />
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label for="description">Description:</label>
                        <input type="text" class="form-control" id="description" name="description"
                               placeholder="Order with us at Elat Roma"
                               value="{{ $dinnerformCurrent->description ?? '' }}"
                               required
                        />
                    </div>

                    <!-- Website -->
                    <div class="col-md-12 mb-3">
                        <label for="url">Restaurant website:</label>
                        <input type="url" class="form-control" id="url" name="url"
                               placeholder='www.elat-roma.nl/'
                               value="{{ $dinnerformCurrent->url ?? ''}}"
                               required
                        />
                    </div>

                    <!-- Helper Discount -->
                    <div class="col-md-12 mb-3">
                        <label for="helper-discount">Helper discount €:</label>
                        <input type="number" step="0.01" class="form-control" id="helper-discount" name="helper_discount"
                               placeholder='7.5'
                               value="{{ $dinnerformCurrent->helper_discount ?? ''}}"
                               required
                        />
                    </div>

                    <!-- Homepage -->
                    <div class="col-md-12 mb-3">
                        <input type="checkbox" class="form-check-input" id="homepage" name="homepage"
                               {{ ($dinnerformCurrent && $dinnerformCurrent->visible_home_page || ! $dinnerformCurrent) ? 'checked' : '' }}
                        />
                        <label for="homepage">Visible on the homepage?</label>
                    </div>

                </div>

                <!-- Right column -->
                <div class="col-md-6">

                    <!-- Start -->
                    @include('website.layouts.macros.datetimepicker', [
                        'name' => 'start',
                        'label' => 'Opens at:',
                        'placeholder' => $dinnerformCurrent ? $dinnerformCurrent->start->timestamp : null,
                        'input_class_name' => 'mb-3'
                    ])

                    <!-- End -->
                    @include('website.layouts.macros.datetimepicker',[
                        'name' => 'end',
                        'label' => 'Closes at:',
                        'placeholder' => $dinnerformCurrent ? $dinnerformCurrent->end->timestamp : null,
                        'input_class_name' => 'mb-3'
                    ])

                    <!-- Event -->
                    <div class="row align-items-end mb-6">
                        <div class="col-md-12 mb-3 form-group autocomplete">
                            <label for="event-select">Event:</label>
                            <input class="form-control event-search" id="event-select" name="event_select"
                                   value="{{ $dinnerformCurrent ? $dinnerformCurrent->event_id : '' }}"
                                   placeholder="{{ ($dinnerformCurrent && $dinnerformCurrent->event && $dinnerformCurrent->event->activity) ? $dinnerformCurrent->event->title : '' }}"
                            />
                        </div>
                    </div>

                    <!-- Regular Discount -->
                    <div class="col-md-12 mb-3">
                        <label for="regular-discount">Regular discount %:</label>
                        <input type="number" step="0.01" class="form-control" id="regular-discount" name="regular_discount"
                               placeholder='0'
                               value="{{ $dinnerformCurrent->regular_discount_percentage ?? ''}}"
                               required
                        />
                    </div>

                </div>

            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if($dinnerformCurrent)

                @include('website.layouts.macros.confirm-modal', [
                                           'action' => route("dinnerform::delete", ['id' => $dinnerformCurrent->id]),
                                           'text' => 'Delete',
                                           'title' => 'Confirm Delete',
                                           'classes' => 'btn btn-danger ms-2',
                                           'message' => "Are you sure you want to remove the dinnerform opening $dinnerformCurrent->start ordering at $dinnerformCurrent->restaurant?<br><br> This will also delete all orderlines!",
                                           'confirm' => 'Delete',
                                       ])

            @endif
        </div>

    </div>

</form>