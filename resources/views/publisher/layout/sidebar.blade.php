<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true">
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      <li class="nav-item {{ Request::is('publisher/dashboard') == 'dashboard' ? 'active' : '' }}">
        <a href="{{ url('publisher/dashboard') }}">
          <i class="la la-tachometer"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      @php $parent = App\Model\Menu::where('parent_id', 0)->orderBy('name', 'desc')->get(); @endphp
      @foreach($parent as $p)
        @php 
          $getChildArr = $p->getChildArr();
          $totalChild  = App\Model\UserAccess::whereIn('menu_id', $getChildArr)->where('role_id', session('role_id'))->count();
        @endphp
        @if($p->child()->count() > 0 && $totalChild > 0)
          <li class="nav-item">
            <a href="#">
              <i class="{{ $p->icon }}"></i>
              <span class="menu-title">{{ $p->name }}</span>
            </a>
            <ul class="menu-content">
              @foreach($p->child() as $c)
                @php $permissionChild = App\Model\UserAccess::where('menu_id', $c->id)->where('role_id', session('role_id')); @endphp
                @if($permissionChild->count() > 0)
                  <li class="{{ Request::is($c->url) ? 'active' : '' }}">
                    <a class="menu-item" href="{{ url($c->url) }}">{{ $c->name }}</a>
                  </li>
                @endif
              @endforeach
            </ul>
          </li>
        @else
          @php $permissionParent = App\Model\UserAccess::where('menu_id', $p->id)->where('role_id', session('role_id')); @endphp
          @if($permissionParent->count() > 0)
            <li class="nav-item {{ Request::is($p->url) ? 'active' : '' }}">
              <a href="{{ url($p->url) }}">
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