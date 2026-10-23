@extends('layouts.vertical', ['subtitle' => 'Users & Roles'])

@section('content')
    <x-page-header title="Users & Roles" :crumbs="['Settings', 'Users']" />

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>New password</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($users as $u)
                                    <tr>
                                        <td class="td-title"><strong>{{ $u->name }}</strong><div class="small text-muted">{{ $u->email }}</div></td>
                                        <td data-label="Role"><select form="u{{ $u->id }}" name="type" class="form-select form-select-sm">@foreach (\App\Models\User::ROLES as $r)<option @selected($u->type === $r)>{{ $r }}</option>@endforeach</select></td>
                                        <td data-label="Status"><select form="u{{ $u->id }}" name="active" class="form-select form-select-sm"><option value="1" @selected($u->active)>Active</option><option value="0" @selected(! $u->active)>Inactive</option></select></td>
                                        <td data-label="Password"><input form="u{{ $u->id }}" type="password" name="password" class="form-control form-control-sm" placeholder="Leave blank" autocomplete="new-password"></td>
                                        <td class="td-actions text-end"><form method="POST" action="{{ route('users.update', $u) }}" id="u{{ $u->id }}">@csrf @method('PUT')<button class="btn btn-sm btn-light">Save</button></form></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">What each role is for</h5></div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item"><strong>Super Admin / Manager</strong> - everything, including price and discount overrides, credit-limit overrides, settings and users.</li>
                    <li class="list-group-item"><strong>Sales</strong> - quotations, invoices and receipts. Limited to the max discount and minimum price rules.</li>
                    <li class="list-group-item"><strong>Store</strong> - GRNs, deliveries, transfers and stock adjustments.</li>
                    <li class="list-group-item"><strong>Accounts</strong> - receipts, supplier payments, cheques, expenses and reports.</li>
                </ul>
                <div class="card-footer small text-muted">In this demo, menu access is not yet restricted by role. Pricing and credit overrides already are.</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add user</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Role</label><select name="type" class="form-select">@foreach (\App\Models\User::ROLES as $r)<option>{{ $r }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required autocomplete="new-password"></div>
                        <button class="btn btn-primary w-100">Add user</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
