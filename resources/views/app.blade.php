<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

	@yield('aimeos_header')

	<title>{{ config('app.name', 'Aimeos') }}</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/css/bootstrap.min.css">

	<style>
		/* Theme: Black&White */
		/* body {
			--ai-primary: #000; --ai-primary-light: #000; --ai-primary-alt: #fff;
			--ai-bg: #fff; --ai-bg-light: #fff; --ai-bg-alt: #000;
			--ai-secondary: #555; --ai-light: #D0D0D0;
		} */
		body { color: #000; color: var(--ai-primary, #000); background-color: #fff; background-color: var(--ai-bg, #fff); }
		.navbar { color: #555; color: var(--ai-primary-alt, #555) }
		.navbar a, .navbar a:before, .navbar span, footer a { color: #555 !important; color: var(--ai-primary-alt, #555) !important; }
		.content { margin: 0 5% } .catalog-stage-image { margin: 0 -5.55% }
		footer { color: #555; color: var(--ai-primary-alt, #555); background-color: #f8f8f8; background-color: var(--ai-bg-alt, #f8f8f8); }
		.sm { display: inline-block } .sm:before { font: normal normal normal 14px/1 FontAwesome; padding: 0 0.2em; font-size: 225% }
		.facebook:before { content: "\f082" } .twitter:before { content: "\f081" } .instagram:before { content: "\f16d" } .youtube:before { content: "\f167" }
	</style>
	<link type="text/css" rel="stylesheet" href="{{ asset(config( 'shop.client.html.common.template.baseurl', 'packages/aimeos/shop/themes/elegance' ) . '/aimeos.css') }}" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="aimeos">
	<nav class="navbar navbar-expand-md navbar-light">
		<a class="navbar-brand" href="/">
			<img src="http://aimeos.org/fileadmin/template/icons/logo.png" height="30" title="Aimeos Logo">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
			<ul class="navbar-nav">
				@if (Auth::guest() && config('app.shop_registration'))
					<li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
				@endif
				@if (Auth::guest())
					<li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
				@else
					<li class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							@if (config('app.shop_registration'))
								<li><a class="nav-link" href="{{ route('aimeos_shop_admin') }}" title="{{ __('Merchant') }}">{{ __('Merchant') }}</a></li>
							@endif
							<li><a class="nav-link" href="{{ route('aimeos_shop_account',['site'=>Route::current()->parameter('site','default'),'locale'=>Route::current()->parameter('locale','en'),'currency'=>Route::current()->parameter('currency','EUR')]) }}" title="{{ __('Profile') }}">{{ __('Profile') }}</a></li>
							<li><form id="logout" action="/logout" method="POST">{{csrf_field()}}</form><a class="nav-link" href="javascript: document.getElementById('logout').submit();">{{ __('Logout') }}</a></li>
						</ul>
					</li>
				@endif
			</ul>
			@yield('aimeos_head')
		</div>
	</nav>
	<div class="content">
		@yield('aimeos_stage')
		@yield('aimeos_nav')
		@yield('aimeos_body')
		@yield('aimeos_aside')
		@yield('content')
	</div>
	<footer class="mt-5 p-5">
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-sm-6 my-4"><h2>{{ __('LEGAL') }}</h2><p><a href="#">{{ __('Terms & Conditions') }}</a></p><p><a href="#">{{ __('Privacy Notice') }}</a></p><p><a href="#">{{ __('Imprint') }}</a></p></div>
					<div class="col-sm-6 my-4"><h2>{{ __('ABOUT US') }}</h2><p><a href="#">{{ __('Contact us') }}</a></p><p><a href="#">{{ __('Company') }}</a></p></div>
				</div>
			</div>
			<div class="col-md-4 my-4">
				<div class="social"><a href="#" class="sm facebook" title="Facebook" rel="noopener"></a><a href="#" class="sm twitter" title="Twitter" rel="noopener"></a><a href="#" class="sm instagram" title="Instagram" rel="noopener"></a><a href="#" class="sm youtube" title="Youtube" rel="noopener"></a></div>
				<a class="px-2 py-4 d-inline-block" href="/"><img src="http://aimeos.org/fileadmin/template/icons/logo.png" style="width: 160px" title="Aimeos Logo"></a>
			</div>
		</div>
	</footer>
	<!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/combine/npm/jquery@3,npm/bootstrap@4"></script>
	@yield('aimeos_scripts')
	</body>
</html>