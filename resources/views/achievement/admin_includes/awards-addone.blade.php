<form method="post" action="{{ route('achievement::give') }}">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Give achievements
        </div>

        <div class="card-body">
            <div class="form-group autocomplete">
                <label for="product">Achievement:</label>
                <input class="form-control achievement-search" id="achievement-id" name="achievement-id" required>
            </div>
            <div class="form-group autocomplete">
                <label for="user">User(s):</label>
                <input class="form-control user-search" id="users" name="users[]" data-label="User(s):" multiple required>
            </div>
        </div>

        <div class="card-footer">
            <input type="submit" class="btn btn-success btn-block" value="Save">
        </div>

    </div>


</form>