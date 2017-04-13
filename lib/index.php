<?php

// Get this thing started
foreach ( glob( dirname( __FILE__ ) . '/*.php' ) as $file ) { include_once $file; }
foreach ( glob( dirname( __FILE__ ) . '/archives/*.php' ) as $file ) { include_once $file; }
foreach ( glob( dirname( __FILE__ ) . '/customize/*.php' ) as $file ) { include_once $file; }
foreach ( glob( dirname( __FILE__ ) . '/integrations/*.php' ) as $file ) { include_once $file; }
foreach ( glob( dirname( __FILE__ ) . '/layouts/*.php' ) as $file ) { include_once $file; }
foreach ( glob( dirname( __FILE__ ) . '/shortcodes/*.php' ) as $file ) { include_once $file; }