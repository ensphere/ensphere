<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Ensphere</title>
		<link rel="shortcut icon" href="/favicon.png">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		@include('ensphere.ensphere::css-loader')
		<style>
		html {
			height: 100%;
		}
		body {
			padding: 0;
			margin: 0;
			height: 100%;
			font-family: 'Open Sans', sans-serif;
			overflow: hidden;
		}
		.curve {
			background: #16A085;
			width: 200%;
			height: 100%;
			border-radius: 100%;
			transform: rotate(-29deg);
			position: relative;
			top: -50%;
			left: -50%;
			z-index: 1;
		}
		.curve:before {
			width: 200%;
			height: 300px;
			background: #16A085;
			content: " ";
			display: block;
		}
		.sphere {
			width: 200px;
			height: 200px;
			background: white;
			border-radius: 100%;
			position: absolute;
			top: 50%;
			left: 50%;
			z-index: 2;
			margin: -100px 0 0 -100px;
			border: 2px solid white;
			overflow: hidden;
		}
		.sphere:before {
			position: absolute;
			top: 0;
			left: 0;
			z-index: 3;
			content: " ";
			display: inline-block;
			width: 200px;
			height: 200px;
			background: #16A085;
			border-radius: 100%;
			margin: -80px 0 0 -80px;
		}
		.sphere:after {
			position: absolute;
			top: 0;
			left: 0;
			z-index: 2;
			content: " ";
			display: block;
			width: 200px;
			height: 200px;
			background: #1ABC9C;
			border-radius: 100%;
			margin: -30px 0 0 -30px;
		}
		.text {
			position: absolute;
			bottom: 0;
			right: 0;
			z-index: 5;
			padding: 20px;
		}
		h1 {
			color: #34495E;
			font-weight: 300;
			margin: 0 0 0 0;
			text-align: right;
		}
		p {
			font-size: 12px;
		}
		</style>
	</head>
	<body>
		<div class="curve"></div>
		<div class="sphere"></div>
		<div class="text">
			<h1>Ensphere</h1>
			<p>An alternative to the Laravel base application</p>
		</div>
		@include('ensphere.ensphere::js-loader')
	</body>
</html>
