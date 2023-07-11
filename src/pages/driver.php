<?php

if (false === session()->isAuthenticatedAs('driver')) html_unauthorized();
response()->redirectTo(site_url('driver/dashboard'));