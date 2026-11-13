@php
    $alerts = \App\Support\Alerts::forTopbar();
@endphp
<header class="app-topbar">
     <div class="container-fluid">
          <div class="navbar-header">
               <div class="d-flex align-items-center gap-2">
                    <div class="topbar-item">
                         <button type="button" class="button-toggle-menu topbar-button" aria-label="Menu">
                              <iconify-icon icon="solar:hamburger-menu-outline" class="fs-24 align-middle"></iconify-icon>
                         </button>
                    </div>
                    <div class="d-none d-md-flex gap-2">
                         <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-brand"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-1"></iconify-icon>Sell</a>
                         <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-light"><iconify-icon icon="solar:box-outline" class="align-middle me-1"></iconify-icon>Buy</a>
                         <a href="{{ route('payments.receive') }}" class="btn btn-sm btn-light"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-1"></iconify-icon>Get paid</a>
                    </div>
               </div>

               <button type="button" class="topbar-search d-none d-md-flex" data-bs-toggle="modal" data-bs-target="#product-lookup">
                    <iconify-icon icon="solar:magnifer-linear"></iconify-icon><span>Check price & stock...</span><kbd>Ctrl K</kbd>
               </button>

               <div class="d-flex align-items-center gap-1">
                    <div class="topbar-item d-md-none">
                         <button type="button" class="topbar-button" data-bs-toggle="modal" data-bs-target="#product-lookup" aria-label="Check price and stock">
                              <iconify-icon icon="solar:magnifer-linear" class="fs-22 align-middle"></iconify-icon>
                         </button>
                    </div>
                    <div class="dropdown topbar-item d-md-none">
                         <button type="button" class="topbar-button" data-bs-toggle="dropdown" aria-label="Quick actions">
                              <iconify-icon icon="solar:add-circle-bold" class="fs-26 align-middle text-primary"></iconify-icon>
                         </button>
                         <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item py-2" href="{{ route('invoices.create') }}"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-2"></iconify-icon>Sell</a>
                              <a class="dropdown-item py-2" href="{{ route('purchases.create') }}"><iconify-icon icon="solar:box-outline" class="align-middle me-2"></iconify-icon>Buy</a>
                              <a class="dropdown-item py-2" href="{{ route('payments.receive') }}"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-2"></iconify-icon>Get paid</a>
                              <a class="dropdown-item py-2" href="{{ route('products.create') }}"><iconify-icon icon="ph:sneaker" class="align-middle me-2"></iconify-icon>Add product</a>
                              <a class="dropdown-item py-2" href="{{ route('expenses.index') }}"><iconify-icon icon="solar:bill-list-outline" class="align-middle me-2"></iconify-icon>Add expense</a>
                         </div>
                    </div>

                    <div class="topbar-item">
                         <button type="button" class="topbar-button" id="light-dark-mode" aria-label="Dark mode">
                              <iconify-icon icon="solar:moon-outline" class="fs-22 align-middle light-mode"></iconify-icon>
                              <iconify-icon icon="solar:sun-2-outline" class="fs-22 align-middle dark-mode"></iconify-icon>
                         </button>
                    </div>

                    <div class="dropdown topbar-item">
                         <button type="button" class="topbar-button position-relative" data-bs-toggle="dropdown" aria-label="Reminders">
                              <iconify-icon icon="solar:bell-bing-outline" class="fs-22 align-middle"></iconify-icon>
                              @if ($alerts->count())
                                   <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">{{ $alerts->count() }}</span>
                              @endif
                         </button>
                         <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end">
                              <div class="p-2 border-bottom bg-light bg-opacity-50"><h6 class="m-0 fs-16 fw-semibold">Reminders</h6></div>
                              @forelse ($alerts as $alert)
                                   <a href="{{ $alert['url'] }}" class="dropdown-item p-2 border-bottom text-wrap d-flex gap-2 align-items-start">
                                        <span class="avatar-sm flex-shrink-0 rounded bg-{{ $alert['color'] }}-subtle text-{{ $alert['color'] }} d-flex align-items-center justify-content-center">
                                             <iconify-icon icon="{{ $alert['icon'] }}" class="fs-20"></iconify-icon>
                                        </span>
                                        <span><span class="fw-semibold d-block">{{ $alert['title'] }}</span><span class="text-muted small">{{ $alert['text'] }}</span></span>
                                   </a>
                              @empty
                                   <div class="p-3 text-center text-muted small">All clear.</div>
                              @endforelse
                         </div>
                    </div>

                    <div class="dropdown topbar-item">
                         <a type="button" class="topbar-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="d-flex align-items-center">
                                   <img class="rounded-circle" width="32" height="32" style="object-fit: cover" src="{{ Auth::user()?->image_path ? asset(Auth::user()->image_path) : asset('images/users/avatar-6.jpg') }}" alt="">
                              </span>
                         </a>
                         <div class="dropdown-menu dropdown-menu-end">
                              <h6 class="dropdown-header">{{ Auth::user()?->name }}<br><span class="fw-normal text-muted">{{ Auth::user()?->type }}</span></h6>
                              <a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><iconify-icon icon="solar:user-outline" class="align-middle me-2 fs-18"></iconify-icon>My account</a>
                              <button type="button" class="dropdown-item d-none" data-install-app><iconify-icon icon="solar:smartphone-outline" class="align-middle me-2 fs-18"></iconify-icon>Install app on this phone</button>
                              <div class="dropdown-divider my-1"></div>
                              <form method="POST" action="{{ route('logout') }}">
                                   @csrf
                                   <button type="submit" class="dropdown-item text-danger"><iconify-icon icon="solar:logout-3-outline" class="align-middle me-2 fs-18"></iconify-icon>Logout</button>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</header>
