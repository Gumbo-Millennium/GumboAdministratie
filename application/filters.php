<?php

return array(
	
	'before' => function()
	{
		// Doe iets voor elke request wordt uitgevoerd.
	},

	'after' => function($response)
	{
		// Doe iets nadat elke request is uitgevoerd.
	},

	'auth' => function()
	{
		return (! Auth::check()) ? Redirect::to_login() : null;
	},

	'csrf' => function()
	{
		return (Input::get('csrf_token') !== Form::raw_token()) ? Response::error('500') : null;
	},
			
);