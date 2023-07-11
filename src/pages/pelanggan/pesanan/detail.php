<?php

if (false === session()->isAuthenticatedAs('pelanggan')) html_unauthorized();