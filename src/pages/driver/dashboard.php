<?php

if (false === session()->isAuthenticatedAs('driver')) html_unauthorized();

echo "Selamat datang, " . session()->auth()->getUsername() . '.';