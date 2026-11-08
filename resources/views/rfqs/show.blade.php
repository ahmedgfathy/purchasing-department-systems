@extends('layouts.app')

@section('title', $rfq->reference)
@section('heading', $rfq->reference)
@section('subheading', $rfq->title)

@section('actions')
    <a href="{{ route('rfqs.index') }}" class="btn btn-ghost">{{ __('app.rfq.back') }}</a>
    @if (in_array($rfq->status, [\App\Models\Rfq::STATUS_PENDING, \App\Models\Rfq::STATUS_REJECTED], true) && auth()->user()->hasPermission('rfq.update'))
        <a href="{{ route('rfqs.edit', $rfq) }}" class="btn btn-secondary">{{ __('app.rfq.edit') }}</a>
    @endif
    @if (in_array($rfq->status, [\App\Models\Rfq::STATUS_PENDING, \App\Models\Rfq::STATUS_REJECTED], true) && auth()->user()->hasPermission('rfq.delete'))
        <form method="POST" action="{{ route('rfqs.destroy', $rfq) }}" onsubmit="return confirm('{{ __('app.rfq.confirm_delete') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">{{ __('app.rfq.delete') }}</button>
        </form>
    @endif
@endsection

@section('content')
    @php
        $assignedIds = $rfq->vendors->pluck('id')->all();
        $assignmentNote = $rfq->vendors->first()?->pivot->note;
    @endphp

    @if ($rfq->status === \App\Models\Rfq::STATUS_REJECTED && $rfq->rejection_reason)
        <div class="alert error">
            <strong>{{ __('app.rfq.rejection_reason') }}:</strong> {{ $rfq->rejection_reason }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ $rfq->title }}</h2>
                <p>{{ __('app.rfq.submitted_at') }} {{ $rfq->created_at->translatedFormat(__('app.date.long')) }}</p>
            </div>
            <span class="badge {{ $rfq->statusColor() }}">{{ $rfq->statusLabel() }}</span>
        </div>

        <div class="detail-grid">
            <div class="detail">
                <dt>{{ __('app.rfq.company_name') }}</dt>
                <dd>{{ $rfq->title }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.requested_by') }}</dt>
                <dd>{{ $rfq->requestedBy?->name ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.priority') }}</dt>
                <dd><span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span></dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.needed_by') }}</dt>
                <dd>{{ $rfq->needed_by?->translatedFormat(__('app.date.short')) ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.currency') }}</dt>
                <dd class="mono">{{ $rfq->currency }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.approved_by') }}</dt>
                <dd>{{ $rfq->approvedBy?->name ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.rfq.assigned_by') }}</dt>
                <dd>{{ $rfq->vendorsAssignedBy?->name ?? '—' }}</dd>
            </div>
            @if ($rfq->description)
                <div class="detail" style="grid-column:1/-1">
                    <dt>{{ __('app.rfq.description') }}</dt>
                    <dd>{{ $rfq->description }}</dd>
                </div>
            @endif
            @if ($rfq->notes)
                <div class="detail" style="grid-column:1/-1">
                    <dt>{{ __('app.rfq.notes') }}</dt>
                    <dd>{{ $rfq->notes }}</dd>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.rfq.items') }}</h2>
                <p>{{ trans_choice('app.rfq.items_count', $rfq->items->count(), ['count' => $rfq->items->count()]) }}</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('app.rfq.row_number') }}</th>
                        <th>{{ __('app.rfq.description') }}</th>
                        <th>{{ __('app.rfq.quantity') }}</th>
                        <th>{{ __('app.rfq.unit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rfq->items as $item)
                        <tr>
                            <td class="cell-sub">{{ $loop->iteration }}</td>
                            <td class="cell-title">{{ $item->description }}</td>
                            <td class="cell-sub">{{ $item->quantity }}</td>
                            <td class="cell-sub">{{ $item->unitOption?->name ?? $item->unit ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($rfq->status === \App\Models\Rfq::STATUS_PENDING && auth()->user()->hasPermission('rfq.approve'))
        <div class="two-col" style="margin-top:20px">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>{{ __('app.rfq.approve') }}</h2>
                        <p>{{ __('app.rfq.approval_hint') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('rfqs.approve', $rfq) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">{{ __('app.rfq.approve') }}</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>{{ __('app.rfq.reject') }}</h2>
                        <p>{{ __('app.rfq.reject_hint') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('rfqs.reject', $rfq) }}">
                    @csrf
                    <div class="field">
                        <label for="rejection_reason">{{ __('app.rfq.reject_reason_label') }}</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-danger">{{ __('app.rfq.reject') }}</button>
                </form>
            </div>
        </div>
    @elseif ($rfq->status === \App\Models\Rfq::STATUS_PENDING)
        <div class="card">
            <p class="muted" style="margin:0">{{ __('app.rfq.approval_pending_hint') }}</p>
        </div>
    @endif

    @if (in_array($rfq->status, [\App\Models\Rfq::STATUS_APPROVED, \App\Models\Rfq::STATUS_VENDORS_ASSIGNED], true) && auth()->user()->hasPermission('rfq.assign'))
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>{{ __('app.rfq.vendor_assignment') }}</h2>
                    <p>{{ __('app.rfq.vendor_assignment_hint') }}</p>
                </div>
            </div>

            @if ($vendors->isEmpty())
                <div class="empty">{{ __('app.rfq.no_vendors_available') }}</div>
            @else
                <form method="POST" action="{{ route('rfqs.vendors', $rfq) }}">
                    @csrf
                    <div class="permission-grid">
                        @foreach ($vendors as $vendor)
                            <label class="check">
                                <input type="checkbox" name="vendors[]" value="{{ $vendor->id }}" @checked(in_array($vendor->id, $assignedIds, true))>
                                <span>
                                    <strong>{{ $vendor->name }}</strong>
                                    <span>{{ $vendor->code }} · {{ $vendor->typeLabel() }}{{ $vendor->country ? ' · '.$vendor->country : '' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('vendors') <div class="error-text">{{ $message }}</div> @enderror

                    <div class="field" style="margin-top:16px">
                        <label for="assignment_note">{{ __('app.rfq.assignment_note') }}</label>
                        <textarea id="assignment_note" name="assignment_note" rows="2">{{ old('assignment_note', $assignmentNote) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $rfq->status === \App\Models\Rfq::STATUS_VENDORS_ASSIGNED ? __('app.rfq.update_vendors') : __('app.rfq.assign_vendors') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.rfq.assigned_vendors') }}</h2>
                <p>{{ __('app.rfq.assigned_vendors_sub') }}</p>
            </div>
        </div>

        @if ($rfq->vendors->isEmpty())
            <div class="empty">{{ __('app.rfq.no_vendors_assigned') }}</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.vendors.col_vendor') }}</th>
                            <th>{{ __('app.vendors.col_type') }}</th>
                            <th>{{ __('app.vendors.col_contact') }}</th>
                            <th>{{ __('app.vendors.col_country') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rfq->vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $vendor->name }}</div>
                                    <div class="cell-sub mono">{{ $vendor->code }}</div>
                                </td>
                                <td class="cell-sub">{{ $vendor->typeLabel() }}</td>
                                <td class="cell-sub">
                                    {{ $vendor->contact_name ?: '—' }}
                                    @if ($vendor->email) · {{ $vendor->email }} @endif
                                    @if ($vendor->phone) · {{ $vendor->phone }} @endif
                                </td>
                                <td class="cell-sub">{{ $vendor->country ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
