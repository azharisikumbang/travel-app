<?php

if (false === session()->isAuthenticatedAs('admin')) html_unauthorized();
response()->redirectTo(site_url('admin/dashboard'));