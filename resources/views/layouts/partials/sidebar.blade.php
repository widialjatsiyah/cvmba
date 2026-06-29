<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
<!-- <div style="border:1px solid #2B80EC"></div> -->
 <hr>
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

	<a href="{{route('home')}}" class="logo">
		<!-- <span class="logo-lg">{{ Session::get('business.name') }}</span> -->
     <span class="logo-lg">
      <img src="{{asset('uploads/business_logos/logo-panjang.png')}}" class="img-rounded" alt="Logo" width="150px">
       
     </span>
	</a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom'); !!}

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>
