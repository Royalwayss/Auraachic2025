<div class="col-lg-3 col-md-12">
                    <div class="accTabs">
                        <ul>
                            <li><a @if($data['slug'] == 'profile') class="active" @endif href="{{ route('account',['profile']) }}">Profile</a></li>
                            <li><a @if($data['slug'] == 'address') class="active" @endif href="{{ route('account',['address']) }}">Address</a></li>
                            <li><a @if($data['slug'] == 'orders') class="active" @endif href="{{ route('account',['orders']) }}">Orders</a></li>
                            <li><a @if($data['slug'] == 'wishlist') class="active" @endif href="{{ route('account',['wishlist']) }}">Wishlist</a></li>
                            <li><a @if($data['slug'] == 'settings') class="active" @endif href="{{ route('account',['settings']) }}">Change Password</a></li>
                            <li><a href="javascript::void()" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>