<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php Loader::element('header_required'); ?>

	<!--[if lte IE 9]><link rel="stylesheet" href="<?php print $this->getThemePath(); ?>/css/ie.css" type="text/css" media="screen" /><![endif]-->

    <link rel="stylesheet" href="<?php print $this->getThemePath(); ?>/css/1140.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php print $this->getThemePath(); ?>/css/styles.css" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php print $this->getThemePath(); ?>/js/modernizr.js"></script>
</head>
<body id="<?php print $c->getCollectionHandle(); ?>" class="<?php print $c->getCollectionTypeHandle(); ?>">
	<header>
		<nav>

		</nav>
	</header>