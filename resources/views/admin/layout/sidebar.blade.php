<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      <li class="nav-item {{ Request::is('admin/dashboard') == 'dashboard' ? 'active' : '' }}">
        <a href="{{ url('admin/dashboard') }}">
          <i class="la la-tachometer"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      @php $parent = App\Models\Menu::where('parent_id', 0)->oldest('order')->get(); @endphp
      @foreach($parent as $p)
        @php
          $getChildArr = $p->getChildArr();
          $totalChild  = App\Models\UserAccess::whereIn('menu_id', $getChildArr)->where('role_id', session('role_id'))->count();
        @endphp
        @if($p->child()->count() > 0 && $totalChild > 0)
          <li class="nav-item">
            <a href="#">
              <i class="{{ $p->icon }}"></i>
              <span class="menu-title">{{ $p->name }}</span>
            </a>
            <ul class="menu-content">
              @foreach($p->child() as $c)
                @php $permissionChild = App\Models\UserAccess::where('menu_id', $c->id)->where('role_id', session('role_id')); @endphp
                @if($permissionChild->count() > 0)
                  <li class="{{ Request::is($c->url) ? 'active' : '' }}">
                    <a class="menu-item" href="{{ url($c->url) }}">{{ $c->name }}</a>
                  </li>
                @endif
              @endforeach
            </ul>
          </li>
        @else
          @php $permissionParent = App\Models\UserAccess::where('menu_id', $p->id)->where('role_id', session('role_id')); @endphp
          @if($permissionParent->count() > 0)
            <li class="nav-item {{ Request::is($p->url) ? 'active' : '' }}">
              <a href="{{ $p->url ? url($p->url) : 'javascript:void(0);' }}">
                <i class="{{ $p->icon }}"></i>
                <span class="menu-title">{{ $p->name }}</span>
              </a>
            </li>
          @endif
        @endif
      @endforeach
    </ul>
  </div>
</div>
