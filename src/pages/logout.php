<?php

// @TODO: error header already sent
session_destroy();
response()->redirectTo(site_url());