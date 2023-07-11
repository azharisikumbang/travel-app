<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();

response()->redirectTo(site_url('pelanggan/dashboard'));