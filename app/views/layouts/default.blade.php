<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>@yield('title') :: A2Z CMS</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- @todo: fill with your company info or remove -->
		-	<link rel="stylesheet" type="text/css"  href="{{asset('assets/site/a2zcms/css/bootstrap.css')}}">	
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/jquery-ui-1.10.3.custom.css')}}">		
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/jquery.multiselect.css')}}">	
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/a2zcms.css')}}">				
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/summernote.css')}}">	
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/summernote-bs3.css')}}">				
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/font-awesome.min.css')}}">	
		<link rel="stylesheet" type="text/css" href="{{asset('assets/site/a2zcms/css/prettify.css')}}">			
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="{{asset('assets/site/a2zcms/js/html5.js')}}"></script>
		<![endif]-->
		<link rel="shortcut icon" href="{{asset('assets/admin/ico/favicon.ico')}}">
		
	</head>
	<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
	      </div>
		 <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav navbar-right">						
			</ul>
        </div>
      </div>
	</nav>
	<div class="container">
	<div class="row">
		@yield('title')  
        @yield('content')      
      </div>
	</div>
	 <footer>
		 <div class="row">
		  	<div class="collapse navbar-collapse navbar-ex1-collapse col-lg-12">
		      	
			</div>			        
		</div>
		<div class="row">
			 <div class="col-lg-12">
				<span style="text-align:left;float:left">
					&copy; 2013 <a class="a2zcms" href="#">A2Z CMS</a></span>
				<span style="text-align: center;padding-left: 30%"></span>
				<span style="text-align:right;float:right">
					Powered by: <a class="a2zcms" href="http://laravel.com/" alt="Laravel 4.1">Laravel 4.1</a></span>
			</div>
		</div>
	</footer>
		<!-- start: JavaScript-->
		<!--[if !IE]>-->
		<script src="{{asset('assets/site/a2zcms/js/jquery-2.0.3.min.js')}}"></script>
		<!--<![endif]-->
		<!--[if IE]>
		<script src="{{asset('assets/site/js/a2zcms/jquery-1.10.2.min.js')}}"></script>
		<![endif]-->
		<!--[if !IE]>-->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='{{asset('assets/site/a2zcms/js/jquery-2.0.3.min.js')}}'>" + "<" + "/script>");
		</script>
		<!--<![endif]-->
		<!--[if IE]>
		<script type="text/javascript">
		window.jQuery || document.write("<script src='{{asset('assets/site/a2zcms/js/jquery-1.10.2.min.js')}}'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script src="{{asset('assets/site/a2zcms/js/jquery-migrate-1.2.1.min.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/bootstrap.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/theme.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/jquery-ui-1.10.3.custom.min.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/jquery.validate.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/select2.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/jquery.multiselect.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/prettify.js')}}"></script>
		<script src="{{asset('assets/site/a2zcms/js/summernote.js')}}"></script>
		<!-- end: JavaScript-->
		
		@yield('scripts')
	</body>
</html>