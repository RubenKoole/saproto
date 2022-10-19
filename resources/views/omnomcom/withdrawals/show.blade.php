@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Withdrawal of {{ date('d-m-Y', strtotime($withdrawal->date)) }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="post" action="{{ route('omnomcom::withdrawal::edit', ['id' => $withdrawal->id]) }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white mb-2">
                        @yield('page-title')
                    </div>

                    <table class="table table-sm table-borderless ms-3">

                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $withdrawal->withdrawalId() }}</td>
                        </tr>
                        <tr>
                            <th>Users</th>
                            <td>{{ $withdrawal->users()->count() }}</td>
                        </tr>
                        <tr>
                            <th>Orderlines</th>
                            <td>{{ $withdrawal->orderlines->count() }}</td>
                        </tr>
                        <tr>
                            <th>Sum</th>
                            <td>&euro;{{ number_format($withdrawal->total(), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $withdrawal->closed ? 'Closed' : 'Pending' }}</td>
                        </tr>
                        </tbody>

                    </table>

                    <div class="card-body">
                        @include('website.layouts.macros.datetimepicker', [
                            'name' => 'date',
                            'label' => 'Change date:',
                            'placeholder' => strtotime($withdrawal->date),
                            'format' => 'date'
                        ])
                    </div>

                    <div class="card-footer">

                        <input type="submit" value="Save" class="btn btn-success btn-block">

                        <a href="{{ route('omnomcom::withdrawal::export', ['id' => $withdrawal->id]) }}"
                           class="btn btn-outline-success btn-block">
                            Generate XML
                        </a>

                        @include('website.layouts.macros.confirm-modal', [
                           'action' => route('omnomcom::withdrawal::email', ['id' => $withdrawal->id]),
                           'classes' => 'btn btn-outline-warning btn-block',
                           'text' => 'E-mail Users',
                           'title' => 'Confirm Send',
                           'message' => 'Are you sure you want to send an email to all '.$withdrawal->users()->count().' users associated with this withdrawal?',
                           'confirm' => 'Send',
                        ])

                        @include('website.layouts.macros.confirm-modal', [
                           'action' => route('omnomcom::withdrawal::close', ['id' => $withdrawal->id]),
                           'classes' => 'btn btn-outline-danger btn-block',
                           'text' => 'Close Withdrawal',
                           'title' => 'Confirm Close',
                           'message' => 'Are you sure you want to close this withdrawal? After closing, you cannot change anything about this withdrawal anymore.',
                           'confirm' => 'Close',
                        ])

                        @include('website.layouts.macros.confirm-modal', [
                           'action' => route('omnomcom::withdrawal::delete', ['id' => $withdrawal->id]),
                           'classes' => 'btn btn-outline-danger btn-block',
                           'text' => 'Delete',
                           'title' => 'Confirm Delete',
                           'message' => 'Are you sure you want to delete this withdrawal?',
                        ])

                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-9">

            <div class="card mb-3">

                <div class="card-header mb-1 bg-dark text-white">
                    Users in this withdrawal
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">

                        <thead>
                        <tr class="bg-dark text-white">
                            <td>User</td>
                            @if(!$withdrawal->closed)
                                <td>Bank Account</td>
                                <td>Authorization</td>
                            @endif
                            <td>#</td>
                            <td>Sum</td>
                            @if(!$withdrawal->closed)
                                <td>Controls</td>
                            @endif
                        </tr>
                        </thead>

                        @foreach($withdrawal->totalsPerUser() as $data)

                            <tr class="{{ !isset($data->user->bank) ? 'bg-warning' : '' }}">
                                <td>{{ $data->user->name }}</td>
                                @if(!$withdrawal->closed)
                                    @isset($data->user->bank)
                                        <td>
                                            <strong>{{ $data->user->bank->iban }}</strong>
                                            / {{ $data->user->bank->bic }}
                                        </td>
                                        <td>{{ $data->user->bank->machtigingid }}</td>
                                    @else
                                        <td>
                                            <i class="fa fas fa-exclamation-triangle"></i>
                                            <strong>This user no longer exists</strong>
                                        </td>
                                        <td></td>
                                    @endisset
                                @endif
                                <td>{{ $data->count }}</td>
                                <td>&euro;{{ number_format($data->sum, 2, ',', '.') }}</td>
                                @if(!$withdrawal->closed)
                                    <td>
                                        @if($withdrawal->getFailedWithdrawal($data->user))
                                            Failed
                                            @include('website.layouts.macros.confirm-modal', [
                                               'action' => route('omnomcom::orders::delete', ['id'=>$withdrawal->getFailedWithdrawal($data->user)->correction_orderline_id]),
                                               'text' => '(Revert)',
                                               'title' => 'Confirm Revert',
                                               'message' => 'Are you sure you want to revert this withdrawal? The user will <b>NOT</b> automatically receive an e-mail about this!',
                                               'confirm' => 'Revert',
                                            ])
                                        @else
                                            <a href="{{ route('omnomcom::withdrawal::deleteuser', ['id' => $withdrawal->id, 'user_id' => $data->user->id]) }}">
                                                Remove
                                            </a>

                                            or

                                            @include('website.layouts.macros.confirm-modal', [
                                               'action' => route('omnomcom::withdrawal::markfailed', ['id' => $withdrawal->id, 'user_id' => $data->user->id]),
                                               'text' => 'Mark Failed',
                                               'title' => 'Confirm Marking Failed',
                                               'message' => 'Are you sure you want to mark this withdrawal as for '.$data->user->name.' as failed? They <b>will</b> automatically receive an e-mail about this!',
                                            ])
                                        @endif
                                    </td>
                                @endif
                            </tr>

                        @endforeach

                    </table>
                </div>

            </div>

        </div>

    </div>

@endsection